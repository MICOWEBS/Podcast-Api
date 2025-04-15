@echo off
echo Setting up Podcast Platform API with Docker...

REM Check if .env file exists
if not exist .env (
    echo Creating .env file from .env.example...
    copy .env.example .env
    echo .env file created successfully!
) else (
    echo .env file already exists.
)

REM Build and start Docker containers
echo Building and starting Docker containers...
docker-compose up -d --build

REM Install dependencies
echo Installing dependencies...
docker-compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader

REM Generate application key
echo Generating application key...
docker-compose exec app php artisan key:generate

REM Run migrations
echo Running database migrations...
docker-compose exec app php artisan migrate

REM Generate API documentation
echo Generating API documentation...
docker-compose exec app php artisan l5-swagger:generate

REM Set permissions (Windows equivalent)
echo Setting proper permissions...
docker-compose exec app chmod -R 775 storage bootstrap/cache

echo Setup completed successfully!
echo The application is now running at:
echo http://localhost:8000
echo API documentation is available at:
echo http://localhost:8000/api/documentation
echo Mailpit is available at:
echo http://localhost:8025

pause 