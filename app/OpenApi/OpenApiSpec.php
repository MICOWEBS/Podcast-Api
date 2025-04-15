<?php

namespace App\OpenApi;

use GoldSpecDigital\ObjectOrientedOAS\OpenApi;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Info;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Server;

class OpenApiSpec
{
    /**
     * @return OpenApi
     */
    public static function generate(): OpenApi
    {
        return OpenApi::create()
            ->info(
                Info::create()
                    ->title('Podcast Platform API')
                    ->description('API documentation for the Podcast Platform')
                    ->version('1.0.0')
            )
            ->servers([
                Server::create()->url(config('app.url')),
            ])
            ->security(SecuritySchemes::security())
            ->components(SecuritySchemes::securitySchemes());
    }
} 