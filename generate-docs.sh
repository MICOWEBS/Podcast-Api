#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Generating API documentation for Podcast Platform API...${NC}"

# Check if running in Docker
if [ -f /.dockerenv ]; then
    echo -e "${YELLOW}Generating documentation in Docker environment...${NC}"
    php artisan l5-swagger:generate
else
    # Check if Docker is running
    if docker info > /dev/null 2>&1; then
        echo -e "${YELLOW}Generating documentation in Docker container...${NC}"
        docker-compose exec app php artisan l5-swagger:generate
    else
        echo -e "${YELLOW}Generating documentation locally...${NC}"
        php artisan l5-swagger:generate
    fi
fi

# Check if documentation generation was successful
if [ $? -eq 0 ]; then
    echo -e "${GREEN}API documentation generated successfully!${NC}"
    echo -e "${GREEN}You can view the documentation at:${NC}"
    echo -e "${YELLOW}http://localhost:8000/api/documentation${NC}"
else
    echo -e "${RED}Failed to generate API documentation. Please check the output above.${NC}"
    exit 1
fi 