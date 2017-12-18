<?php

declare(strict_types=1);

namespace Rinvex\Menus\Tests;

use Collective\Html\HtmlServiceProvider;
use Rinvex\Menus\Providers\MenusServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class BaseTestCase extends OrchestraTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            HtmlServiceProvider::class,
            MenusServiceProvider::class,
        ];
    }
}
