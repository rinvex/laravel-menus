<?php

declare(strict_types=1);

namespace Rinvex\Menus\Contracts;

use Rinvex\Menus\Models\MenuItem;

interface PresenterContract
{
    /**
     * Get open tag wrapper.
     *
     * @return string
     */
    public function getOpenTagWrapper(): string;

    /**
     * Get close tag wrapper.
     *
     * @return string
     */
    public function getCloseTagWrapper(): string;

    /**
     * Get divider tag wrapper.
     *
     * @return string
     */
    public function getDividerWrapper(): string;

    /**
     * Get header tag wrapper.
     *
     * @param \Rinvex\Menus\Models\MenuItem $item
     *
     * @return string
     */
    public function getHeaderWrapper(MenuItem $item): string;

    /**
     * Get menu tag without dropdown wrapper.
     *
     * @param \Rinvex\Menus\Models\MenuItem $item
     *
     * @return string
     */
    public function getMenuWithoutDropdownWrapper(MenuItem $item): string;

    /**
     * Get menu tag with dropdown wrapper.
     *
     * @param \Rinvex\Menus\Models\MenuItem $item
     * @param bool                          $specialSidebar
     *
     * @return string
     */
    public function getMenuWithDropDownWrapper(MenuItem $item, bool $specialSidebar = false): string;

    /**
     * Get multi level dropdown menu wrapper.
     *
     * @param \Rinvex\Menus\Models\MenuItem $item
     *
     * @return string
     */
    public function getMultiLevelDropdownWrapper(MenuItem $item): string;

    /**
     * Get child menu items.
     *
     * @param \Rinvex\Menus\Models\MenuItem $item
     *
     * @return string
     */
    public function getChildMenuItems(MenuItem $item): string;
}
