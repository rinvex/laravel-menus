<?php

declare(strict_types=1);

namespace Rinvex\Menus\Tests\Unit;

use Rinvex\Menus\Models\MenuManager;
use Rinvex\Menus\Models\MenuGenerator;

class MenuTest extends BaseTestCase
{
    /**
     * @var \Rinvex\Menus\Models\MenuManager
     */
    protected $menuManager;

    protected function setUp()
    {
        parent::setUp();

        $this->menuManager = app(MenuManager::class);
    }

    /** @test */
    public function it_can_get_the_instance_of_a_menu()
    {
        $this->menuManager->register('test', function (MenuGenerator $menu) {
        });

        $this->assertInstanceOf(MenuGenerator::class, $this->menuManager->instance('test'));
    }

    /** @test */
    public function it_can_get_all_menus()
    {
        $this->menuManager->register('main', function (MenuGenerator $menu) {
        });
        $this->menuManager->register('footer', function (MenuGenerator $menu) {
        });

        $this->assertCount(2, $this->menuManager->all());
    }

    /** @test */
    public function it_can_count_menus()
    {
        $this->menuManager->register('main', function (MenuGenerator $menu) {
        });
        $this->menuManager->register('footer', function (MenuGenerator $menu) {
        });

        $this->assertEquals(2, $this->menuManager->count());
    }

    /** @test */
    public function it_can_destroy_all_menus()
    {
        $this->menuManager->register('main', function (MenuGenerator $menu) {
        });
        $this->menuManager->register('footer', function (MenuGenerator $menu) {
        });

        $this->assertCount(2, $this->menuManager->all());
        $this->menuManager->destroy();
        $this->assertCount(0, $this->menuManager->all());
    }
}
