@echo off
echo Setting up Podcast Platform API...

REM Check if .env file exists
if not exist .env (
    echo Creating .env file from .env.example...
    copy .env.example .env
    echo .env file created successfully!
) else (
    echo .env file already exists.
)

REM Install dependencies
echo Installing dependencies...
call composer install --no-interaction --prefer-dist --optimize-autoloader

REM Generate application key
echo Generating application key...
call php artisan key:generate

REM Run migrations
echo Running database migrations...
call php artisan migrate

REM Generate API documentation
echo Generating API documentation...
call php artisan l5-swagger:generate

REM Set permissions (Windows equivalent)
echo Setting proper permissions...
icacls storage /grant Everyone:(OI)(CI)F /T
icacls bootstrap/cache /grant Everyone:(OI)(CI)F /T

echo Setup completed successfully!
echo You can now start the application with:
echo php artisan serve
echo API documentation will be available at:
echo http://localhost:8000/api/documentation

pause 