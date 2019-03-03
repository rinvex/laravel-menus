<?php

declare(strict_types=1);

namespace Rinvex\Menus\Tests\Unit;

use Rinvex\Menus\Models\MenuItem;
use Rinvex\Menus\Models\MenuManager;

class MenuItemTest extends BaseTestCase
{
    /**
     * @var \Rinvex\Menus\Models\MenuManager
     */
    protected $menuManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->menuManager = app(MenuManager::class);
    }

    /** @test */
    public function it_can_make_an_empty_menu_item()
    {
        $menuItem = new MenuItem([]);

        $this->assertInstanceOf(MenuItem::class, $menuItem);
    }

    /** @test */
    public function it_can_set_properties_on_menu_item()
    {
        $properties = [
            'url' => 'my.url',
            'route' => 'my.route',
            'title' => 'My Menu item',
            'type' => 'my-menu-item',
            'icon' => 'fa fa-user',
            'attributes' => [],
            'order' => 1,
        ];
        $menuItem = new MenuItem($properties);

        $this->assertEquals($properties, $menuItem->properties);
    }

    /** @test */
    public function it_can_fill_a_menu_item_with_allowed_properties()
    {
        $properties = [
            'url' => 'my.url',
            'route' => 'my.route',
            'title' => 'My Menu item',
            'type' => 'my-menu-item',
            'icon' => 'fa fa-user',
            'attributes' => [],
            'active' => false,
            'order' => 1,
        ];
        $menuItem = new MenuItem($properties);

        $this->assertEquals('my.url', $menuItem->url);
        $this->assertEquals('my.route', $menuItem->route);
        $this->assertEquals('My Menu item', $menuItem->title);
        $this->assertEquals('my-menu-item', $menuItem->type);
        $this->assertEquals('fa fa-user', $menuItem->icon);
        $this->assertSame([], $menuItem->attributes);
        $this->assertSame(1, $menuItem->order);
    }

    /** @test */
    public function it_can_set_icon_via_attributes()
    {
        $menuItem = new MenuItem(['icon' => 'fa fa-user']);

        $this->assertEquals('fa fa-user', $menuItem->icon);
    }

    /** @test */
    public function it_can_add_a_child_menu_item()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->url('test', 'Child Item');

        $this->assertCount(1, $menuItem->getChilds());
    }

    /** @test */
    public function it_can_get_ordered_children()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->url('test', 'Child Item', 10);
        $menuItem->url('test', 'First Child Item', 1);

        $children = $menuItem->getChilds();
        $this->assertEquals('First Child Item', $children[1]->title);
        $this->assertEquals('Child Item', $children[0]->title);
    }

    /** @test */
    public function it_can_create_a_dropdown_menu_item()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->url('settings/account', 'Account');
            $sub->url('settings/password', 'Password');
        }, 'Dropdown item');
        $this->assertCount(1, $menuItem->getChilds());
        $this->assertCount(2, $menuItem->getChilds()[0]->getChilds());
    }

    /** @test */
    public function it_can_make_a_simple_route_menu_item()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->route(['settings.account', ['user_id' => 1]], 'Account');
        }, 'Dropdown item');
        $children = $menuItem->getChilds()[0]->getChilds();

        $this->assertCount(1, $children);
        $childMenuItem = array_first($children);
        $this->assertEquals('settings.account', $childMenuItem->route[0]);
        $this->assertEquals(['user_id' => 1], $childMenuItem->route[1]);
    }

    /** @test */
    public function it_can_make_a_route_menu_item()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->route(['settings.account', ['user_id' => 1]], 'Account', 1, null, ['my-attr' => 'value']);
        }, 'Dropdown item');
        $children = $menuItem->getChilds()[0]->getChilds();

        $this->assertCount(1, $children);
        $childMenuItem = array_first($children);
        $this->assertEquals('settings.account', $childMenuItem->route[0]);
        $this->assertEquals(['user_id' => 1], $childMenuItem->route[1]);
        $this->assertSame(1, $childMenuItem->order);
        $id = md5(json_encode(array_except($childMenuItem->properties, ['attributes.id'])));
        $this->assertEquals(['my-attr' => 'value', 'id' => $id], $childMenuItem->attributes);
    }

    /** @test */
    public function it_can_make_a_simple_url_menu_item()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->url('settings/account', 'Account');
        }, 'Dropdown item');
        $children = $menuItem->getChilds()[0]->getChilds();

        $this->assertCount(1, $children);
        $childMenuItem = array_first($children);
        $this->assertEquals('settings/account', $childMenuItem->url);
        $this->assertEquals('Account', $childMenuItem->title);
    }

    /** @test */
    public function it_can_make_a_url_menu_item()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->url('settings/account', 'Account', 1, null, ['my-attr' => 'value']);
        }, 'Dropdown item');
        $children = $menuItem->getChilds()[0]->getChilds();

        $this->assertCount(1, $children);
        $childMenuItem = array_first($children);
        $this->assertEquals('settings/account', $childMenuItem->url);
        $this->assertEquals('Account', $childMenuItem->title);
        $this->assertSame(1, $childMenuItem->order);
        $id = md5(json_encode(array_except($childMenuItem->properties, ['attributes.id'])));
        $this->assertEquals(['my-attr' => 'value', 'id' => $id], $childMenuItem->attributes);
    }

    /** @test */
    public function it_can_add_a_menu_item_divider()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->url('settings/account', 'Account');
            $sub->divider();
        }, 'Dropdown item');

        $children = $menuItem->getChilds()[0]->getChilds();

        $this->assertCount(2, $children);
        $dividerMenuItem = $children[1];
        $this->assertEquals('divider', $dividerMenuItem->type);
        $this->assertTrue($dividerMenuItem->isDivider());
    }

    /** @test */
    public function it_can_add_a_header_menu_item()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->header('User Stuff');
            $sub->url('settings/account', 'Account');
        }, 'Dropdown item');

        $children = $menuItem->getChilds()[0]->getChilds();

        $this->assertCount(2, $children);
        $headerItem = $children[0];
        $this->assertEquals('header', $headerItem->type);
        $this->assertEquals('User Stuff', $headerItem->title);
        $this->assertTrue($headerItem->isHeader());
    }

    /** @test */
    public function it_can_get_the_correct_url_for_url_type()
    {
        $menuItem = new MenuItem(['url' => 'settings/account', 'title' => 'Parent Item']);

        $this->assertEquals('http://localhost/settings/account', $menuItem->getUrl());
    }

    /** @test */
    public function it_can_get_the_correct_url_for_route_type()
    {
        $this->app['router']->get('settings/account', ['as' => 'settings.account']);
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->route(['settings.account'], 'Account');
        }, 'Dropdown item');
        $children = $menuItem->getChilds()[0]->getChilds();
        $childMenuItem = array_first($children);

        $this->assertEquals('http://localhost/settings/account', $childMenuItem->getUrl());
    }

    /** @test */
    public function it_can_get_the_icon_html_attribute()
    {
        $menuItem = new MenuItem(['url' => 'settings/account', 'title' => 'Parent Item', 'icon' => 'fa fa-user']);

        $this->assertEquals('fa fa-user', $menuItem->icon);
    }

    /** @test */
    public function it_returns_no_icon_if_none_exist()
    {
        $menuItem = new MenuItem(['url' => 'settings/account', 'title' => 'Parent Item']);

        $this->assertNull($menuItem->icon);
    }

    /** @test */
    public function it_can_get_item_properties()
    {
        $menuItem = new MenuItem(['url' => 'settings/account', 'title' => 'Parent Item']);

        $this->assertEquals(['url' => 'settings/account', 'title' => 'Parent Item'], $menuItem->properties);
    }

    /** @test */
    public function it_can_get_item_html_attributes()
    {
        $menuItem = new MenuItem([
            'url' => 'settings/account',
            'title' => 'Parent Item',
            'attributes' => ['my-attr' => 'value'],
        ]);

        $this->assertEquals(' my-attr="value"', $menuItem->getAttributes());
    }

    /** @test */
    public function it_can_check_for_a_submenu()
    {
        $menuItem = new MenuItem(['title' => 'Parent Item']);
        $menuItem->dropdown(function (MenuItem $sub) {
            $sub->header('User Stuff');
            $sub->url('settings/account', 'Account');
        }, 'Dropdown item');

        $this->assertTrue($menuItem->hasChilds());
    }
}
