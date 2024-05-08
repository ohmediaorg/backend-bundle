<?php

namespace OHMedia\BackendBundle\Shortcodes;

class Shortcode
{
    public function __construct(
        public readonly $label,
        public readonly $shortcode
    ) {
    }
}
