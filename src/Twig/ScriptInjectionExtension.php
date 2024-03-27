<?php

namespace OHMedia\BackendBundle\Twig;

use OHMedia\SettingsBundle\Service\Settings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScriptInjectExtension extends AbstractExtension
{
    public const SCRIPT_HEAD_OPEN = 'oh_media_script_head_open';
    public const SCRIPT_HEAD_CLOSE = 'oh_media_script_head_close';
    public const SCRIPT_BODY_OPEN = 'oh_media_script_body_open';
    public const SCRIPT_BODY_CLOSE = 'oh_media_script_body_close';

    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('script_head_open', [$this, 'scriptHeadOpen'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('script_head_close', [$this, 'scriptHeadClose'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('script_body_open', [$this, 'scriptBodyOpen'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('script_body_close', [$this, 'scriptBodyClose'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function scriptHeadOpen()
    {
        return $this->script(self::SCRIPT_HEAD_OPEN);
    }

    public function scriptHeadClose()
    {
        return $this->script(self::SCRIPT_HEAD_CLOSE);
    }

    public function scriptBodyOpen()
    {
        return $this->script(self::SCRIPT_BODY_OPEN);
    }

    public function scriptBodyClose()
    {
        return $this->script(self::SCRIPT_BODY_CLOSE);
    }

    private function script(string $id): ?string
    {
        return $this->settings->get($id);
    }
}
