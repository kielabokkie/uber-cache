<?php

namespace Kielabokkie\UberCache\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function resolveApplicationCore($app)
    {
        parent::resolveApplicationCore($app);

        $app->detectEnvironment(function () {
            return 'self-testing';
        });
    }

//    protected function getEnvironmentSetUp($app)
//    {
//        $config = $app->get('config');
//
//        // Setup default database to use in-memory sqlite
//        $config->set('database.default', 'testbench');
//        $config->set('database.connections.testbench', [
//            'driver' => 'sqlite',
//            'database' => ':memory:',
//            'prefix' => '',
//        ]);
//    }
}
