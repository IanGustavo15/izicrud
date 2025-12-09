<?php

namespace App\Console\Commands\CrudGenerator\Views;

class ViewGeneratorFactory
{
    public static function create(bool $componentized = false): ViewGeneratorInterface
    {
        return $componentized
            ? new ComponentizedViewGenerator()
            : new StandardViewGenerator();
    }
}
