<?php

namespace App\Controller\Backend;

use App\Entity\Menu;
use App\Entity\MenuSection;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use App\Security\Voter\MenuItemVoter;
use App\Security\Voter\MenuSectionVoter;
use App\Security\Voter\MenuVoter;
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
class MenuController extends AbstractController
{
    public function __construct(private MenuRepository $menuRepository)
    {
    }

    private const CSRF_TOKEN_REORDER = 'menu_reorder';

    #[Route('/menus', name: 'menu_index', methods: ['GET'])]
    public function index(): Response
    {
        $newMenu = new Menu();

        $this->denyAccessUnlessGranted(
            MenuVoter::INDEX,
            $newMenu,
            'You cannot access the list of menus.'
        );

        $menus = $this->menuRepository->createQueryBuilder('mc')
            ->orderBy('mc.ordinal', \SortDirection::Ascending)
            ->getQuery()
            ->getResult();

        return $this->render('@backend/menu/menu_index.html.twig', [
            'menus' => $menus,
            'new_menu' => $newMenu,
            'attributes' => self::getAttributes(),
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/menus/reorder', name: 'menu_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuVoter::INDEX,
            new Menu(),
            'You cannot reorder the menus.'
        );

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $menus = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($menus as $ordinal => $id) {
                $menu = $this->menuRepository->find($id);

                if ($menu) {
                    $menu->setOrdinal($ordinal);

                    $this->menuRepository->save($menu, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/menu/create', name: 'menu_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $menu = new Menu();

        $this->denyAccessUnlessGranted(
            MenuVoter::CREATE,
            $menu,
            'You cannot create a new menu.'
        );

        $form = $this->createForm(MenuType::class, $menu);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuRepository->save($menu, true);

                $this->addFlash('notice', 'The menu was created successfully.');

                return $this->redirectForm($menu, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/menu/menu_create.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    #[Route('/menu/{id}', name: 'menu_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] Menu $menu,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuVoter::VIEW,
            $menu,
            'You cannot view this menu.'
        );

        $newMenuSection = new MenuSection();
        $newMenuSection->setMenu($menu);

        return $this->render('@backend/menu/menu_view.html.twig', [
            'menu' => $menu,
            'new_menu_section' => $newMenuSection,
            'attributes' => self::getAttributes(),
            'csrf_token_name' => MenuSectionController::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/menu/{id}/edit', name: 'menu_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] Menu $menu,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuVoter::EDIT,
            $menu,
            'You cannot edit this menu.'
        );

        $form = $this->createForm(MenuType::class, $menu);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuRepository->save($menu, true);

                $this->addFlash('notice', 'The menu was updated successfully.');

                return $this->redirectForm($menu, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/menu/menu_edit.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    private function redirectForm(Menu $menu, FormInterface $form): Response
    {
        $clickedButtonName = $form->getClickedButton()->getName() ?? null;

        if ('keep_editing' === $clickedButtonName) {
            return $this->redirectToRoute('menu_edit', [
                'id' => $menu->getId(),
            ]);
        } elseif ('add_another' === $clickedButtonName) {
            return $this->redirectToRoute('menu_create');
        }

        return $this->redirectToRoute('menu_view', [
            'id' => $menu->getId(),
        ]);
    }

    #[Route('/menu/{id}/delete', name: 'menu_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] Menu $menu,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuVoter::DELETE,
            $menu,
            'You cannot delete this menu.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuRepository->remove($menu, true);

                $this->addFlash('notice', 'The menu was deleted successfully.');

                return $this->redirectToRoute('menu_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/menu/menu_delete.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    public static function getAttributes(): array
    {
        return [
            'menu' => [
                'view' => MenuVoter::VIEW,
                'create' => MenuVoter::CREATE,
                'delete' => MenuVoter::DELETE,
                'edit' => MenuVoter::EDIT,
            ],
            'section' => [
                'view' => MenuSectionVoter::VIEW,
                'create' => MenuSectionVoter::CREATE,
                'delete' => MenuSectionVoter::DELETE,
                'edit' => MenuSectionVoter::EDIT,
            ],
            'item' => [
                'create' => MenuItemVoter::CREATE,
                'delete' => MenuItemVoter::DELETE,
                'edit' => MenuItemVoter::EDIT,
            ],
        ];
    }
}
