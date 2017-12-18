<?php

declare(strict_types=1);

namespace Rinvex\Menus\Presenters;

class NavTabPresenter extends NavbarPresenter
{
    /**
     * {@inheritdoc}
     */
    public function getOpenTagWrapper(): string
    {
        return '<ul class="nav nav-tabs">';
    }
}
