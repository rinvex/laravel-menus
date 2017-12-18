<?php

declare(strict_types=1);

namespace Rinvex\Menus\Presenters;

use Rinvex\Menus\Models\MenuItem;

class NavMenuPresenter extends NavbarPresenter
{
    /**
     * {@inheritdoc}
     */
    public function getOpenTagWrapper(): string
    {
        return '<ul class="nav navmenu-nav">';
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuWithDropDownWrapper(MenuItem $item, bool $specialSidebar = false): string
    {
        return $specialSidebar
            ? $this->getHeaderWrapper($item).$this->getChildMenuItems($item)
            : '<li class="dropdown'.($item->hasActiveOnChild() ? ' active open' : '').'">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        '.$item->title.'<strong class="caret pull-right"></strong>
                    </a>
                    <ul class="dropdown-menu navmenu-nav">
                        '.$this->getChildMenuItems($item).'
                    </ul>
                </li>';
    }

    /**
     * Get multilevel menu wrapper.
     *
     * @param \Rinvex\Menus\Models\MenuItem $item
     *
     * @return string`
     */
    public function getMultiLevelDropdownWrapper(MenuItem $item): string
    {
        return '<li class="dropdown'.($item->hasActiveOnChild() ? ' active open' : '').'">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        '.$item->title.'<strong class="caret pull-right caret-right"></strong>
                    </a>
                    <ul class="dropdown-menu navmenu-nav">
                        '.$this->getChildMenuItems($item).'
                    </ul>
                </li>';
    }
}
