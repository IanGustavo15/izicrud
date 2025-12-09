<?php

namespace App\Console\Commands\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;

abstract class BaseGenerator
{
    protected function getStubContent(string $stubPath): string
    {
        return File::get(base_path("stubs/{$stubPath}"));
    }

    protected function writeFile(string $path, string $content): void
    {
        File::put($path, $content);
    }
}
