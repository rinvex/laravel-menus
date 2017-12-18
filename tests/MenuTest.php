<?php

declare(strict_types=1);

namespace Rinvex\Menus\Tests;

use Rinvex\Menus\Models\Menu;
use Rinvex\Menus\Factories\MenuFactory;

class MenuTest extends BaseTestCase
{
    /**
     * @var Menu
     */
    private $menu;

    public function setUp()
    {
        parent::setUp();

        $this->menu = app(Menu::class);
    }

    /** @test */
    public function it_generates_an_empty_menu()
    {
        $this->menu->make('test', function (MenuFactory $menu) {});

        $expected = '<ul class="nav navbar-nav"></ul>';

        self::assertEquals($expected, $this->menu->render('test'));
    }

    /** @test */
    public function it_can_get_the_instance_of_a_menu()
    {
        $this->menu->make('test', function (MenuFactory $menu) {});

        $this->assertInstanceOf(MenuFactory::class, $this->menu->instance('test'));
    }

    /** @test */
    public function it_can_modify_a_menu_instance()
    {
        $this->menu->make('test', function (MenuFactory $menu) {});

        $this->menu->modify('test', function (MenuFactory $builder) {
            $builder->url('hello', 'world');
        });

        $this->assertCount(1, $this->menu->instance('test'));
    }

    /** @test */
    public function it_can_get_all_menus()
    {
        $this->menu->make('main', function (MenuFactory $menu) {});
        $this->menu->make('footer', function (MenuFactory $menu) {});

        $this->assertCount(2, $this->menu->all());
    }

    /** @test */
    public function it_can_count_menus()
    {
        $this->menu->make('main', function (MenuFactory $menu) {});
        $this->menu->make('footer', function (MenuFactory $menu) {});

        $this->assertEquals(2, $this->menu->count());
    }

    /** @test */
    public function it_can_destroy_all_menus()
    {
        $this->menu->make('main', function (MenuFactory $menu) {});
        $this->menu->make('footer', function (MenuFactory $menu) {});

        $this->assertCount(2, $this->menu->all());
        $this->menu->destroy();
        $this->assertCount(0, $this->menu->all());
    }
}
