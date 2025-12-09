<?php

namespace App\Console\Commands\CrudGenerator\Generators;

class ModelGenerator extends BaseGenerator
{
    public function generate(string $model, array $fields): array
    {
        // Placeholder - will move logic from MakeCrud
        return [
            'path' => app_path("Models/{$model}.php"),
            'content' => "<?php\n\n// Model placeholder for {$model}"
        ];
    }
}
