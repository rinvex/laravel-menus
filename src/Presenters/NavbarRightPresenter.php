<?php

declare(strict_types=1);

namespace Rinvex\Menus\Presenters;

use Rinvex\Menus\Models\MenuItem;

class NavbarRightPresenter extends NavbarPresenter
{
    /**
     * {@inheritdoc}
     */
    public function getOpenTagWrapper(): string
    {
        return '<ul class="nav navbar-nav navbar-right">';
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuWithDropDownWrapper(MenuItem $item, bool $specialSidebar = false): string
    {
        return $specialSidebar
            ? $this->getHeaderWrapper($item).$this->getChildMenuItems($item)
            : '<li class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        '.($item->icon ? '<i class="'.$item->icon.'"></i>' : '').'
                        '.$item->title.'<strong class="caret"></strong>
                    </a>
                    <ul class="dropdown-menu">
                        '.$this->getChildMenuItems($item).'
                    </ul>
                </li>';
    }
}
