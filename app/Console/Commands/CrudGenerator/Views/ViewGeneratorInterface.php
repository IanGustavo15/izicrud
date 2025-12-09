<?php

namespace App\Console\Commands\CrudGenerator\Views;

interface ViewGeneratorInterface
{
    public function generateIndex(array $config, array $fields): array;
    public function generateCreate(array $config, array $fields): array;
    public function generateComponents(array $config, array $fields): array;
}
