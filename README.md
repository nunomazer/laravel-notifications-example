# Notification Dashboard Application

## Introduction

This Laravel application is built as a technical assessment to demonstrate advanced knowledge in specific areas while 
intentionally simplifying certain common implementations to focus on the more complex and interesting parts of the system.

## Development Environment

This project uses **Laradock** as the Docker development environment, which is why no custom Dockerfile is included in this repository. Laradock provides a comprehensive Docker environment for Laravel applications with pre-configured services like MySQL, Redis, Nginx, and PHP-FPM.

### Design Decisions & Missing Best Practices

1. **No Separate CSS Files**: All styles are embedded inline to keep the focus on the backend logic and real-time 
    functionality rather than asset management and build processes. In production, I would implement proper CSS 
    organization with tools like Sass/SCSS, CSS modules, or Tailwind CSS.

2. **No Authentication System**: A custom auto-authentication middleware is used to simulate user sessions without 
    implementing the full Laravel authentication scaffolding. This allows focusing on the notification system rather 
   than user management. In production, I would implement Laravel Sanctum, Passport, or Fortify depending on requirements.

3. **No Sanctum Security**: API endpoints use simplified authentication to focus on the notification logic and real-time 
   features. In production environments, I would implement proper API authentication with Laravel Sanctum, including 
   token management, rate limiting, and CORS configuration.

**Technical Architecture Decisions:**

- **Repository Pattern**: I've chosen to use Eloquent ORM directly in services for most applications as it provides 
   excellent readability and Laravel integration. However, I've implemented a simple repository pattern example to 
  demonstrate knowledge of abstraction layers. In more complex systems, I would extend this with DTOs (Data Transfer Objects) 
   or Entities for better layer communication and testability.
   
- **On Mixing ORM with Repository Pattern**: Contrary to some purist opinions, In understand that mixing ORM with 
  Repository pattern is not  
   inherently a bad choice. It's a pragmatic approach that leverages Laravel's strengths while maintaining testability and 
   abstraction when needed. In this application, I've implemented repositories that use Eloquent ORM to demonstrate this 
   balanced approach - it provides the benefits of abstraction without completely abandoning Laravel's powerful ORM features.

### API Documentation     

API documentation is available in the `docs` folder, including collections for Bruno, Postman, and the OpenAPI specification. 

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Environment Configuration

Copy the environment file and configure it:

```bash
cp .env.example .env
```

### 4. Configure Environment Variables

Edit the `.env` file with your database and application settings:

```dotenv
# Application Settings
APP_NAME="Notification Dashboard"
APP_ENV=local
APP_KEY=base64:your-generated-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Localization
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=notification_dashboard
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache Configuration
CACHE_STORE=database
# For better performance, use Redis:
# CACHE_STORE=redis

# Queue Configuration
QUEUE_CONNECTION=database
# For production, use Redis or SQS:
# QUEUE_CONNECTION=redis

# Broadcasting Configuration (Laravel Reverb)
BROADCAST_CONNECTION=reverb

# Laravel Reverb Settings
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite Environment Variables
VITE_APP_NAME="${APP_NAME}"
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Mail Configuration (for notifications via email)
MAIL_MAILER=log
# For production, configure your mail service:
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=your_email@gmail.com
# MAIL_PASSWORD=your_app_password
# MAIL_FROM_ADDRESS=your_email@gmail.com
# MAIL_FROM_NAME="${APP_NAME}"

# Redis Configuration (if using Redis for cache/queue)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Logging
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Database Setup

```bash
# Run database migrations
php artisan migrate

# Seed the database with sample data (users and notifications)
php artisan db:seed
```

### 7. Build Frontend Assets

```bash
# Build assets for development
npm run dev

# Or build for production
npm run build
```

## Running the Application

### 1. Start the Laravel Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### 2. Start Laravel Reverb (WebSocket Server)

In a separate terminal:

```bash
php artisan reverb:start
```

This starts the WebSocket server for real-time notifications.

### 3. Start the Queue Worker (Optional)

If you plan to use queued jobs for notifications:

```bash
php artisan queue:work
```

## Architecture Overview

### Key Components

1. **DashboardService**: Handles all dashboard-related business logic and statistics
2. **NotificationCacheService**: Manages intelligent caching with automatic invalidation
3. **Livewire Components**: Provides real-time UI updates without page refreshes
4. **WebSocket Integration**: Real-time notifications using Laravel Reverb

### Database Structure

- **notifications**: Main notification storage
- **users**: User management (simplified)
- **sessions**: Session management
- **cache**: Database cache storage

### Caching Strategy

The application implements a sophisticated caching layer:

- Dashboard statistics cached for 10 minutes
- Trend data cached for 5 minutes
- Automatic cache invalidation when data changes
- User-specific cache keys

## API Endpoints

### Notifications
- `GET /api/notifications` - List notifications with pagination
- `POST /api/notifications` - Create new notification
- `PUT /api/notifications/{id}/mark-read` - Mark notification as read

### Users
- `GET /api/users/me` - Get current user information
- `GET /api/users/{id}/notifications/latest` - Get latest notifications for user

## Development

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

