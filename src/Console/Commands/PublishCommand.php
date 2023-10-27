<?php

namespace Shooorov\Generator\Console\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    protected $signature = 'generator:publish';
    protected $description = 'Publish generator assets';

    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'generator-resources',
            '--force' => true,
        ]);
    }
}
