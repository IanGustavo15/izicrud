<?php

namespace App\Console\Commands\CrudGenerator\Generators;

class MenuGenerator extends BaseGenerator
{
    public function generate(array $config): array
    {
        // Placeholder - will move logic from MakeCrud
        $modelPluralTitle = $config['modelPluralTitle'] ?? 'Items';
        return [
            'file' => 'AppSidebar.vue',
            'content' => "// Menu item for {$modelPluralTitle}"
        ];
    }
}
