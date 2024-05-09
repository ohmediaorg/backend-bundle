<?php

namespace OHMedia\BackendBundle\ContentLinks;

class ContentLinkManager
{
    private array $contentLinkProviders = [];

    public function addContentLinkProvider(AbstractContentLinkProvider $contentLinkProvider): self
    {
        $this->contentLinkProviders[] = $contentLinkProvider;

        return $this;
    }

    public function getContentLinks()
    {
        usort($this->contentLinkProviders, function (
            AbstractContentLinkProvider $a,
            AbstractContentLinkProvider $b
        ) {
            return $a->getTitle() <=> $b->getTitle();
        });

        $tabs = [];

        foreach ($this->contentLinkProviders as $i => $contentLinkProvider) {
            $contentLinkProvider->buildContentLinks();

            $contentLinks = $contentLinkProvider->getContentLinks();

            if (!$contentLinks) {
                continue;
            }

            $items = $this->getTreeItems(...$contentLinks);

            if (!$items) {
                continue;
            }

            $tabs[] = [
                'title' => $contentLinkProvider->getTitle(),
                'items' => [[
                    'type' => 'tree',
                    'items' => $items,
                ]],
            ];
        }

        return $tabs;
    }

    private int $id = 0;

    private function getTreeItems(ContentLink ...$contentLinks)
    {
        $treeItems = [];

        foreach ($contentLinks as $contentLink) {
            $title = $contentLink->getTitle();

            if ($contentLink->hasChildren()) {
                $children = $this->getTreeItems(...$contentLink->getChildren());

                if ($children) {
                    $treeItems[] = [
                        'type' => 'directory',
                        'id' => 'directory_'.$this->id++,
                        'title' => $title,
                        'children' => $children,
                    ];
                }
            } else {
                $treeItems[] = [
                    'type' => 'leaf',
                    'title' => $title,
                    'id' => json_encode([
                        'href' => $contentLink->getShortcode(),
                        'title' => $title,
                        'text' => $title,
                    ]),
                ];
            }
        }

        return $treeItems;
    }
}
