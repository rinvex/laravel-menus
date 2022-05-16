<?php

declare(strict_types=1);

namespace Rinvex\Menus\Presenters;

use Rinvex\Menus\Models\MenuItem;

class NavbarPresenter extends BasePresenter
{
    /**
     * {@inheritdoc}
     */
    public function getOpenTagWrapper(): string
    {
        return '<ul class="nav navbar-nav">';
    }

    /**
     * {@inheritdoc}
     */
    public function getCloseTagWrapper(): string
    {
        return '</ul>';
    }

    /**
     * {@inheritdoc}
     */
    public function getDividerWrapper(): string
    {
        return '<li class="divider"></li>';
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderWrapper(MenuItem $item): string
    {
        return '<li class="dropdown-header">'.($item->icon ? '<i class="'.$item->icon.'"></i> ' : '').$item->title.'</li>';
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuWithoutDropdownWrapper(MenuItem $item): string
    {
        return '<li '.$item->getItemAttributes().'>
                    <a href="'.$item->getUrl().'" '.$item->getAttributes().'>
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        '.$item->title.'
                    </a>
                </li>';
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuWithDropDownWrapper(MenuItem $item, bool $specialSidebar = false): string
    {
        return $specialSidebar
            ? $this->getHeaderWrapper($item).$this->getChildMenuItems($item)
            : '<li class="dropdown'.($item->hasActiveOnChild() ? ' active' : '').'">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        '.$item->title.'<strong class="caret"></strong>
                    </a>
                    <ul class="dropdown-menu">
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
        return '<li class="dropdown'.($item->hasActiveOnChild() ? ' active' : '').'">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        '.$item->title.'<strong class="caret pull-right caret-right"></strong>
                    </a>
                    <ul class="dropdown-menu">
                        '.$this->getChildMenuItems($item).'
                    </ul>
                </li>';
    }
}
