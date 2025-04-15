@echo off
echo Generating API documentation for Podcast Platform API...

REM Check if Docker is running
docker info > nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo Generating documentation in Docker container...
    docker-compose exec app php artisan l5-swagger:generate
) else (
    echo Generating documentation locally...
    php artisan l5-swagger:generate
)

REM Check if documentation generation was successful
if %ERRORLEVEL% EQU 0 (
    echo API documentation generated successfully!
    echo You can view the documentation at:
    echo http://localhost:8000/api/documentation
) else (
    echo Failed to generate API documentation. Please check the output above.
    exit /b 1
)

pause 