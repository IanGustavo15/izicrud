<?php

namespace App\Console\Commands\CrudGenerator\Generators;

class MigrationGenerator extends BaseGenerator
{
    public function generate(string $model, array $fields): array
    {
        // Placeholder - will move logic from MakeCrud
        return [
            'path' => database_path('migrations/'),
            'content' => "<?php\n\n// Migration placeholder for {$model}"
        ];
    }
}
