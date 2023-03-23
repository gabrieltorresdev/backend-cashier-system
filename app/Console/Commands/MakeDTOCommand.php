<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeDTOCommand extends Command
{
    protected $signature = 'make:dto {name}';
    protected $description = 'Create a new DTO.';

    public function handle()
    {
        $name = $this->argument('name');
        $parts = explode('/', $name);
        $class = array_pop($parts);
        $directory = count($parts) > 0 ? implode('/', $parts) . '/' : '';
        $path = app_path("DTO/{$directory}{$class}.php");
        $namespace = 'App\DTO\\' . str_replace('/', '\\', $directory) . $class;

        $stub = $this->getStub();
        $stub = str_replace('{{class}}', $class, $stub);
        $stub = str_replace('{{namespace}}', $namespace, $stub);

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $stub);

        $this->info("DTO created successfully in app/DTO/{$directory}{$class}.php");
    }

    protected function getStub()
    {
        return file_get_contents(__DIR__ . '/stubs/dto.stub');
    }
}
