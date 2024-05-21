<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\ContentLinks\ContentLinkManager;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Shortcodes\ShortcodeManager;
use OHMedia\FileBundle\Entity\File;
use OHMedia\FileBundle\Entity\FileFolder;
use OHMedia\FileBundle\Repository\FileFolderRepository;
use OHMedia\FileBundle\Service\FileBrowser;
use OHMedia\FileBundle\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class TinyMCEController extends AbstractController
{
    #[Route('/tinymce/shortcodes', name: 'tinymce_shortcodes')]
    public function shortcodes(ShortcodeManager $shortcodeManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        return new JsonResponse($shortcodeManager->getShortcodes());
    }

    #[Route('/tinymce/content-links', name: 'tinymce_content_links')]
    public function contentLinks(ContentLinkManager $contentLinkManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        return new JsonResponse($contentLinkManager->getContentLinks());
    }

    #[Route('/tinymce/imagebrowser/{id}', name: 'tinymce_imagebrowser')]
    public function images(
        FileBrowser $fileBrowser,
        FileFolderRepository $fileFolderRepository,
        ImageManager $imageManager,
        int $id = null
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if (!$fileBrowser->isEnabled()) {
            return new JsonResponse([]);
        }

        $fileFolder = $id ? $fileFolderRepository->find($id) : null;

        $listing = $fileBrowser->getListing($fileFolder);

        $items = [];

        if ($fileFolder) {
            $parent = $fileFolder->getFolder();

            $items[] = [
                'type' => 'directory',
                'text' => '..',
                'url' => $this->generateUrl('tinymce_imagebrowser', [
                    'id' => $parent ? $parent->getId() : null,
                ]),
            ];
        }

        foreach ($listing as $item) {
            $id = $item->getId();

            if ($item instanceof FileFolder) {
                $items[] = [
                    'type' => 'directory',
                    'text' => (string) $item,
                    'url' => $this->generateUrl('tinymce_imagebrowser', [
                        'id' => $id,
                    ]),
                ];
            } elseif (($item instanceof File) && $item->isImage()) {
                $items[] = [
                    'type' => 'image',
                    'image' => $imageManager->render($item, [
                        'width' => 47,
                        'height' => 47,
                        'style' => 'height:47px',
                    ]),
                    'text' => sprintf('%s (ID:%s)', $item, $id),
                    'id' => (string) $id,
                ];
            }
        }

        return new JsonResponse($items);
    }
}
