<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Shortcodes\ShortcodeManager;
use OHMedia\FileBundle\Entity\File;
use OHMedia\FileBundle\Entity\FileFolder;
use OHMedia\FileBundle\Service\FileBrowser;
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

    #[Route('/tinymce/imagebrowser', name: 'tinymce_imagebrowser')]
    public function images(FileBrowser $fileBrowser): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if (!$fileBrowser->isEnabled()) {
            return new JsonResponse([]);
        }

        return new JsonResponse($this->getTreeItems($fileBrowser));
    }

    private function getTreeItems(FileBrowser $fileBrowser, FileFolder $fileFolder = null)
    {
        $listing = $fileBrowser->getListing($fileFolder);

        $treeItems = [];

        foreach ($listing as $item) {
            $id = $item->getId();

            if ($item instanceof FileFolder) {
                $children = $this->getTreeItems($fileBrowser, $item);

                if ($children) {
                    $treeItems[] = [
                        'type' => 'directory',
                        'id' => 'directory_'.$id,
                        'title' => (string) $item,
                        'children' => $children,
                    ];
                }
            } elseif (($item instanceof File) && $item->isImage()) {
                $treeItems[] = [
                    'type' => 'leaf',
                    'title' => (string) $item,
                    'id' => (string) $id,
                ];
            }
        }

        return $treeItems;
    }
}
