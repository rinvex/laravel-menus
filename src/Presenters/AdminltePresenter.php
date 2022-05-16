<?php

declare(strict_types=1);

namespace Rinvex\Menus\Presenters;

use Rinvex\Menus\Models\MenuItem;

class AdminltePresenter extends BasePresenter
{
    /**
     * {@inheritdoc}
     */
    public function getOpenTagWrapper(): string
    {
        return '<ul class="sidebar-menu tree" data-widget="tree">';
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
        return '<li class="header">'.($item->icon ? '<i class="'.$item->icon.'"></i> ' : '').$item->title.'</li>';
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuWithoutDropdownWrapper(MenuItem $item): string
    {
        return '<li '.$item->getItemAttributes().'>
                    <a href="'.$item->getUrl().'" '.$item->getLinkAttributes().'>
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        <span>'.$item->title.'</span>
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
            : '<li class="treeview'.($item->hasActiveOnChild() ? ' active' : '').'">
                    <a href="#">
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        <span>'.$item->title.'</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
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
        return '<li class="treeview'.($item->hasActiveOnChild() ? ' active' : '').'">
                    <a href="#">
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        <span>'.$item->title.'</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        '.$this->getChildMenuItems($item).'
                    </ul>
                </li>';
    }
}
