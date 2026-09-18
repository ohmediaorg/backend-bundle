<?php echo "<?php\n"; ?>

namespace App\Controller\Backend;

use App\Entity\MenuItem;
use App\Entity\MenuItemPrice;
use App\Entity\MenuSection;
use App\Form\MenuItemType;
use App\Repository\MenuItemRepository;
use App\Security\Voter\MenuItemVoter;
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
class MenuItemController extends AbstractController
{
    public function __construct(private MenuItemRepository $menuItemRepository)
    {
    }

    public const CSRF_TOKEN_REORDER = 'menu_item_reorder';

    #[Route('/menu/items/reorder', name: 'menu_item_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuItemVoter::REORDER,
            new MenuItem(),
            'You cannot reorder the menu items.'
        );

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $menuItems = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($menuItems as $ordinal => $id) {
                $menuItem = $this->menuItemRepository->find($id);

                if ($menuItem) {
                    $menuItem->setOrdinal($ordinal);

                    $this->menuItemRepository->save($menuItem, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/menu/section/{id}/item/create', name: 'menu_item_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        #[MapEntity(id: 'id')] MenuSection $menuSection,
    ): Response {
        $menuItem = new MenuItem();
        $menuItem->setSection($menuSection);

        $menuItemPrice = new MenuItemPrice();
        $menuItemPrice->setLabel('Default');
        $menuItem->addPrice($menuItemPrice);

        $this->denyAccessUnlessGranted(
            MenuItemVoter::CREATE,
            $menuItem,
            'You cannot create a new menu item.'
        );

        $form = $this->createForm(MenuItemType::class, $menuItem);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuItemRepository->save($menuItem, true);

                $this->addFlash('notice', 'The menu item was created successfully.');

                return $this->redirectForm($menuItem, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/menu_item/menu_item_create.html.twig', [
            'form' => $form->createView(),
            'menu_item' => $menuItem,
        ]);
    }

    #[Route('/menu/item/{id}/edit', name: 'menu_item_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] MenuItem $menuItem,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuItemVoter::EDIT,
            $menuItem,
            'You cannot edit this menu item.'
        );

        $form = $this->createForm(MenuItemType::class, $menuItem);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuItemRepository->save($menuItem, true);

                $this->addFlash('notice', 'The menu item was updated successfully.');

                return $this->redirectForm($menuItem, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/menu_item/menu_item_edit.html.twig', [
            'form' => $form->createView(),
            'menu_item' => $menuItem,
        ]);
    }

    private function redirectForm(MenuItem $menuItem, FormInterface $form): Response
    {
        $clickedButtonName = $form->getClickedButton()->getName() ?? null;

        if ('keep_editing' === $clickedButtonName) {
            return $this->redirectToRoute('menu_item_edit', [
                'id' => $menuItem->getId(),
            ]);
        } elseif ('add_another' === $clickedButtonName) {
            return $this->redirectToRoute('menu_item_create', [
                'id' => $menuItem->getSection()->getId(),
            ]);
        }

        return $this->redirectToSection($menuItem);
    }

    #[Route('/menu/item/{id}/delete', name: 'menu_item_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] MenuItem $menuItem,
    ): Response {
        $this->denyAccessUnlessGranted(
            MenuItemVoter::DELETE,
            $menuItem,
            'You cannot delete this menu item.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->menuItemRepository->remove($menuItem, true);

                $this->addFlash('notice', 'The menu item was deleted successfully.');

                return $this->redirectToSection($menuItem);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/menu_item/menu_item_delete.html.twig', [
            'form' => $form->createView(),
            'menu_item' => $menuItem,
        ]);
    }

    private function redirectToSection(MenuItem $menuItem): Response
    {
        return $this->redirectToRoute('menu_section_view', [
            'id' => $menuItem->getSection()->getId(),
        ]);
    }
}
