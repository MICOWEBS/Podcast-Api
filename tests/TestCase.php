<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear cache before each test
        $this->artisan('cache:clear');
        
        // Set up test environment
        config(['app.env' => 'testing']);
    }

    /**
     * Get the API route with the v1 prefix.
     *
     * @param string $route
     * @return string
     */
    protected function apiRoute(string $route): string
    {
        return '/api/v1/' . ltrim($route, '/');
    }
}
