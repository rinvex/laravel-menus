# Rinvex Menus

**Rinvex Menus** is a simple menu builder package for Laravel, that supports hierarchical structure, ordering, and styling with full flexibility using presenters for easy styling and custom structure of menu rendering.

[![Packagist](https://img.shields.io/packagist/v/rinvex/laravel-menus.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/rinvex/laravel-menus)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/rinvex/laravel-menus.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/rinvex/laravel-menus/)
[![Travis](https://img.shields.io/travis/rinvex/laravel-menus.svg?label=TravisCI&style=flat-square)](https://travis-ci.org/rinvex/laravel-menus)
[![StyleCI](https://styleci.io/repos/114586319/shield)](https://styleci.io/repos/114586319)
[![License](https://img.shields.io/packagist/l/rinvex/laravel-menus.svg?label=License&style=flat-square)](https://github.com/rinvex/laravel-menus/blob/develop/LICENSE)


## Credits notice

This package is a rewritten fork of [nWidart/laravel-menus](https://github.com/nWidart/laravel-menus), which itself is a fork of [pingpong-labs/menus](https://github.com/pingpong-labs/menus), original credits goes to them both. It's been widely rewritten to drop technical debt and remove legacy code, so be aware that the API is different and not compatible with the original package(s) for good. The main goals behind this fork was to:

- Simplify menu registration
- Clean code and enhance readability
- Enable sorting order feature by default
- Remove legacy code, and drop technical debt
- Allow extensibility with minimum or no core changes
- Enforce consistency and straighten API to be intuitive
- New sidebar menu feature, to treat dropdowns differently
- Integrate with Laravel [Authentication](https://laravel.com/docs/master/authentication) and [Authorization](https://laravel.com/docs/master/authorization) features to streamline hiding/displaying menus according to permissions


## Installation

1. Install the package via composer:
    ```shell
    composer require rinvex/laravel-menus
    ```

2. **Optionally** you can publish view files by running the following commands:
    ```shell
    php artisan vendor:publish --tag="rinvex-menus-views"
    ```

3. Done!


## Usage

### Create new menu

To register a new menu, simply call `Menu::register()` method. It takes two parameters, the first one is the menu title and the second one is a callback for defining menu items. See the following example:

```php
use Rinvex\Menus\Models\MenuItem;
use Rinvex\Menus\Models\MenuGenerator;

Menu::register('frontend.sidebar', function(MenuGenerator $menu) {
    // Add menu header
    $menu->header('Header Title');

    // Add url menu item
    $menu->url('url/path', 'Menu Title #1');

    // Add route menu item
    $menu->route(['route.name'], 'Menu Title #2');

    // Add menu divider
    $menu->divider();

    // Add menu dropdown (it can have childs too)
    $menu->dropdown(function(MenuItem $dropdown) {
        $dropdown->header('Child Header Title');
        $dropdown->url('url/path', 'Child Menu Title #1');
        $dropdown->route(['route.name'], 'Child Menu Title #2');
        $dropdown->divider();
    }, 'Dropdown Title', 50, 'fa fa-arrows', ['data-attribute' => 'something']);
});
```

All the `url`, `route`, `header`, and `dropdown` methods has a standard API like: `$menu->method('data', 'title', 'order', 'icon', 'linkAttributes', 'itemAttributes')` that's intutive and self explanatory. Only the first parameter is mandatory and different for each method, but the rest are all the same and optional. `header` accepts string title, `url`: string link, `route`: array with string route name and optionally route parameters, `dropdown`: callback for child items definition, other parameters are optional.

> **Notes:**
> - Menu items are ordered in ascending order by default. If you don't need sorting, just ignore the `order` parameter when defining your menus as it's optional anyway. That way menu items will be displayed in the order they've been added.
> - The `icon` parameter takes a css class name, like `fa fa-user` for fontawesome, and the `linkAttributes` parameter takes array of any additional HTML attributes you would like to add to your menu item.
> - You can create a multi-level menu items by creating child dropdown menus inside parent dropdown menus, and it has no limit, so you can create the structure you need as deep as you want.
> - You can create multiple menus with different names using the `Menu::register()` method, and call them in different places. Like if you want a topbar menu, and a sidebar menu ..etc


### Modify existing menu

To modify an existing menu item that's already been added somewhere else in the code you can use the same registration method:

```php
Menu::register('frontend.sidebar', function(MenuGenerator $menu) {
    // Add url menu item above the dropdown we created before
    $menu->url('different/path', 'Menu Title #3', 40);
});
```

As you can see, we just modified the `frontend.sidebar` menu, and added a new url menu item under the divider, above the dropdown. See, it's that simple!

Alternatively you can get a handle of the menu you need to modify, and then use it as you prefer, like so:

```php
$sidebar = Menu::instance('frontend.sidebar');
$sidebar->url('new/url', 'Menu Title #4', 40);
$sidebar->route('some.new.route', 'Menu Title #5', 60);
```

#### Hide menus conditionally

To simply hide any of your menu items, you can use any of the following methods:

```php
$sidebar->url('one/more/url', 'One more new item')->hideWhen(function () {
    return true; // Any expression
});
```

As you can see, the `hideWhen` method takes a closure that returns true or false. If true returned the menu item will be hidden, otherwise it will be displayed, so you can put whatever logic here to be evaluated.

And as a syntactic sugar, there's few more methods that makes life easier! See the `ifUser`, `ifGuest`, and `ifCan` methods:

```php
// Only display if logged condition is true
$sidebar->url('one/more/url', 'One more new item')->if(true);

// Only display if logged in user (authenticated)
$sidebar->url('one/more/url', 'One more new item')->ifUser();

// Only display if guest not yet authenticated
$sidebar->url('one/more/url', 'One more new item')->ifGuest();

// Only display if logged in user has required ability (authorization)
$sidebar->url('one/more/url', 'One more new item')->ifCan('do-some-ability');
```

Sure, as you expected all these methods works smoothly and fully integrated with Laravel's default [Authentication](https://laravel.com/docs/master/authentication) and [Authorization](https://laravel.com/docs/master/authorization) features.

To make it easy to control menu hide states, you can chain all hide methods infinitely and all hide callbacks will be stacked and executed in order. It will stop execution with the first positive condition result. Example:

```php
// Only display if logged in user has required ability (authorization)
$sidebar->url('one/more/url', 'One more new item')->ifUser()->ifCan('do-some-ability')->hideWhen(function () {
    return true; // Any expression
});
```

This example means that menu will only displayed for users, who has `do-some-ability` permission, and also when the `hideWhen` callback expression returns true.

#### Activate menus conditionally

To activate menus conditionally based on route name, you can set the route prefix to match against. If the current route name contains that prefix, then the menu item will be activated automatically. That way we can activate parent menu items by accessing child pages. Example:

```php
$menu->route(['route.name.example'], 'Menu Title #2')->activateOnRoute('route.name');
```

Now when we access any route prefixed by `route.name`, our menu with the `route.name.example` route will be activated automatically.

Alternatively, you can fully control when that menu item is beeing activated by adding your own logic within callback that resolve to boolean, as follows:

```php
$menu->route(['route.name.example'], 'Menu Title #2')->activateWhen(function () {
    return true; // Any expression
});
```

### Search for existing menu item

You can also search for a specific menu item, a dropdown for example using `findBy` and add child items to it directly. The `findBy` method can search inside your menus by any attribute and take two required parameters, the attribute name & value to search by, and optionaly you can pass a third parameter as a callback to define child items (a way to modify the dropdown in one go).

```php
Menu::register('frontend.sidebar', function(MenuGenerator $menu) {
    $menu->findBy('title', 'Dropdown Title', function (MenuItem $dropdown) { // Seach items by title
        $dropdown->route(['the.newest.route'], 'Yet another menu item', 15, 'fa fa-building-o');
    });
});
```

If you need to update a specific menu item, or for example you need to change the url or rename the title, that's totally achievable too using the same method above with one simple tweak:

```php
Menu::register('frontend.sidebar', function(MenuGenerator $menu) {
    $menu->findBy('title', 'Yet another menu item', function (MenuItem $item) {
        $item->fill(['icon' => 'fa fa-business]);
    });
});
```

This code search for a menu item titled 'Yet another menu item' and update only it's icon. The `fill` method accepts an array with any properties you'd like to update, and merge it with the originals, resulting an overridden menu item definition.


### Menu presenters

Rendering menus is the easiest part, but let's discover first few interesting concepts utilized by this package, Presenters!

Presenters are like layout drivers that defines the way your menus are rendered, and it could be different for each and every menu. Let's make it simple by explained example, if you have multiple sections in your project that uses different CSS frameworks, like vanilla bootstrap and AdminLTE, you can create two different presenters for both (fortunately these two already built in out-of-the-box, but you can build your own for any other framework). The way it work is by creating the presenter, register it with the package, and just use it's name. So in short, your menu definition never change even if you changed the whole layout or even shifted to another CSS framework, you just need to change your presenter.

Presenters are used also to define the different layouts for your menu structure, that way you can use menus to build navbars, dropdowns, or even tabs. It's all yours and the same code. Just hook your presenter and you're ready to go. By default there's few presenters built in for you out-of-the-box:

- `navbar` \Rinvex\Menus\Presenters\NavbarPresenter
- `navbar-right` \Rinvex\Menus\Presenters\NavbarRightPresenter
- `nav-pills` \Rinvex\Menus\Presenters\NavPillsPresenter
- `nav-tab` \Rinvex\Menus\Presenters\NavTabPresenter
- `sidebar` \Rinvex\Menus\Presenters\SidebarMenuPresenter
- `navmenu` \Rinvex\Menus\Presenters\NavMenuPresenter
- `adminlte` \Rinvex\Menus\Presenters\AdminltePresenter

All are based on Bootstrap except for AdminLTE, but you can build your own. You always use the alias, not the full class path.

#### Create new presenter

To build your own presenter you need to:

- Create a new PHP class that implements `\Rinvex\Menus\Contracts\PresenterContract`.
- Register your presenter with the package: `app('rinvex.menus.presenters')->put('new-presenter', \Your\New\Presenter\ClassPresenter::class)`

That's it, your new presenter is ready to be used by it's name `new-presenter`. See `Rinvex\Menus\Presenters\AdminltePresenter` source code for real example.

#### View presenters

In addition to the class-based presenters explained above, you can use view-based presenters as well. Fortunately there's also built in bootstrap views to be used and you can create your own too.

There's nothing complex here to be explained, just think of view presenters as normal Laravel views, because it is really are, nothing special. The only difference is when you render the menus, you can set your prefered presenter. View-based presenters has precedence over class-based presenters if both supplied, but if none supplied it will fallback to the default class-based built-in presenters. By default there's few view-based presenters built in for you out-of-the-box:

- `rinvex/menus::menu` Plain Menu
- `rinvex/menus::default` Bootstrap Navbar (default)
- `rinvex/menus::navbar-left` Bootstrap Navbar Left
- `rinvex/menus::navbar-right` Bootstrap Navbar Right
- `rinvex/menus::nav-tabs` Bootstrap Nav Tabs
- `rinvex/menus::nav-tabs-justified` Bootstrap Nav Tabs Justified
- `rinvex/menus::nav-pills` Bootstrap Nav Pills
- `rinvex/menus::nav-pills-stacked` Bootstrap Nav Pills Stacked
- `rinvex/menus::nav-pills-justified` Bootstrap Nav Pills Justified


### Render existing menu

To render a menu you can use the `Menu::render()` method as follows:

```php
Menu::render('frontend.sidebar');
```

As you will see in the method definition, there's three more optional parameters to be explained: `public function render(string $name, string $presenter = null, array $bindings = [], bool $specialSidebar = false)`. The `presenter` parameter specify how the menu is being rendered, the `bindings` is a simple way to search and replace title placeholders (more on this below), and the `specialSidebar` is a flag to treat sidebar dropdowns differently by displaying headers above each group instead of collapsible dropdowns (beta feature).

#### Data binding

When you define a new menu, you can put placeholders in titles, and then when rendering you can pass bindings to be replaced at runtime. Interesting, right? See the following example:

```php
// Define new menu item with title placeholder
$sidebar = Menu::instance('frontend.sidebar');
$sidebar->url('very/new/url', 'Welcome {user}');

// Render menu and bind data on runtime
Menu::render('frontend.sidebar', null, ['user' => 'Omran']);
```

As you can see we defined a new menu item with a `{user}` placeholder, and when we rendered the menu we passed the required data to be bound. It will do search/replace on runtime and so you can pass any dynamic data within menu item titles.

#### Change default presenter

You can change default presenters either on menu definition or on menu rendering step, but it's always prefered to do so on runtime rendering to have a stable unchanged menu structure, while keeping layout related changes like presenters on the frontend layer.

Here's how to change presenters both ways:

```php
// Change menu presenter on definition
$sidebar = Menu::instance('frontend.sidebar');
$sidebar->setView('view-name'); // Set view-based presenter
$sidebar->setPresenter('presenter-name'); // Set class-based presenter

// Change menu presenter on rendering
Menu::render('frontend.sidebar', 'view-name'); // Set view-based presenter
Menu::render('frontend.sidebar', 'presenter-name'); // Set class-based presenter
```

You don't need to worry about how this package works and how does it know whether the supplied presenter is view-based or class-based, but keep in mind that view-based presenters has precedence over class-based presenters, so this package will search for existing view-presenter with the supplied name, if found it will be used and returned immediately, otherwise it will search secondly for class-based presenters with the supplied name.


## Changelog

Refer to the [Changelog](CHANGELOG.md) for a full history of the project.


## Support

The following support channels are available at your fingertips:

- [Chat on Slack](https://bit.ly/rinvex-slack)
- [Help on Email](mailto:help@rinvex.com)
- [Follow on Twitter](https://twitter.com/rinvex)


## Contributing & Protocols

Thank you for considering contributing to this project! The contribution guide can be found in [CONTRIBUTING.md](CONTRIBUTING.md).

Bug reports, feature requests, and pull requests are very welcome.

- [Versioning](CONTRIBUTING.md#versioning)
- [Pull Requests](CONTRIBUTING.md#pull-requests)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Feature Requests](CONTRIBUTING.md#feature-requests)
- [Git Flow](CONTRIBUTING.md#git-flow)


## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to [help@rinvex.com](help@rinvex.com). All security vulnerabilities will be promptly addressed.


## About Rinvex

Rinvex is a software solutions startup, specialized in integrated enterprise solutions for SMEs established in Alexandria, Egypt since June 2016. We believe that our drive The Value, The Reach, and The Impact is what differentiates us and unleash the endless possibilities of our philosophy through the power of software. We like to call it Innovation At The Speed Of Life. That’s how we do our share of advancing humanity.


## License

This software is released under [The MIT License (MIT)](LICENSE).

(c) 2016-2022 Rinvex LLC, Some rights reserved.
