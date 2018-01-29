<?php

declare(strict_types=1);

namespace Rinvex\Menus\Tests;

use Rinvex\Menus\Models\MenuItem;
use Rinvex\Menus\Models\MenuGenerator;

class MenuBuilderTest extends BaseTestCase
{
    /** @test */
    public function it_makes_a_menu_item()
    {
        $builder = new MenuGenerator();

        self::assertInstanceOf(MenuItem::class, $builder->url('hello', 'world'));
    }
}
