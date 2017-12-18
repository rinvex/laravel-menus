<?php

declare(strict_types=1);

namespace Rinvex\Menus\Tests;

use Rinvex\Menus\Models\MenuItem;
use Rinvex\Menus\Factories\MenuFactory;

class MenuBuilderTest extends BaseTestCase
{
    /** @test */
    public function it_makes_a_menu_item()
    {
        $builder = new MenuFactory();

        self::assertInstanceOf(MenuItem::class, $builder->url('hello', 'world'));
    }
}
