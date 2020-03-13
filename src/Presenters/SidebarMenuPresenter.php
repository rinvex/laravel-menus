<?php

declare(strict_types=1);

namespace Rinvex\Menus\Presenters;

use Illuminate\Support\Str;
use Rinvex\Menus\Models\MenuItem;

class SidebarMenuPresenter extends BasePresenter
{
    /**
     * Get open tag wrapper.
     *
     * @return string
     */
    public function getOpenTagWrapper(): string
    {
        return '<ul class="nav navbar-nav">';
    }

    /**
     * Get close tag wrapper.
     *
     * @return string
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
     * Get menu tag without dropdown wrapper.
     *
     * @param \Rinvex\Menus\Models\MenuItem $item
     *
     * @return string
     */
    public function getMenuWithoutDropdownWrapper(MenuItem $item): string
    {
        return '<li class="'.($item->isActive() ? 'active' : '').'">
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
        $id = Str::random();

        return $specialSidebar
            ? $this->getHeaderWrapper($item).$this->getChildMenuItems($item)
            : '<li class="panel panel-default'.($item->hasActiveOnChild() ? ' active' : '').'" id="dropdown">
                    <a data-toggle="collapse" href="#'.$id.'">
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        '.$item->title.' <span class="caret"></span>
                    </a>
                    <div id="'.$id.'" class="panel-collapse collapse'.($item->hasActiveOnChild() ? ' in' : '').'">
                        <div class="panel-body">
                            <ul class="nav navbar-nav">
                                '.$this->getChildMenuItems($item).'
                            </ul>
                        </div>
                    </div>
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
        return $this->getMenuWithDropDownWrapper($item);
    }
}
