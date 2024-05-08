<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Shortcodes\ShortcodeManager;
use OHMedia\FileBundle\Entity\File;
use OHMedia\FileBundle\Entity\FileFolder;
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

    #[Route('/tinymce/images', name: 'tinymce_images')]
    public function images(ImageManager $imageManager, FileBrowser $fileBrowser): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        return new JsonResponse($this->getTreeItems($imageManager, $fileBrowser));
    }

    private function getTreeItems(ImageManager $imageManager, FileBrowser $fileBrowser, FileFolder $fileFolder = null)
    {
        $listing = $fileBrowser->getListing($fileFolder);

        $treeItems = [];

        foreach ($listing as $item) {
            $id = $item->getId();

            if ($item instanceof FileFolder) {
                $children = $this->getTreeItems($imageManager, $fileBrowser, $item);

                if ($children) {
                    $treeItems[] = [
                        'type' => 'directory',
                        'id' => 'directory_'.$id,
                        'title' => (string) $item,
                        'children' => $children,
                    ];
                }
            } elseif (($item instanceof File) && $item->isImage()) {
                // $img = $imageManager->render($item, ['width' => 25, 'height' => 25]);

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
