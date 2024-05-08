<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Shortcodes\ShortcodeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class ShortcodesController extends AbstractController
{
    #[Route('/shortcodes', name: 'tinymce_shortcodes')]
    public function __invoke(ShortcodeManager $shortcodeManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        return new JsonResponse($shortcodeManager->getShortcodes());
    }
}
