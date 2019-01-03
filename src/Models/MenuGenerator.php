<?php

declare(strict_types=1);

namespace Rinvex\Menus\Models;

use Countable;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;
use Rinvex\Menus\Presenters\NavbarPresenter;
use Rinvex\Menus\Contracts\PresenterContract;
use Illuminate\Contracts\View\View as ViewContract;

class MenuGenerator implements Countable
{
    /**
     * The items collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     * The presenter class.
     *
     * @var string
     */
    protected $presenter = NavbarPresenter::class;

    /**
     * The URL prefix.
     *
     * @var string|null
     */
    protected $urlPrefix;

    /**
     * The view name.
     *
     * @var string
     */
    protected $view;

    /**
     * The laravel view factory instance.
     *
     * @var \Illuminate\View\Factory
     */
    protected $views;

    /**
     * Resolved item binding map.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * Create a new MenuGenerator instance.
     */
    public function __construct()
    {
        $this->items = collect();
    }

    /**
     * Find menu item by given key and value.
     *
     * @param string   $key
     * @param string   $value
     * @param callable $callback
     *
     * @return \Rinvex\Menus\Models\MenuItem|null
     */
    public function findBy(string $key, string $value, callable $callback = null): ?MenuItem
    {
        $item = $this->items->filter(function ($item) use ($key, $value) {
            return $item->{$key} === $value;
        })->first();

        (! is_callable($callback) || ! $item) || call_user_func($callback, $item);

        return $item;
    }

    /**
     * Find menu item by given key and value.
     *
     * @param string   $title
     * @param int      $order
     * @param string   $icon
     * @param array    $attributes
     * @param callable $callback
     *
     * @return \Rinvex\Menus\Models\MenuItem|null
     */
    public function findByTitleOrAdd(string $title, int $order = null, string $icon = null, array $attributes = [], callable $callback = null): ?MenuItem
    {
        if (! ($item = $this->findBy('title', $title, $callback))) {
            $item = $this->add(compact('title', 'order', 'icon', 'attributes'));
            ! is_callable($callback) || call_user_func($callback, $item);
        }

        return $item;
    }

    /**
     * Set view factory instance.
     *
     * @param \Illuminate\View\Factory $views
     *
     * @return $this
     */
    public function setViewFactory(ViewFactory $views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Set view.
     *
     * @param string $view
     *
     * @return $this
     */
    public function setView(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set Prefix URL.
     *
     * @param string $prefixUrl
     *
     * @return $this
     */
    public function setUrlPrefix(string $urlPrefix)
    {
        $this->urlPrefix = $urlPrefix;

        return $this;
    }

    /**
     * Set new presenter class.
     *
     * @param string $presenter
     *
     * @return $this
     */
    public function setPresenter(string $presenter)
    {
        $this->presenter = app('rinvex.menus.presenters')->get($presenter);

        return $this;
    }

    /**
     * Get presenter instance.
     *
     * @return \Rinvex\Menus\Contracts\PresenterContract
     */
    public function getPresenter(): PresenterContract
    {
        return new $this->presenter();
    }

    /**
     * Determine if the given name in the presenter style.
     *
     * @param string $presenter
     *
     * @return bool
     */
    public function presenterExists(string $presenter): bool
    {
        return app('rinvex.menus.presenters')->has($presenter);
    }

    /**
     * Set the resolved item bindings.
     *
     * @param array $bindings
     *
     * @return $this
     */
    public function setBindings(array $bindings)
    {
        $this->bindings = $bindings;

        return $this;
    }

    /**
     * Resolves a key from the bindings array.
     *
     * @param string|array $key
     *
     * @return mixed
     */
    public function resolve($key)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $key[$k] = $this->resolve($v);
            }
        } elseif (is_string($key)) {
            $matches = [];

            // Search for any {placeholders} and replace with their replacement values
            preg_match_all('/{[\s]*?([^\s]+)[\s]*?}/i', $key, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                if (array_key_exists($match[1], $this->bindings)) {
                    $key = preg_replace('/'.$match[0].'/', $this->bindings[$match[1]], $key, 1);
                }
            }
        }

        return $key;
    }

    /**
     * Resolves an array of menu items properties.
     *
     * @param \Illuminate\Support\Collection &$items
     *
     * @return void
     */
    protected function resolveItems(Collection &$items): void
    {
        $resolver = function ($property) {
            return $this->resolve($property) ?: $property;
        };

        $items->each(function (MenuItem $item) use ($resolver) {
            $item->fill(array_map($resolver, $item->properties));
        });
    }

    /**
     * Add new child menu.
     *
     * @param array $properties
     *
     * @return \Rinvex\Menus\Models\MenuItem
     */
    protected function add(array $properties = []): MenuItem
    {
        $properties['attributes']['id'] = $properties['attributes']['id'] ?? md5(json_encode($properties));
        $this->items->push($item = new MenuItem($properties));

        return $item;
    }

    /**
     * Create new menu with dropdown.
     *
     * @param callable $callback
     * @param string   $title
     * @param int      $order
     * @param string   $icon
     * @param array    $attributes
     *
     * @return \Rinvex\Menus\Models\MenuItem
     */
    public function dropdown(callable $callback, string $title, int $order = null, string $icon = null, array $attributes = []): MenuItem
    {
        call_user_func($callback, $item = $this->add(compact('title', 'order', 'icon', 'attributes')));

        return $item;
    }

    /**
     * Register new menu item using registered route.
     *
     * @param string $route
     * @param string $title
     * @param int    $order
     * @param string $icon
     * @param array  $attributes
     *
     * @return \Rinvex\Menus\Models\MenuItem
     */
    public function route(array $route, string $title, int $order = null, string $icon = null, array $attributes = []): MenuItem
    {
        return $this->add(compact('route', 'title', 'order', 'icon', 'attributes'));
    }

    /**
     * Register new menu item using url.
     *
     * @param string $url
     * @param string $title
     * @param int    $order
     * @param string $icon
     * @param array  $attributes
     *
     * @return \Rinvex\Menus\Models\MenuItem
     */
    public function url(string $url, string $title, int $order = null, string $icon = null, array $attributes = []): MenuItem
    {
        ! $this->urlPrefix || $url = $this->formatUrl($url);

        return $this->add(compact('url', 'title', 'order', 'icon', 'attributes'));
    }

    /**
     * Add new header item.
     *
     * @param string $title
     * @param int    $order
     * @param string $icon
     * @param array  $attributes
     *
     * @return \Rinvex\Menus\Models\MenuItem
     */
    public function header(string $title, int $order = null, string $icon = null, array $attributes = []): MenuItem
    {
        $type = 'header';

        return $this->add(compact('type', 'url', 'title', 'order', 'icon', 'attributes'));
    }

    /**
     * Add new divider item.
     *
     * @param int   $order
     * @param array $attributes
     *
     * @return \Rinvex\Menus\Models\MenuItem
     */
    public function divider(int $order = null, array $attributes = []): MenuItem
    {
        return $this->add(['type' => 'divider', 'order' => $order, 'attributes' => $attributes]);
    }

    /**
     * Get items count.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Empty the current menu items.
     *
     * @return $this
     */
    public function destroy()
    {
        $this->items = collect();

        return $this;
    }

    /**
     * Get menu items and order it by 'order' key.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getOrderedItems(): Collection
    {
        return $this->items->sortBy('properties.order');
    }

    /**
     * Render the menu to HTML tag.
     *
     * @param string $presenter
     * @param bool   $specialSidebar
     *
     * @return string
     */
    public function render(string $presenter = null, bool $specialSidebar = false): string
    {
        $this->resolveItems($this->items);

        if (! is_null($this->view)) {
            return $this->renderView($presenter, $specialSidebar)->render();
        }

        (! $presenter || ! $this->presenterExists($presenter)) || $this->setPresenter($presenter);

        return $this->renderMenu($specialSidebar);
    }

    /**
     * Render menu via view presenter.
     *
     * @param string $view
     * @param bool   $specialSidebar
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function renderView(string $view, bool $specialSidebar = false): ViewContract
    {
        return $this->views->make($view, ['items' => $this->getOrderedItems(), 'specialSidebar' => $specialSidebar]);
    }

    /**
     * Render the menu.
     *
     * @param bool $specialSidebar
     *
     * @return string
     */
    protected function renderMenu(bool $specialSidebar = false): string
    {
        $presenter = $this->getPresenter();
        $menu = $presenter->getOpenTagWrapper();

        foreach ($this->getOrderedItems() as $item) {
            if ($item->isHidden()) {
                continue;
            }

            if ($item->hasChilds()) {
                $menu .= $presenter->getMenuWithDropDownWrapper($item, $specialSidebar);
            } elseif ($item->isHeader()) {
                $menu .= $presenter->getHeaderWrapper($item);
            } elseif ($item->isDivider()) {
                $menu .= $presenter->getDividerWrapper();
            } else {
                $menu .= $presenter->getMenuWithoutDropdownWrapper($item);
            }
        }

        $menu .= $presenter->getCloseTagWrapper();

        return $menu;
    }

    /**
     * Format URL.
     *
     * @param string $url
     *
     * @return string
     */
    protected function formatUrl(string $url): string
    {
        $uri = $this->urlPrefix.$url;

        return $uri === '/' ? '/' : ltrim(rtrim($uri, '/'), '/');
    }
}
