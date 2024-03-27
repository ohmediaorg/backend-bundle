<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Security\Voter\SettingVoter;
use OHMedia\MetaBundle\Settings\MetaSettings;
use OHMedia\SettingsBundle\Entity\Setting;
use OHMedia\SettingsBundle\Service\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class SettingController extends AbstractController
{
    #[Route('/settings/meta', name: 'settings_meta')]
    public function meta(Request $request, MetaSettings $metaSettings): Response
    {
        $this->denyAccessUnlessGranted(
            SettingVoter::META,
            new Setting()
        );

        $formBuilder = $this->createFormBuilder();

        $metaSettings->addDefaultFields($formBuilder);

        $formBuilder->add('save', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $metaSettings->saveDefaultFields($form);

            $this->addFlash('notice', 'Global meta settings updated successfully');

            return $this->redirectToRoute('settings_meta');
        }

        return $this->render('@OHMediaBackend/settings/settings_meta.html.twig', [
            'form' => $form->createView(),
            'meta_default' => [
                'base_title' => MetaSettings::SETTING_BASE_TITLE,
                'description' => MetaSettings::SETTING_DESCRIPTION,
                'image' => MetaSettings::SETTING_IMAGE,
            ],
        ]);
    }

    #[Route('/settings/scripts', name: 'settings_scripts')]
    public function scripts(Request $request, Settings $settings): Response
    {
        $this->denyAccessUnlessGranted(
            SettingVoter::SCRIPTS,
            new Setting()
        );

        $scripts = [
            'script_head_open' => 'Placed just after the opening <head> tag',
            'script_head_close' => 'Placed just before the closing </head> tag',
            'script_body_open' => 'Placed just after the opening <body> tag',
            'script_body_close' => 'Placed just before the closing </body> tag',
        ];

        $formBuilder = $this->createFormBuilder();

        foreach ($scripts as $id => $label) {
            $formBuilder->add($id, TextareaType::class, [
                'label' => $label,
                'data' => $settings->get($id),
                'required' => false,
                'attr' => [
                    'rows' => 10,
                ],
            ]);
        }

        $formBuilder->add('save', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            foreach ($scripts as $id => $label) {
                $settings->set($id, $formData[$id]);
            }

            $this->addFlash('notice', 'Script settings updated successfully');

            return $this->redirectToRoute('settings_scripts');
        }

        return $this->render('@OHMediaBackend/settings/settings_scripts.html.twig', [
            'form' => $form->createView(),
            'scripts' => $scripts,
        ]);
    }
}
