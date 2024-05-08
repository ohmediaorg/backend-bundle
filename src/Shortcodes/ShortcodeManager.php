<?php

namespace OHMedia\BackendBundle\Shortcodes;

class ShortcodeManager
{
    private array $shortcodeProviders = [];

    public function addShortcodeProvider(AbstractShortcodeProvider $shortcodeProvider): self
    {
        $this->shortcodeProviders[] = $shortcodeProvider;

        return $this;
    }

    public function getShortcodes()
    {
        usort($this->shortcodeProviders, function (
            AbstractShortcodeProvider $a,
            AbstractShortcodeProvider $b
        ) {
            return $a->getTitle() <=> $b->getTitle();
        });

        $tabs = [];

        foreach ($this->shortcodeProviders as $i => $shortcodeProvider) {
            $name = 'tab_'.$i;

            $selectbox = [
                'type' => 'selectbox',
                'name' => $name.'_shortcode',
                'label' => 'Shortcode',
                'items' => [],
            ];

            foreach ($shortcodeProvider->getShortcodes() as $shortcode) {
                $selectbox['items'][] = [
                    'value' => trim($shortcode->shortcode, '{} '),
                    'text' => $shortcode->label,
                ];
            }

            $tabs[] = [
                'name' => $name,
                'title' => $shortcodeProvider->getTitle(),
                'items' => [$selectbox],
            ];
        }

        return $tabs;
    }
}
