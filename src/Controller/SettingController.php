<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Security\Voter\SettingVoter;
use OHMedia\BackendBundle\Twig\ScriptInjectionExtension;
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
    #[Route('/settings/global-meta', name: 'settings_global_meta')]
    public function globalMeta(Request $request, MetaSettings $metaSettings): Response
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

            return $this->redirectToRoute('settings_global_meta');
        }

        return $this->render('@OHMediaBackend/settings/settings_global_meta.html.twig', [
            'form' => $form->createView(),
            'meta_default' => [
                'base_title' => MetaSettings::SETTING_BASE_TITLE,
                'description' => MetaSettings::SETTING_DESCRIPTION,
                'image' => MetaSettings::SETTING_IMAGE,
            ],
        ]);
    }

    #[Route('/settings/script-injection', name: 'settings_script_injection')]
    public function scripts(Request $request, Settings $settings): Response
    {
        $this->denyAccessUnlessGranted(
            SettingVoter::SCRIPTS,
            new Setting()
        );

        $scripts = [
            ScriptInjectionExtension::SCRIPT_HEAD_OPEN => 'Placed just after the opening <head> tag',
            ScriptInjectionExtension::SCRIPT_HEAD_CLOSE => 'Placed just before the closing </head> tag',
            ScriptInjectionExtension::SCRIPT_BODY_OPEN => 'Placed just after the opening <body> tag',
            ScriptInjectionExtension::SCRIPT_BODY_CLOSE => 'Placed just before the closing </body> tag',
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

            $this->addFlash('notice', 'Script injection settings updated successfully');

            return $this->redirectToRoute('settings_script_injection');
        }

        return $this->render('@OHMediaBackend/settings/settings_script_injection.html.twig', [
            'form' => $form->createView(),
            'scripts' => $scripts,
        ]);
    }
}
