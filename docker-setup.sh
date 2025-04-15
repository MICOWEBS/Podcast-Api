#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Setting up Podcast Platform API with Docker...${NC}"

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file from .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}.env file created successfully!${NC}"
else
    echo -e "${YELLOW}.env file already exists.${NC}"
fi

# Build and start Docker containers
echo -e "${YELLOW}Building and starting Docker containers...${NC}"
docker-compose up -d --build

# Install dependencies
echo -e "${YELLOW}Installing dependencies...${NC}"
docker-compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key
echo -e "${YELLOW}Generating application key...${NC}"
docker-compose exec app php artisan key:generate

# Run migrations
echo -e "${YELLOW}Running database migrations...${NC}"
docker-compose exec app php artisan migrate

# Generate API documentation
echo -e "${YELLOW}Generating API documentation...${NC}"
docker-compose exec app php artisan l5-swagger:generate

# Set permissions
echo -e "${YELLOW}Setting proper permissions...${NC}"
docker-compose exec app chmod -R 775 storage bootstrap/cache

echo -e "${GREEN}Setup completed successfully!${NC}"
echo -e "${GREEN}The application is now running at:${NC}"
echo -e "${YELLOW}http://localhost:8000${NC}"
echo -e "${GREEN}API documentation is available at:${NC}"
echo -e "${YELLOW}http://localhost:8000/api/documentation${NC}"
echo -e "${GREEN}Mailpit is available at:${NC}"
echo -e "${YELLOW}http://localhost:8025${NC}" 