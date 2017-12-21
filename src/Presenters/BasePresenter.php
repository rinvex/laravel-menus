<?php

declare(strict_types=1);

namespace Rinvex\Menus\Presenters;

use Rinvex\Menus\Models\MenuItem;
use Rinvex\Menus\Contracts\PresenterContract;

abstract class BasePresenter implements PresenterContract
{
    /**
     * Get child menu items.
     *
     * @param \Rinvex\Menus\Models\MenuItem $item
     *
     * @return string
     */
    public function getChildMenuItems(MenuItem $item): string
    {
        $results = '';

        foreach ($item->getChilds() as $child) {
            if ($child->isHidden()) {
                continue;
            }

            if ($child->hasChilds()) {
                $results .= $this->getMultiLevelDropdownWrapper($child);
            } elseif ($child->isHeader()) {
                $results .= $this->getHeaderWrapper($child);
            } elseif ($child->isDivider()) {
                $results .= $this->getDividerWrapper();
            } else {
                $results .= $this->getMenuWithoutDropdownWrapper($child);
            }
        }

        return $results;
    }
}
