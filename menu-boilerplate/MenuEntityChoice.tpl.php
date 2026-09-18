<?php

namespace App\Service\EntityChoice;

use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Entity\MenuSection;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class MenuEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Menu';
    }

    public function getEntities(): array
    {
        return [
            Menu::class,
            MenuSection::class,
            MenuItem::class,
        ];
    }
}
