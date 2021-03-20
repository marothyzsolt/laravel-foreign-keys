<?php

namespace MarothyZsolt\LaravelForeignKeys\Tests;

use MarothyZsolt\LaravelForeignKeys\LaravelForeignKeysServiceProvider;
use Orchestra\Testbench\TestCase as OriginalTestCase;

class TestCase extends OriginalTestCase
{
    protected function getPackageProviders($app): iterable
    {
        return [LaravelForeignKeysServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
