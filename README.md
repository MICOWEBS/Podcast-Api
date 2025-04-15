# Podcast Platform API

A comprehensive RESTful API for managing podcasts, episodes, and categories. Built with Laravel 10, this API provides endpoints for user authentication, podcast management, and content discovery.

## Table of Contents

- [Features](#features)
- [Architecture](#architecture)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [Development](#development)
- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## Features

### Authentication
- User registration and login
- Token-based authentication with Laravel Sanctum
- Password reset functionality
- Rate limiting for API endpoints

### Podcast Management
- Create, read, update, and delete podcasts
- Featured content filtering
- Category-based organization
- Search functionality
- Image upload and management

### Episode Management
- Create, read, update, and delete episodes
- Audio file management
- Episode metadata (duration, publish date, etc.)
- Season and episode numbering

### Category Management
- Create, read, update, and delete categories
- Podcast categorization
- Category-based filtering

### Additional Features
- Pagination for list endpoints
- Sorting and filtering options
- Caching with Redis
- Comprehensive error handling
- API rate limiting
- Swagger/OpenAPI documentation

## Architecture

### System Architecture

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│  Client         │────▶│  API Gateway    │────▶│  Laravel API    │
│  (Frontend)     │◀────│  (Nginx)        │◀────│  (Laravel 10)   │
│                 │     │                 │     │                 │
└─────────────────┘     └─────────────────┘     └────────┬────────┘
                                                         │
                                                         ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│  Redis Cache    │◀───▶│  MySQL Database │◀───▶│  Mail Service   │
│                 │     │                 │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

### Application Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────────────┐  │
│  │             │    │             │    │                     │  │
│  │ Controllers │───▶│  Services   │───▶│  Repositories       │  │
│  │             │    │             │    │                     │  │
│  └─────────────┘    └─────────────┘    └─────────────────────┘  │
│         │                   │                    │               │
│         │                   │                    │               │
│         ▼                   ▼                    ▼               │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────────────┐  │
│  │             │    │             │    │                     │  │
│  │  Requests   │    │   Models    │───▶│  Database           │  │
│  │             │    │             │    │                     │  │
│  └─────────────┘    └─────────────┘    └─────────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Database Schema

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│             │       │             │       │             │
│  Categories │       │  Podcasts   │       │  Episodes   │
│             │       │             │       │             │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id          │       │ id          │       │ id          │
│ name        │       │ title       │       │ title       │
│ slug        │       │ description │       │ description │
│ created_at  │       │ image_url   │       │ audio_url   │
│ updated_at  │       │ category_id │──┐    │ duration    │
└─────────────┘       │ created_at  │  │    │ podcast_id  │◀─┘
                      │ updated_at  │  │    │ created_at  │
                      └─────────────┘  │    │ updated_at  │
                                      │    └─────────────┘
                                      └─────┐
                                            │
                                            │
```

## Tech Stack

- **Backend Framework**: Laravel 10
- **PHP Version**: 8.2+
- **Database**: MySQL 8.0
- **Caching**: Redis
- **Authentication**: Laravel Sanctum
- **API Documentation**: Swagger/OpenAPI
- **Containerization**: Docker & Docker Compose
- **Email Testing**: Mailpit
- **Development Tools**: 
  - Composer
  - Git
  - Postman (for API testing)

## Prerequisites

Before you begin, ensure you have the following installed:

- [Docker](https://www.docker.com/get-started) (20.10+)
- [Docker Compose](https://docs.docker.com/compose/install/) (2.0+)
- [Git](https://git-scm.com/downloads) (2.30+)
- [Composer](https://getcomposer.org/download/) (2.0+)

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/podcast-platform-api.git
cd podcast-platform-api
```

### Step 2: Environment Setup

```bash
cp .env.example .env
```

Edit the `.env` file with your configuration:

```
APP_NAME="Podcast Platform"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=podcast_platform
DB_USERNAME=root
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 3: Start Docker Containers

```bash
docker-compose up -d
```

This will start the following services:
- Laravel application (PHP 8.2)
- MySQL database (8.0)
- Redis for caching
- Mailpit for email testing

### Step 4: Install Dependencies

```bash
docker-compose exec app composer install
```

### Step 5: Generate Application Key

```bash
docker-compose exec app php artisan key:generate
```

### Step 6: Run Migrations

```bash
docker-compose exec app php artisan migrate
```

### Step 7: Generate API Documentation

```bash
docker-compose exec app php artisan l5-swagger:generate
```

## Configuration

### Docker Services

The application uses the following Docker services:

1. **app**: Laravel application
   - PHP 8.2 with FPM
   - Nginx web server
   - Composer for dependency management

2. **db**: MySQL database
   - MySQL 8.0
   - Persistent volume for data storage

3. **redis**: Redis cache
   - Redis 6.0
   - Used for caching and rate limiting

4. **mailpit**: Email testing service
   - SMTP server for development
   - Web interface for viewing emails

### Environment Variables

Key environment variables to configure:

- `APP_NAME`: Application name
- `APP_ENV`: Environment (local, production)
- `APP_DEBUG`: Debug mode
- `APP_URL`: Application URL
- `FRONTEND_URL`: Frontend application URL
- `DB_*`: Database configuration
- `REDIS_*`: Redis configuration
- `MAIL_*`: Mail configuration

## API Documentation

The API documentation is available at `/api/documentation` when the application is running.

### Authentication Endpoints

- `POST /api/auth/register`: Register a new user
- `POST /api/auth/login`: Login user
- `POST /api/auth/logout`: Logout user
- `POST /api/auth/forgot-password`: Request password reset
- `POST /api/auth/reset-password`: Reset password

### Podcast Endpoints

- `GET /api/podcasts`: List all podcasts
- `GET /api/podcasts/{id}`: Get podcast details
- `GET /api/podcasts/{id}/episodes`: Get podcast episodes

### Category Endpoints

- `GET /api/categories`: List all categories

### Episode Endpoints

- `GET /api/episodes/{id}`: Get episode details

## Development

### Running the Application

```bash
docker-compose up -d
```

The application will be available at:
- API: http://localhost:8000
- Swagger Documentation: http://localhost:8000/api/documentation
- Mailpit: http://localhost:8025

### Useful Commands

```bash
# Run artisan commands
docker-compose exec app php artisan <command>

# Run composer commands
docker-compose exec app composer <command>

# Access MySQL
docker-compose exec db mysql -u root -p

# View logs
docker-compose logs -f

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Code Style

The project follows PSR-12 coding standards. You can check your code with:

```bash
docker-compose exec app ./vendor/bin/phpcs
```

## Testing

### Running Tests

```bash
docker-compose exec app php artisan test
```

### API Testing with Postman

1. Import the Postman collection from `postman/Podcast-Platform-API.postman_collection.json`
2. Set up environment variables:
   - `base_url`: http://localhost:8000
   - `token`: (will be set after login)
   - `reset_token`: (will be set after requesting password reset)

## Deployment

### Production Deployment

1. Update the `.env` file with production settings:
   ```
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Build and start the containers:
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

3. Run migrations:
   ```bash
   docker-compose exec app php artisan migrate --force
   ```

### Scaling

The application can be scaled horizontally by:
1. Using a load balancer
2. Setting up multiple application containers
3. Using a managed Redis service
4. Using a managed MySQL service

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
