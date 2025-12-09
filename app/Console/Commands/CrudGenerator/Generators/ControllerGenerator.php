<?php

namespace App\Console\Commands\CrudGenerator\Generators;

class ControllerGenerator extends BaseGenerator
{
    public function generate(array $config, array $fields): array
    {
        // Placeholder - will move logic from MakeCrud
        $controller = $config['controller'] ?? 'TestController';
        return [
            'path' => app_path("Http/Controllers/{$controller}.php"),
            'content' => "<?php\n\n// Controller placeholder for {$controller}"
        ];
    }
}
