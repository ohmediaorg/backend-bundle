<?php

namespace OHMedia\BackendBundle\Shortcodes;

abstract class AbstractShortcodeProvider
{
    abstract public function getTitle(): array;

    final public function getShortcodes(): array
    {
        return $this->shortcodes;
    }

    final protected function addShortcode(Shortcode $shortcode): static
    {
        $this->shortcodes[] = $shortcode;

        return $this;
    }
}
