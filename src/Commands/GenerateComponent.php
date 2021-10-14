<?php

namespace essa\APIGenerator\Commands;

use Illuminate\Console\Command;
use essa\APIGenerator\Generator\Generator;

class GenerateComponent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {model}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generate api crud.';

    /**
     */
    public function handle()
    {
        (new Generator(ucfirst($this->argument('model'))))->process();

        $this->info('Module created successfully!');
    }
}

