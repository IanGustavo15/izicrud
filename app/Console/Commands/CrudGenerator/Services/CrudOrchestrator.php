<?php

namespace App\Console\Commands\CrudGenerator\Services;

use App\Console\Commands\CrudGenerator\Generators\ModelGenerator;
use App\Console\Commands\CrudGenerator\Generators\ControllerGenerator;
use App\Console\Commands\CrudGenerator\Generators\MigrationGenerator;
use App\Console\Commands\CrudGenerator\Generators\RouteGenerator;
use App\Console\Commands\CrudGenerator\Generators\MenuGenerator;
use App\Console\Commands\CrudGenerator\Views\ViewGeneratorFactory;
use App\Console\Commands\CrudGenerator\Helpers\FieldParser;

class CrudOrchestrator
{
    protected FieldParser $fieldParser;
    protected ModelGenerator $modelGenerator;
    protected ControllerGenerator $controllerGenerator;
    protected MigrationGenerator $migrationGenerator;
    protected RouteGenerator $routeGenerator;
    protected MenuGenerator $menuGenerator;
    protected ViewGeneratorFactory $viewFactory;

    public function __construct()
    {
        $this->fieldParser = new FieldParser();
        $this->modelGenerator = new ModelGenerator();
        $this->controllerGenerator = new ControllerGenerator();
        $this->migrationGenerator = new MigrationGenerator();
        $this->routeGenerator = new RouteGenerator();
        $this->menuGenerator = new MenuGenerator();
        $this->viewFactory = new ViewGeneratorFactory();
    }

    public function generate(array $config): array
    {
        // Parse and validate fields
        $fields = $this->fieldParser->parse($config['fields']);

        // Create view generator based on type
        $viewGenerator = $this->viewFactory::create($config['componentized'] ?? false);

        $result = [
            'model' => $this->modelGenerator->generate($config['model'], $fields),
            'controller' => $this->controllerGenerator->generate($config, $fields),
            'migration' => $this->migrationGenerator->generate($config['model'], $fields),
            'routes' => $this->routeGenerator->generate($config),
            'menu' => $this->menuGenerator->generate($config),
            'views' => [
                'index' => $viewGenerator->generateIndex($config, $fields),
                'create' => $viewGenerator->generateCreate($config, $fields),
                'components' => $viewGenerator->generateComponents($config, $fields)
            ]
        ];

        return $result;
    }
}
