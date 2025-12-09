<?php

namespace App\Console\Commands\CrudGenerator\Generators;

class RouteGenerator extends BaseGenerator
{
    public function generate(array $config): array
    {
        // Placeholder - will move logic from MakeCrud
        $routePrefix = $config['routePrefix'] ?? 'test';
        return [
            'file' => 'web.php',
            'content' => "// Routes for {$routePrefix}"
        ];
    }
}
