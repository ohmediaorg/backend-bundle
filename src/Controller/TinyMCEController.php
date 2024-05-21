<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\ContentLinks\ContentLinkManager;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Shortcodes\ShortcodeManager;
use OHMedia\FileBundle\Entity\File;
use OHMedia\FileBundle\Entity\FileFolder;
use OHMedia\FileBundle\Repository\FileFolderRepository;
use OHMedia\FileBundle\Service\FileBrowser;
use OHMedia\FileBundle\Service\FileManager;
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

    #[Route('/tinymce/filebrowser/{id}', name: 'tinymce_filebrowser')]
    public function files(
        FileBrowser $fileBrowser,
        FileFolderRepository $fileFolderRepository,
        FileManager $fileManager,
        ImageManager $imageManager,
        int $id = null
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if (!$fileBrowser->isEnabled()) {
            return new JsonResponse([]);
        }

        $fileFolder = $id ? $fileFolderRepository->find($id) : null;

        $listingItems = $fileBrowser->getListing($fileFolder);

        $items = [];

        foreach ($listingItems as $listingItem) {
            $id = $listingItem->getId();

            if ($listingItem instanceof FileFolder) {
                $items[] = [
                    'type' => 'folder',
                    'name' => (string) $listingItem,
                    'url' => $this->generateUrl('tinymce_filebrowser', [
                        'id' => $id,
                    ]),
                ];
            } elseif ($listingItem instanceof File) {
                $item = [
                    'name' => (string) $listingItem,
                    'id' => (string) $id,
                ];

                if ($listingItem->isImage()) {
                    $item['type'] = 'image';
                    $item['image'] = $imageManager->render($listingItem, [
                        'width' => 47,
                        'height' => 47,
                        'style' => 'height:47px',
                    ]);
                } else {
                    $item['type'] = 'file';
                }

                $items[] = $item;
            }
        }

        $backPath = null;

        if ($fileFolder) {
            $parent = $fileFolder->getFolder();

            $backPath = $this->generateUrl('tinymce_filebrowser', [
                'id' => $parent ? $parent->getId() : null,
            ]);
        }

        return new JsonResponse([
            'back_path' => $backPath,
            'items' => $items,
        ]);
    }
}
