<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Security\Voter\SettingVoter;
use OHMedia\BackendBundle\Twig\ScriptInjectionExtension;
use OHMedia\MetaBundle\Settings\MetaSettings;
use OHMedia\SettingsBundle\Entity\Setting;
use OHMedia\SettingsBundle\Service\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class SettingController extends AbstractController
{
    #[Route('/settings/seo', name: 'settings_seo')]
    public function seo(
        Request $request,
        MetaSettings $metaSettings,
        Settings $settings
    ): Response {
        $this->denyAccessUnlessGranted(
            SettingVoter::SEO,
            new Setting()
        );

        $formBuilder = $this->createFormBuilder();

        $meta = $formBuilder->create('meta', FormType::class);

        $metaSettings->addDefaultFields($meta);

        $formBuilder->add($meta);

        $schema = $formBuilder->create('schema', FormType::class);

        $schema->add('organization_name', TextType::class, [
            'data' => $settings->get('schema_organization_name'),
            'label' => 'Organization Name',
            'required' => false,
        ]);

        $formBuilder->add($schema);

        $formBuilder->add('save', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $metaSettings->saveDefaultFields($form->get('meta'));

                $schemaData = $form->get('schema')->getData();

                $settings->set('schema_organization_name', $schemaData['organization_name']);

                $this->addFlash('notice', 'Global meta settings updated successfully');

                return $this->redirectToRoute('settings_seo');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaBackend/settings/settings_seo.html.twig', [
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

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $formData = $form->getData();

                foreach ($scripts as $id => $label) {
                    $settings->set($id, $formData[$id]);
                }

                $this->addFlash('notice', 'Script injection settings updated successfully');

                return $this->redirectToRoute('settings_script_injection');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaBackend/settings/settings_script_injection.html.twig', [
            'form' => $form->createView(),
            'scripts' => $scripts,
        ]);
    }
}
