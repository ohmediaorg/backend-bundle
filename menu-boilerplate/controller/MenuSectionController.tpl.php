<?php echo "<?php\n"; ?>

namespace App\Controller\Backend;

use App\Entity\<?php echo $singular['pascal_case']; ?>;
use App\Entity\<?php echo $singular['pascal_case']; ?>Item;
use App\Entity\<?php echo $singular['pascal_case']; ?>Section;
use App\Form\<?php echo $singular['pascal_case']; ?>SectionType;
use App\Repository\<?php echo $singular['pascal_case']; ?>SectionRepository;
use App\Security\Voter\<?php echo $singular['pascal_case']; ?>SectionVoter;
use Doctrine\DBAL\Connection;
use OHMedia\BackendBundle\Form\MultiSaveType;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class <?php echo $singular['pascal_case']; ?>SectionController extends AbstractController
{
    public function __construct(private <?php echo $singular['pascal_case']; ?>SectionRepository $menuSectionRepository)
    {
    }

    public const CSRF_TOKEN_REORDER = '<?php echo $singular['snake_case']; ?>_section_reorder';

    #[Route('/<?php echo $singular['kebab_case']; ?>/sections/reorder', name: '<?php echo $singular['snake_case']; ?>_section_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>SectionVoter::REORDER,
            new <?php echo $singular['pascal_case']; ?>Section(),
            'You cannot reorder the menu sections.'
        );

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $menuSections = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($menuSections as $ordinal => $id) {
                $menuSection = $this->menuSectionRepository->find($id);

                if ($menuSection) {
                    $menuSection->setOrdinal($ordinal);

                    $this->menuSectionRepository->save($menuSection, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/<?php echo $singular['kebab_case']; ?>/{id}/section/create', name: '<?php echo $singular['snake_case']; ?>_section_create', methods: ['GET', 'POST'])]
    public function create(
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?> $menu,
        Request $request,
    ): Response {
        $menuSection = new <?php echo $singular['pascal_case']; ?>Section();
        $menuSection->setMenu($menu);

        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>SectionVoter::CREATE,
            $menuSection,
            'You cannot create a new menu section.'
        );

        $form = $this->createForm(<?php echo $singular['pascal_case']; ?>SectionType::class, $menuSection);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuSectionRepository->save($menuSection, true);

                $this->addFlash('notice', 'The menu section was created successfully.');

                return $this->redirectForm($menuSection, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>_section/<?php echo $singular['snake_case']; ?>_section_create.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>_section' => $menuSection,
        ]);
    }

    #[Route('/<?php echo $singular['kebab_case']; ?>/section/{id}', name: '<?php echo $singular['snake_case']; ?>_section_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?>Section $menuSection,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>SectionVoter::VIEW,
            $menuSection,
            'You cannot view this menu section.'
        );

        $new<?php echo $singular['pascal_case']; ?>Item = new <?php echo $singular['pascal_case']; ?>Item();
        $new<?php echo $singular['pascal_case']; ?>Item->setSection($menuSection);

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>_section/<?php echo $singular['snake_case']; ?>_section_view.html.twig', [
            '<?php echo $singular['snake_case']; ?>_section' => $menuSection,
            'new_<?php echo $singular['snake_case']; ?>_item' => $new<?php echo $singular['pascal_case']; ?>Item,
            'attributes' => <?php echo $singular['pascal_case']; ?>Controller::getAttributes(),
            'csrf_token_name' => <?php echo $singular['pascal_case']; ?>ItemController::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/<?php echo $singular['kebab_case']; ?>/section/{id}/edit', name: '<?php echo $singular['snake_case']; ?>_section_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?>Section $menuSection,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>SectionVoter::EDIT,
            $menuSection,
            'You cannot edit this menu section.'
        );

        $form = $this->createForm(<?php echo $singular['pascal_case']; ?>SectionType::class, $menuSection);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuSectionRepository->save($menuSection, true);

                $this->addFlash('notice', 'The menu section was updated successfully.');

                return $this->redirectForm($menuSection, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>_section/<?php echo $singular['snake_case']; ?>_section_edit.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>_section' => $menuSection,
        ]);
    }

    private function redirectForm(<?php echo $singular['pascal_case']; ?>Section $menuSection, FormInterface $form): Response
    {
        $clickedButtonName = $form->getClickedButton()->getName() ?? null;

        if ('keep_editing' === $clickedButtonName) {
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_section_edit', [
                'id' => $menuSection->getId(),
            ]);
        } elseif ('add_another' === $clickedButtonName) {
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_section_create', [
                'id' => $menuSection->getMenu()->getId(),
            ]);
        }

        return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_section_view', [
            'id' => $menuSection->getId(),
        ]);
    }

    #[Route('/<?php echo $singular['kebab_case']; ?>/section/{id}/delete', name: '<?php echo $singular['snake_case']; ?>_section_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?>Section $menuSection,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>SectionVoter::DELETE,
            $menuSection,
            'You cannot delete this menu section.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuSectionRepository->remove($menuSection, true);

                $this->addFlash('notice', 'The menu section was deleted successfully.');

                return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_view', [
                    'id' => $menuSection->getMenu()->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>_section/<?php echo $singular['snake_case']; ?>_section_delete.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>_section' => $menuSection,
        ]);
    }
}
