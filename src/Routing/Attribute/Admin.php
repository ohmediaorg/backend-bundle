<?php

namespace OHMedia\BackendBundle\Routing\Attribute;

use Symfony\Component\Routing\Attribute\Route;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Admin extends Route
{
    public function __construct()
    {
        parent::__construct('/admin');
    }
}
