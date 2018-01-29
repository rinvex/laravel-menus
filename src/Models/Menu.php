<?php

declare(strict_types=1);

namespace Rinvex\Menus\Models;

use Closure;
use Countable;
use Rinvex\Menus\Factories\MenuFactory;
use Illuminate\View\Factory as ViewFactory;

class Menu implements Countable
{
    /**
     * The menus collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $menus;

    /**
     * The view factory.
     *
     * @var \Illuminate\View\Factory
     */
    private $views;

    /**
     * The constructor.
     *
     * @param \Illuminate\View\Factory $views
     */
    public function __construct(ViewFactory $views)
    {
        $this->views = $views;
        $this->menus = collect();
    }

    /**
     * Make new menu.
     *
     * @param string   $name
     * @param callable $resolver
     *
     * @return void
     */
    public function make($name, Closure $resolver = null): void
    {
        $builder = new MenuFactory();

        $builder->setViewFactory($this->views);

        $this->menus->put($name, $builder);

        ! $resolver || $resolver($builder);
    }

    /**
     * Check if the menu exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name): bool
    {
        return $this->menus->has($name);
    }

    /**
     * Get instance of the given menu if exists.
     *
     * @param string $name
     *
     * @return \Rinvex\Menus\Factories\MenuFactory|null
     */
    public function instance($name): ?MenuFactory
    {
        return $this->menus->get($name);
    }

    /**
     * Modify a specific menu.
     *
     * @param string   $name
     * @param \Closure $callback
     *
     * @return void
     */
    public function modify($name, Closure $callback): void
    {
        $instance = $this->instance($name);
        ! $instance || $callback($instance);
    }

    /**
     * Render the menu tag by given name.
     *
     * @param string $name
     * @param string $presenter
     * @param array  $bindings
     * @param bool   $specialSidebar
     *
     * @return string
     */
    public function render(string $name, string $presenter = null, array $bindings = [], bool $specialSidebar = false): string
    {
        return $this->has($name) ? $this->instance($name)->setBindings($bindings)->render($presenter, $specialSidebar) : null;
    }

    /**
     * Get all menus.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->menus;
    }

    /**
     * Get count from all menus.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->menus->count();
    }

    /**
     * Empty the current menus.
     */
    public function destroy()
    {
        $this->menus = collect();

        return $this;
    }
}
