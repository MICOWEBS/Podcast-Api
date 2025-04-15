#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}Running tests for Podcast Platform API...${NC}"

# Check if running in Docker
if [ -f /.dockerenv ]; then
    echo -e "${YELLOW}Running tests in Docker environment...${NC}"
    php artisan test
else
    # Check if Docker is running
    if docker info > /dev/null 2>&1; then
        echo -e "${YELLOW}Running tests in Docker container...${NC}"
        docker-compose exec app php artisan test
    else
        echo -e "${YELLOW}Running tests locally...${NC}"
        php artisan test
    fi
fi

# Check if tests passed
if [ $? -eq 0 ]; then
    echo -e "${GREEN}All tests passed successfully!${NC}"
else
    echo -e "${RED}Some tests failed. Please check the output above.${NC}"
    exit 1
fi 