<?php

namespace OHMedia\BackendBundle\Routing\Attribute;

use Symfony\Component\Routing\Attribute\Route;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Admin extends Route
{
    public function __construct(
        string|array $path = null,
        private ?string $name = null,
        private array $requirements = [],
        private array $options = [],
        private array $defaults = [],
        private ?string $host = null,
        array|string $methods = [],
        array|string $schemes = [],
        private ?string $condition = null,
        private ?int $priority = null,
        string $locale = null,
        string $format = null,
        bool $utf8 = null,
        bool $stateless = null,
        private ?string $env = null
    ) {
        parent::__construct('/admin');
    }
}
