<?php

namespace App\Console\Commands\CrudGenerator\Views;

use App\Console\Commands\CrudGenerator\Generators\BaseGenerator;

class StandardViewGenerator extends BaseGenerator implements ViewGeneratorInterface
{
    public function generateIndex(array $config, array $fields): array
    {
        // Placeholder - will move logic from createViews in MakeCrud
        return [
            'path' => resource_path("js/pages/{$config['model']}/index.vue"),
            'content' => "<!-- Standard Index view placeholder -->"
        ];
    }

    public function generateCreate(array $config, array $fields): array
    {
        // Placeholder - will move logic from createViews in MakeCrud
        return [
            'path' => resource_path("js/pages/{$config['model']}/create.vue"),
            'content' => "<!-- Standard Create view placeholder -->"
        ];
    }

    public function generateComponents(array $config, array $fields): array
    {
        // Standard views don't generate components
        return [];
    }
}

class ComponentizedViewGenerator extends BaseGenerator implements ViewGeneratorInterface
{
    public function generateIndex(array $config, array $fields): array
    {
        // Placeholder - will move logic from createComponentizedViews in MakeCrud
        return [
            'path' => resource_path("js/pages/{$config['model']}/index.vue"),
            'content' => "<!-- Componentized Index view placeholder -->"
        ];
    }

    public function generateCreate(array $config, array $fields): array
    {
        // Placeholder - will move logic from createComponentizedViews in MakeCrud
        return [
            'path' => resource_path("js/pages/{$config['model']}/create.vue"),
            'content' => "<!-- Componentized Create view placeholder -->"
        ];
    }

    public function generateComponents(array $config, array $fields): array
    {
        // Placeholder - will move component generation logic
        return [
            'form' => [
                'path' => resource_path("js/components/forms/{$config['model']}Form.vue"),
                'content' => "<!-- Form component placeholder -->"
            ],
            'relationship' => [] // Relationship components if any
        ];
    }
}
