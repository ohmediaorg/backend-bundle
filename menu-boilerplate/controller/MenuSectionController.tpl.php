<?php echo "<?php\n"; ?>

namespace App\Controller\Backend;

use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Entity\MenuSection;
use App\Form\MenuSectionType;
use App\Repository\MenuSectionRepository;
use App\Security\Voter\MenuSectionVoter;
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
class MenuSectionController extends AbstractController
{
    public function __construct(private MenuSectionRepository $menuSectionRepository)
    {
    }

    public const CSRF_TOKEN_REORDER = 'menu_section_reorder';

    #[Route('/menu/sections/reorder', name: 'menu_section_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuSectionVoter::REORDER,
            new MenuSection(),
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

    #[Route('/menu/{id}/section/create', name: 'menu_section_create', methods: ['GET', 'POST'])]
    public function create(
        #[MapEntity(id: 'id')] Menu $menu,
        Request $request,
    ): Response {
        $menuSection = new MenuSection();
        $menuSection->setMenu($menu);

        $this->denyAccessUnlessGranted(
            MenuSectionVoter::CREATE,
            $menuSection,
            'You cannot create a new menu section.'
        );

        $form = $this->createForm(MenuSectionType::class, $menuSection);

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

        return $this->render('@backend/menu_section/menu_section_create.html.twig', [
            'form' => $form->createView(),
            'menu_section' => $menuSection,
        ]);
    }

    #[Route('/menu/section/{id}', name: 'menu_section_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] MenuSection $menuSection,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuSectionVoter::VIEW,
            $menuSection,
            'You cannot view this menu section.'
        );

        $newMenuItem = new MenuItem();
        $newMenuItem->setSection($menuSection);

        return $this->render('@backend/menu_section/menu_section_view.html.twig', [
            'menu_section' => $menuSection,
            'new_menu_item' => $newMenuItem,
            'attributes' => MenuController::getAttributes(),
            'csrf_token_name' => MenuItemController::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/menu/section/{id}/edit', name: 'menu_section_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] MenuSection $menuSection,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuSectionVoter::EDIT,
            $menuSection,
            'You cannot edit this menu section.'
        );

        $form = $this->createForm(MenuSectionType::class, $menuSection);

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

        return $this->render('@backend/menu_section/menu_section_edit.html.twig', [
            'form' => $form->createView(),
            'menu_section' => $menuSection,
        ]);
    }

    private function redirectForm(MenuSection $menuSection, FormInterface $form): Response
    {
        $clickedButtonName = $form->getClickedButton()->getName() ?? null;

        if ('keep_editing' === $clickedButtonName) {
            return $this->redirectToRoute('menu_section_edit', [
                'id' => $menuSection->getId(),
            ]);
        } elseif ('add_another' === $clickedButtonName) {
            return $this->redirectToRoute('menu_section_create', [
                'id' => $menuSection->getMenu()->getId(),
            ]);
        }

        return $this->redirectToRoute('menu_section_view', [
            'id' => $menuSection->getId(),
        ]);
    }

    #[Route('/menu/section/{id}/delete', name: 'menu_section_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] MenuSection $menuSection,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuSectionVoter::DELETE,
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

                return $this->redirectToRoute('menu_view', [
                    'id' => $menuSection->getMenu()->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/menu_section/menu_section_delete.html.twig', [
            'form' => $form->createView(),
            'menu_section' => $menuSection,
        ]);
    }
}
