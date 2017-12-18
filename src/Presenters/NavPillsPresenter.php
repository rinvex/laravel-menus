<?php

declare(strict_types=1);

namespace Rinvex\Menus\Presenters;

class NavPillsPresenter extends NavbarPresenter
{
    /**
     * {@inheritdoc}
     */
    public function getOpenTagWrapper(): string
    {
        return '<ul class="nav nav-pills">';
    }
}
