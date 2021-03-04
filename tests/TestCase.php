<?php

namespace Kielabokkie\UberCache\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function resolveApplicationCore($app): void
    {
        parent::resolveApplicationCore($app);
    }
}
