#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Setting up Podcast Platform API...${NC}"

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file from .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}.env file created successfully!${NC}"
else
    echo -e "${YELLOW}.env file already exists.${NC}"
fi

# Install dependencies
echo -e "${YELLOW}Installing dependencies...${NC}"
composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key
echo -e "${YELLOW}Generating application key...${NC}"
php artisan key:generate

# Run migrations
echo -e "${YELLOW}Running database migrations...${NC}"
php artisan migrate

# Generate API documentation
echo -e "${YELLOW}Generating API documentation...${NC}"
php artisan l5-swagger:generate

# Set permissions
echo -e "${YELLOW}Setting proper permissions...${NC}"
chmod -R 775 storage bootstrap/cache

echo -e "${GREEN}Setup completed successfully!${NC}"
echo -e "${GREEN}You can now start the application with:${NC}"
echo -e "${YELLOW}php artisan serve${NC}"
echo -e "${GREEN}API documentation will be available at:${NC}"
echo -e "${YELLOW}http://localhost:8000/api/documentation${NC}" 