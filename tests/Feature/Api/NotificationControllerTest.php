<?php

namespace Tests\Feature\Api;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar usuário para os testes
        $this->user = User::factory()->create();

        // Simular autenticação automática
        $this->actingAs($this->user);
    }

    public function test_index_returns_paginated_notifications()
    {
        Notification::factory()->count(15)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/notifications?page=1&per_page=10');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'message',
                        'type',
                        'read_at',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page'
                ]
            ])
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonCount(10, 'data');
    }

    public function test_index_filters_by_type()
    {
        Notification::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'type' => 'info'
        ]);

        Notification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'type' => 'warning'
        ]);

        $response = $this->getJson('/api/notifications?type=info');

        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(5, count($data));

        foreach ($data as $notification) {
            $this->assertEquals('info', $notification['type']);
        }
    }

    public function test_index_filters_by_read_status()
    {
        // Arrange
        Notification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'read_at' => now()
        ]);

        Notification::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        // Act - Filtrar apenas lidas
        $response = $this->getJson('/api/notifications?read_status=read');

        // Assert
        $response->assertOk()
            ->assertJsonCount(3, 'data');

        foreach ($response->json('data') as $notification) {
            $this->assertNotNull($notification['read_at']);
        }

        // Act - Filtrar apenas não lidas
        $response = $this->getJson('/api/notifications?read_status=unread');

        // Assert
        $response->assertOk()
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $notification) {
            $this->assertNull($notification['read_at']);
        }
    }

    public function test_store_creates_notification_successfully()
    {
        Event::fake();

        $notificationData = [
            'title' => 'Test Notification',
            'message' => 'This is a test notification',
            'type' => 'info',
            'user_id' => $this->user->id
        ];

        $response = $this->postJson('/api/notifications', $notificationData);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'message',
                    'type',
                    'read_at',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonPath('data.title', $notificationData['title'])
            ->assertJsonPath('data.message', $notificationData['message'])
            ->assertJsonPath('data.type', $notificationData['type']);

        $this->assertDatabaseHas('notifications', $notificationData);
    }

    public function test_store_validates_required_fields()
    {
        // Act
        $response = $this->postJson('/api/notifications', []);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'message', 'type', 'user_id']);
    }

    public function test_store_validates_field_types()
    {
        // Arrange
        $invalidData = [
            'title' => 123, // deve ser string
            'message' => [], // deve ser string
            'type' => 'invalid_type', // deve ser um tipo válido
            'user_id' => 'not_a_number' // deve ser número
        ];

        // Act
        $response = $this->postJson('/api/notifications', $invalidData);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'message', 'type', 'user_id']);
    }

    public function test_store_validates_user_exists()
    {
        // Arrange
        $nonExistentUserId = 99999;
        $notificationData = [
            'title' => 'Test Notification',
            'message' => 'This is a test notification',
            'type' => 'info',
            'user_id' => $nonExistentUserId
        ];

        // Act
        $response = $this->postJson('/api/notifications', $notificationData);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);
    }

    public function test_mark_as_read_updates_notification()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        $response = $this->putJson("/api/notifications/{$notification->id}/mark-read");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'message',
                    'type',
                    'read_at',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonPath('data.id', $notification->id);

        $this->assertNotNull($response->json('data.read_at'));

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_mark_as_read_fails_for_nonexistent_notification()
    {
        $response = $this->putJson('/api/notifications/99999/mark-read');

        $response->assertNotFound();
    }

    public function test_get_latest_by_user_returns_recent_notifications()
    {
        $targetUser = User::factory()->create();

        Notification::factory()->count(5)->create([
            'user_id' => $targetUser->id,
            'created_at' => now()->subDays(10)
        ]);

        Notification::factory()->count(3)->create([
            'user_id' => $targetUser->id,
            'created_at' => now()
        ]);

        $response = $this->getJson("/api/users/{$targetUser->id}/notifications/latest");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'message',
                        'type',
                        'read_at',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        $notifications = $response->json();
        $this->assertLessThanOrEqual(10, count($notifications));
    }

    public function test_response_includes_correct_headers()
    {
        // Act
        $response = $this->getJson('/api/notifications');

        // Assert
        $response->assertHeader('Content-Type', 'application/json')
            ->assertOk();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}