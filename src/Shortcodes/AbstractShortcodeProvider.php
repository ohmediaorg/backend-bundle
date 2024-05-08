<?php

namespace OHMedia\BackendBundle\Shortcodes;

abstract class AbstractShortcodeProvider
{
    abstract public function getTitle(): string;

    abstract public function buildShortcodes(): void;

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
