<?php

namespace App\OpenApi;

use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityScheme;
use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement;

class SecuritySchemes
{
    /**
     * @return array
     */
    public static function securitySchemes(): array
    {
        return [
            'bearerAuth' => SecurityScheme::create('bearerAuth')
                ->type('http')
                ->scheme('bearer')
                ->bearerFormat('JWT')
                ->description('Use your API token as: Bearer <token>'),
        ];
    }

    /**
     * @return array
     */
    public static function security(): array
    {
        return [
            SecurityRequirement::create('bearerAuth'),
        ];
    }
} 