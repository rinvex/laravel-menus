<?php

declare(strict_types=1);

namespace Rinvex\Menus\Models;

use Illuminate\Support\Facades\Route;
use Collective\Html\HtmlFacade as HTML;
use Illuminate\Support\Facades\Request;

class MenuItem
{
    /**
     * The properties array.
     *
     * @var array
     */
    public $properties = [];

    /**
     * The childs collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $childs;

    /**
     * The hide callback.
     *
     * @var callable
     */
    protected $hideWhen;

    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct($properties = [])
    {
        $this->fill($properties);
        $this->childs = collect();
    }

    /**
     * Fill the properties.
     *
     * @param array $properties
     *
     * @return static
     */
    public function fill($properties)
    {
        $this->properties = array_merge($this->properties, $properties);

        return $this;
    }

    /**
     * Add new child item.
     *
     * @param array $properties
     *
     * @return static
     */
    protected function add(array $properties = [])
    {
        $properties['attributes']['id'] = $properties['attributes']['id'] ?? md5(json_encode($properties));
        $this->childs->push($item = new static($properties));

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
     * @return static
     */
    public function dropdown(callable $callback, string $title, int $order = null, string $icon = null, array $attributes = [])
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
     * @return static
     */
    public function route(array $route, string $title, int $order = null, string $icon = null, array $attributes = [])
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
     * @return static
     */
    public function url(string $url, string $title, int $order = null, string $icon = null, array $attributes = [])
    {
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
     * @return static
     */
    public function header(string $title, int $order = null, string $icon = null, array $attributes = [])
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
     * @return static
     */
    public function divider(int $order = null, array $attributes = [])
    {
        return $this->add(['type' => 'divider', 'order' => $order, 'attributes' => $attributes]);
    }

    /**
     * Get childs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChilds()
    {
        return $this->childs->sortBy('properties.order');
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->route ? route($this->route[0], $this->route[1] ?? []) : ($this->url ? url($this->url) : '');
    }

    /**
     * Get HTML attribute data.
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return HTML::attributes(array_except($this->attributes ?? [], ['active']));
    }

    /**
     * Check if the current item is divider.
     *
     * @return bool
     */
    public function isDivider(): bool
    {
        return $this->type === 'divider';
    }

    /**
     * Check if the current item is header.
     *
     * @return bool
     */
    public function isHeader(): bool
    {
        return $this->type === 'header';
    }

    /**
     * Check is the current item has sub menu .
     *
     * @return bool
     */
    public function hasChilds()
    {
        return $this->childs->isNotEmpty();
    }

    /**
     * Check the active state for current menu.
     *
     * @return bool
     */
    public function hasActiveOnChild()
    {
        if ($this->inactive()) {
            return false;
        }

        return $this->hasChilds() ? $this->hasActiveStateFromChilds() : false;
    }

    /**
     * Check if the item has active state from childs.
     *
     * @return bool
     */
    public function hasActiveStateFromChilds(): bool
    {
        return $this->getChilds()->contains(function (MenuItem $child) {
            if ($child->inactive()) {
                return false;
            }

            return ($child->hasChilds() && $child->hasActiveStateFromChilds())
                   || ($child->route && $child->hasActiveStateFromRoute())
                   || $child->isActive() || $child->hasActiveStateFromUrl();
        }) ?? false;
    }

    /**
     * Get inactive state.
     *
     * @return bool
     */
    public function inactive(): bool
    {
        if (is_bool($inactive = $this->inactive)) {
            return $inactive;
        }

        if (is_callable($inactive)) {
            return (bool) call_user_func($inactive);
        }

        return false;
    }

    /**
     * Get active state for current item.
     *
     * @return mixed
     */
    public function isActive()
    {
        if ($this->inactive()) {
            return false;
        }

        if (is_bool($active = $this->active)) {
            return $active;
        }

        if (is_callable($active)) {
            return call_user_func($active);
        }

        if ($this->route) {
            return $this->hasActiveStateFromRoute();
        }

        return $this->hasActiveStateFromUrl();
    }

    /**
     * Get active status using route.
     *
     * @return bool
     */
    protected function hasActiveStateFromRoute(): bool
    {
        return Route::is($this->route);
    }

    /**
     * Get active status using request url.
     *
     * @return bool
     */
    protected function hasActiveStateFromUrl(): bool
    {
        return Request::is($this->url);
    }

    /**
     * Set hide callback for current menu item.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function hideWhen(callable $callback)
    {
        $this->hideWhen = $callback;

        return $this;
    }

    /**
     * Set hide callback for current menu item.
     *
     * @param string $ability
     * @param mixed  $params
     *
     * @return $this
     */
    public function can(string $ability, $params = null)
    {
        $this->hideWhen = function () use ($ability, $params) {
            return ! auth()->user()->can($ability, $params);
        };

        return $this;
    }

    /**
     * Check if the menu item is hidden.
     *
     * @return bool
     */
    public function hidden()
    {
        return $this->hideWhen ? (bool) call_user_func($this->hideWhen) : false;
    }

    /**
     * Get property.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return data_get($this->properties, $key);
    }
}
