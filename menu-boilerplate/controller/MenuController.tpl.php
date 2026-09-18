<?php echo "<?php\n"; ?>

namespace App\Controller\Backend;

use App\Entity\<?php echo $singular['pascal_case']; ?>;
use App\Entity\<?php echo $singular['pascal_case']; ?>Section;
use App\Form\<?php echo $singular['pascal_case']; ?>Type;
use App\Repository\<?php echo $singular['pascal_case']; ?>Repository;
use App\Security\Voter\<?php echo $singular['pascal_case']; ?>ItemVoter;
use App\Security\Voter\<?php echo $singular['pascal_case']; ?>SectionVoter;
use App\Security\Voter\<?php echo $singular['pascal_case']; ?>Voter;
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
class <?php echo $singular['pascal_case']; ?>Controller extends AbstractController
{
    public function __construct(private <?php echo $singular['pascal_case']; ?>Repository $menuRepository)
    {
    }

    private const CSRF_TOKEN_REORDER = '<?php echo $singular['snake_case']; ?>_reorder';

    #[Route('/menus', name: '<?php echo $singular['snake_case']; ?>_index', methods: ['GET'])]
    public function index(): Response
    {
        $newMenu = new <?php echo $singular['pascal_case']; ?>();

        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::INDEX,
            $newMenu,
            'You cannot access the list of menus.'
        );

        $menus = $this->menuRepository->createQueryBuilder('mc')
            ->orderBy('mc.ordinal', \SortDirection::Ascending)
            ->getQuery()
            ->getResult();

        return $this->render('@backend/menu/<?php echo $singular['snake_case']; ?>_index.html.twig', [
            'menus' => $menus,
            'new_menu' => $newMenu,
            'attributes' => self::getAttributes(),
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/menus/reorder', name: '<?php echo $singular['snake_case']; ?>_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::INDEX,
            new <?php echo $singular['pascal_case']; ?>(),
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

    #[Route('/menu/create', name: '<?php echo $singular['snake_case']; ?>_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $menu = new <?php echo $singular['pascal_case']; ?>();

        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::CREATE,
            $menu,
            'You cannot create a new menu.'
        );

        $form = $this->createForm(<?php echo $singular['pascal_case']; ?>Type::class, $menu);

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

        return $this->render('@backend/menu/<?php echo $singular['snake_case']; ?>_create.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    #[Route('/menu/{id}', name: '<?php echo $singular['snake_case']; ?>_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?> $menu,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::VIEW,
            $menu,
            'You cannot view this menu.'
        );

        $new<?php echo $singular['pascal_case']; ?>Section = new <?php echo $singular['pascal_case']; ?>Section();
        $new<?php echo $singular['pascal_case']; ?>Section->setMenu($menu);

        return $this->render('@backend/menu/<?php echo $singular['snake_case']; ?>_view.html.twig', [
            'menu' => $menu,
            'new_<?php echo $singular['snake_case']; ?>_section' => $new<?php echo $singular['pascal_case']; ?>Section,
            'attributes' => self::getAttributes(),
            'csrf_token_name' => <?php echo $singular['pascal_case']; ?>SectionController::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/menu/{id}/edit', name: '<?php echo $singular['snake_case']; ?>_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?> $menu,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::EDIT,
            $menu,
            'You cannot edit this menu.'
        );

        $form = $this->createForm(<?php echo $singular['pascal_case']; ?>Type::class, $menu);

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

        return $this->render('@backend/menu/<?php echo $singular['snake_case']; ?>_edit.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    private function redirectForm(<?php echo $singular['pascal_case']; ?> $menu, FormInterface $form): Response
    {
        $clickedButtonName = $form->getClickedButton()->getName() ?? null;

        if ('keep_editing' === $clickedButtonName) {
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_edit', [
                'id' => $menu->getId(),
            ]);
        } elseif ('add_another' === $clickedButtonName) {
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_create');
        }

        return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_view', [
            'id' => $menu->getId(),
        ]);
    }

    #[Route('/menu/{id}/delete', name: '<?php echo $singular['snake_case']; ?>_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?> $menu,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::DELETE,
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

                return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/menu/<?php echo $singular['snake_case']; ?>_delete.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    public static function getAttributes(): array
    {
        return [
            'menu' => [
                'view' => <?php echo $singular['pascal_case']; ?>Voter::VIEW,
                'create' => <?php echo $singular['pascal_case']; ?>Voter::CREATE,
                'delete' => <?php echo $singular['pascal_case']; ?>Voter::DELETE,
                'edit' => <?php echo $singular['pascal_case']; ?>Voter::EDIT,
            ],
            'section' => [
                'view' => <?php echo $singular['pascal_case']; ?>SectionVoter::VIEW,
                'create' => <?php echo $singular['pascal_case']; ?>SectionVoter::CREATE,
                'delete' => <?php echo $singular['pascal_case']; ?>SectionVoter::DELETE,
                'edit' => <?php echo $singular['pascal_case']; ?>SectionVoter::EDIT,
            ],
            'item' => [
                'create' => <?php echo $singular['pascal_case']; ?>ItemVoter::CREATE,
                'delete' => <?php echo $singular['pascal_case']; ?>ItemVoter::DELETE,
                'edit' => <?php echo $singular['pascal_case']; ?>ItemVoter::EDIT,
            ],
        ];
    }
}
