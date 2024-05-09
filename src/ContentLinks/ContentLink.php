<?php

namespace OHMedia\BackendBundle\ContentLinks;

class ContentLink
{
    private string $shortcode = '';
    private array $children = [];

    public function __construct(private string $title)
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getShortcode(): string
    {
        return $this->shortcode;
    }

    public function setShortcode(string $shortcode): static
    {
        $this->shortcode = $shortcode;
        $this->children = [];

        return $this;
    }

    public function getChildren(): string
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    public function setChildren(ContentLink ...$children): static
    {
        $this->children = $children;
        $this->shortcode = null;

        return $this;
    }
}
