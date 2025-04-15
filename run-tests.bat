@echo off
echo Running tests for Podcast Platform API...

REM Check if Docker is running
docker info > nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo Running tests in Docker container...
    docker-compose exec app php artisan test
) else (
    echo Running tests locally...
    php artisan test
)

REM Check if tests passed
if %ERRORLEVEL% EQU 0 (
    echo All tests passed successfully!
) else (
    echo Some tests failed. Please check the output above.
    exit /b 1
)

pause 