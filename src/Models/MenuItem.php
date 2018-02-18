<?php

declare(strict_types=1);

namespace Rinvex\Menus\Models;

use Illuminate\Support\Collection;
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
     * The hide callbacks collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $hideCallbacks;

    /**
     * The active callback.
     *
     * @var callable
     */
    protected $activeWhen;

    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct($properties = [])
    {
        $this->fill($properties);

        $this->hideCallbacks = collect();
        $this->childs = collect();
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
    public function getChilds(): Collection
    {
        return $this->childs->sortBy('properties.order');
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl(): string
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
        return HTML::attributes($this->attributes);
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
    public function hasChilds(): bool
    {
        return $this->childs->isNotEmpty();
    }

    /**
     * Check the active state for current menu.
     *
     * @return bool
     */
    public function hasActiveOnChild(): bool
    {
        return $this->hasChilds() ? $this->hasActiveStateFromChilds() : false;
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
        $this->hideCallbacks->push($callback);

        return $this;
    }

    /**
     * Set authorization callback for current menu item.
     *
     * @param string $ability
     * @param mixed  $params
     * @param string $guard
     *
     * @return $this
     */
    public function ifCan(string $ability, $params = null, $guard = null)
    {
        $this->hideCallbacks->push(function () use ($ability, $params, $guard) {
            return ! optional(auth()->guard($guard)->user())->can($ability, $params);
        });

        return $this;
    }

    /**
     * Set condition callback for current menu item.
     *
     * @param mixed $condition
     *
     * @return $this
     */
    public function if($condition)
    {
        $this->hideCallbacks->push(function () use ($condition) {
            return ! $condition;
        });

        return $this;
    }

    /**
     * Set authentication callback for current menu item.
     *
     * @param string $guard
     *
     * @return $this
     */
    public function ifUser($guard = null)
    {
        $this->hideCallbacks->push(function () use ($guard) {
            return ! auth()->guard($guard)->user();
        });

        return $this;
    }

    /**
     * Set authentication callback for current menu item.
     *
     * @param string $guard
     *
     * @return $this
     */
    public function ifGuest($guard = null)
    {
        $this->hideCallbacks->push(function () use ($guard) {
            return auth()->guard($guard)->user();
        });

        return $this;
    }

    /**
     * Check if the menu item is hidden.
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return (bool) $this->hideCallbacks->first(function ($callback) {
            return call_user_func($callback);
        });
    }

    /**
     * Get active state for current item.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if (is_callable($activeWhen = $this->activeWhen)) {
            return call_user_func($activeWhen);
        }

        if ($this->route) {
            return $this->hasActiveStateFromRoute();
        }

        return $this->hasActiveStateFromUrl();
    }

    /**
     * Set active callback.
     *
     * @param callable $route
     *
     * @return $this
     */
    public function activateWhen(callable $callback)
    {
        $this->activeWhen = $callback;

        return $this;
    }

    /**
     * Set active callback on the given route.
     *
     * @param string $route
     *
     * @return $this
     */
    public function activateOnRoute(string $route)
    {
        $this->activeWhen = function () use ($route) {
            return str_contains(Route::currentRouteName(), $route);
        };

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
     * Get active status using route.
     *
     * @return bool
     */
    protected function hasActiveStateFromRoute(): bool
    {
        return Route::is($this->route[0]);
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
     * Check if the item has active state from childs.
     *
     * @return bool
     */
    protected function hasActiveStateFromChilds(): bool
    {
        return $this->getChilds()->contains(function (MenuItem $child) {
            return ($child->hasChilds() && $child->hasActiveStateFromChilds())
                       || ($child->route && $child->hasActiveStateFromRoute())
                       || $child->isActive() || $child->hasActiveStateFromUrl();
        }) ?? false;
    }
}
