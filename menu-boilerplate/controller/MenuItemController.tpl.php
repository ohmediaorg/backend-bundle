<?php echo "<?php\n"; ?>

namespace App\Controller\Backend;

use App\Entity\<?php echo $singular['pascal_case']; ?>Item;
use App\Entity\<?php echo $singular['pascal_case']; ?>ItemPrice;
use App\Entity\<?php echo $singular['pascal_case']; ?>Section;
use App\Form\<?php echo $singular['pascal_case']; ?>ItemType;
use App\Repository\<?php echo $singular['pascal_case']; ?>ItemRepository;
use App\Security\Voter\<?php echo $singular['pascal_case']; ?>ItemVoter;
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
class <?php echo $singular['pascal_case']; ?>ItemController extends AbstractController
{
    public function __construct(private <?php echo $singular['pascal_case']; ?>ItemRepository $menuItemRepository)
    {
    }

    public const CSRF_TOKEN_REORDER = '<?php echo $singular['snake_case']; ?>_item_reorder';

    #[Route('/<?php echo $singular['kebab_case']; ?>/items/reorder', name: '<?php echo $singular['snake_case']; ?>_item_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>ItemVoter::REORDER,
            new <?php echo $singular['pascal_case']; ?>Item(),
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

    #[Route('/<?php echo $singular['kebab_case']; ?>/section/{id}/item/create', name: '<?php echo $singular['snake_case']; ?>_item_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?>Section $menuSection,
    ): Response {
        $menuItem = new <?php echo $singular['pascal_case']; ?>Item();
        $menuItem->setSection($menuSection);

        $menuItemPrice = new <?php echo $singular['pascal_case']; ?>ItemPrice();
        $menuItemPrice->setLabel('Default');
        $menuItem->addPrice($menuItemPrice);

        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>ItemVoter::CREATE,
            $menuItem,
            'You cannot create a new menu item.'
        );

        $form = $this->createForm(<?php echo $singular['pascal_case']; ?>ItemType::class, $menuItem);

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

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>_item/<?php echo $singular['snake_case']; ?>_item_create.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>_item' => $menuItem,
        ]);
    }

    #[Route('/<?php echo $singular['kebab_case']; ?>/item/{id}/edit', name: '<?php echo $singular['snake_case']; ?>_item_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?>Item $menuItem,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>ItemVoter::EDIT,
            $menuItem,
            'You cannot edit this menu item.'
        );

        $form = $this->createForm(<?php echo $singular['pascal_case']; ?>ItemType::class, $menuItem);

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

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>_item/<?php echo $singular['snake_case']; ?>_item_edit.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>_item' => $menuItem,
        ]);
    }

    private function redirectForm(<?php echo $singular['pascal_case']; ?>Item $menuItem, FormInterface $form): Response
    {
        $clickedButtonName = $form->getClickedButton()->getName() ?? null;

        if ('keep_editing' === $clickedButtonName) {
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_item_edit', [
                'id' => $menuItem->getId(),
            ]);
        } elseif ('add_another' === $clickedButtonName) {
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_item_create', [
                'id' => $menuItem->getSection()->getId(),
            ]);
        }

        return $this->redirectToSection($menuItem);
    }

    #[Route('/<?php echo $singular['kebab_case']; ?>/item/{id}/delete', name: '<?php echo $singular['snake_case']; ?>_item_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?>Item $menuItem,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>ItemVoter::DELETE,
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

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>_item/<?php echo $singular['snake_case']; ?>_item_delete.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>_item' => $menuItem,
        ]);
    }

    private function redirectToSection(<?php echo $singular['pascal_case']; ?>Item $menuItem): Response
    {
        return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_section_view', [
            'id' => $menuItem->getSection()->getId(),
        ]);
    }
}
