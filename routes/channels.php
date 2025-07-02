<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('{id}.user.notifications', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
