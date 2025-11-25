<?php echo "<?php\n"; ?>

namespace App\Controller\Backend;

use App\Entity\<?php echo $singular['pascal_case']; ?>;
use App\Form\<?php echo $singular['pascal_case']; ?>Type;
use App\Repository\<?php echo $singular['pascal_case']; ?>Repository;
use App\Security\Voter\<?php echo $singular['pascal_case']; ?>Voter;
<?php if ($has_reorder) { ?>
use Doctrine\DBAL\Connection;
<?php } ?>
<?php if (!$has_reorder) { ?>
use Doctrine\ORM\QueryBuilder;
<?php } ?>
use OHMedia\BackendBundle\Form\MultiSaveType;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
<?php if (!$has_reorder) { ?>
use OHMedia\BootstrapBundle\Service\Paginator;
<?php } ?>
<?php if (!$has_reorder && $is_publishable) { ?>
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
<?php } ?>
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
<?php if (!$has_reorder && $is_publishable) { ?>
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
<?php } ?>
<?php if (!$has_reorder) { ?>
use Symfony\Component\Form\Extension\Core\Type\FormType;
<?php } ?>
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
<?php if (!$has_reorder) { ?>
use Symfony\Component\Form\Extension\Core\Type\TextType;
<?php } ?>
use Symfony\Component\Form\FormInterface;
<?php if ($has_reorder) { ?>
use Symfony\Component\HttpFoundation\JsonResponse;
<?php } ?>
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class <?php echo $singular['pascal_case']; ?>Controller extends AbstractController
{
    public function __construct(private <?php echo $singular['pascal_case']; ?>Repository $<?php echo $singular['camel_case']; ?>Repository)
    {
    }

<?php if ($has_reorder) { ?>
    private const CSRF_TOKEN_REORDER = '<?php echo $singular['snake_case']; ?>_reorder';

    #[Route('/<?php echo $plural['kebab_case']; ?>', name: '<?php echo $singular['snake_case']; ?>_index', methods: ['GET'])]
    public function index(): Response
    {
        $new<?php echo $singular['pascal_case']; ?> = new <?php echo $singular['pascal_case']; ?>();

        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::INDEX,
            $new<?php echo $singular['pascal_case']; ?>,
            'You cannot access the list of <?php echo $plural['readable']; ?>.'
        );

        $<?php echo $plural['camel_case']; ?> = $this-><?php echo $singular['camel_case']; ?>Repository->createQueryBuilder('<?php echo $alias; ?>')
            ->orderBy('<?php echo $alias; ?>.ordinal', 'asc')
            ->getQuery()
            ->getResult();

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>/<?php echo $singular['snake_case']; ?>_index.html.twig', [
            '<?php echo $plural['snake_case']; ?>' => $<?php echo $plural['camel_case']; ?>,
            'new_<?php echo $singular['snake_case']; ?>' => $new<?php echo $singular['pascal_case']; ?>,
            'attributes' => $this->getAttributes(),
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/<?php echo $plural['kebab_case']; ?>/reorder', name: '<?php echo $singular['snake_case']; ?>_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::INDEX,
            new <?php echo $singular['pascal_case']; ?>(),
            'You cannot reorder the <?php echo $plural['readable']; ?>.'
        );

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $<?php echo $plural['camel_case']; ?> = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($<?php echo $plural['camel_case']; ?> as $ordinal => $id) {
                $<?php echo $singular['camel_case']; ?> = $this-><?php echo $singular['camel_case']; ?>Repository->find($id);

                if ($<?php echo $singular['camel_case']; ?>) {
                    $<?php echo $singular['camel_case']; ?>->setOrdinal($ordinal);

                    $this-><?php echo $singular['camel_case']; ?>Repository->save($<?php echo $singular['camel_case']; ?>, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }
<?php } else { ?>
    #[Route('/<?php echo $plural['kebab_case']; ?>', name: '<?php echo $singular['snake_case']; ?>_index', methods: ['GET'])]
    public function index(
        Paginator $paginator,
        Request $request,
    ): Response {
        $new<?php echo $singular['pascal_case']; ?> = new <?php echo $singular['pascal_case']; ?>();

        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::INDEX,
            $new<?php echo $singular['pascal_case']; ?>,
            'You cannot access the list of <?php echo $plural['readable']; ?>.'
        );

        $qb = $this-><?php echo $singular['camel_case']; ?>Repository->createQueryBuilder('<?php echo $alias; ?>');
<?php if ($is_publishable) { ?>
        $qb->orderBy('CASE WHEN <?php echo $alias; ?>.published_at IS NULL THEN 0 ELSE 1 END', 'ASC');
        $qb->addOrderBy('<?php echo $alias; ?>.published_at', 'DESC');
<?php } else { ?>
        $qb->orderBy('<?php echo $alias; ?>.id', 'desc');
<?php } ?>

        $searchForm = $this->getSearchForm($request);

        $this->applySearch($searchForm, $qb);

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>/<?php echo $singular['snake_case']; ?>_index.html.twig', [
            'pagination' => $paginator->paginate($qb, 20),
            'new_<?php echo $singular['snake_case']; ?>' => $new<?php echo $singular['pascal_case']; ?>,
            'attributes' => $this->getAttributes(),
            'search_form' => $searchForm,
        ]);
    }

    private function getSearchForm(Request $request): FormInterface
    {
        $formBuilder = $this->container->get('form.factory')
            ->createNamedBuilder('', FormType::class, null, [
                'csrf_protection' => false,
            ]);

        $formBuilder->setMethod('GET');

        $formBuilder->add('search', TextType::class, [
            'required' => false,
        ]);
<?php if ($is_publishable) { ?>

        $formBuilder->add('status', ChoiceType::class, [
            'required' => false,
            'choices' => [
                'All' => '',
                'Published' => 'published',
                'Scheduled' => 'scheduled',
                'Draft' => 'draft',
            ],
        ]);
<?php } ?>

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        return $form;
    }

    private function applySearch(FormInterface $form, QueryBuilder $qb): void
    {
        $search = $form->get('search')->getData();

        if ($search) {
            $searchFields = [
                // TODO: put your search fields here
                '<?php echo $alias; ?>.created_by',
            ];

            $searchLikes = [];
            foreach ($searchFields as $searchField) {
                $searchLikes[] = "$searchField LIKE :search";
            }

            $qb->andWhere('('.implode(' OR ', $searchLikes).')')
                ->setParameter('search', '%'.$search.'%');
        }
<?php if ($is_publishable) { ?>

        $status = $form->get('status')->getData();

        if ('published' === $status) {
            $qb->andWhere('<?php echo $alias; ?>.published_at IS NOT NULL');
            $qb->andWhere('<?php echo $alias; ?>.published_at <= :now');
            $qb->setParameter('now', DateTimeUtil::getDateTimeUtc());
        } elseif ('scheduled' === $status) {
            $qb->andWhere('<?php echo $alias; ?>.published_at IS NOT NULL');
            $qb->andWhere('<?php echo $alias; ?>.published_at > :now');
            $qb->setParameter('now', DateTimeUtil::getDateTimeUtc());
        } elseif ('draft' === $status) {
            $qb->andWhere('<?php echo $alias; ?>.published_at IS NULL');
        }
<?php } ?>
    }
<?php } ?>

    #[Route('/<?php echo $singular['kebab_case']; ?>/create', name: '<?php echo $singular['snake_case']; ?>_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $<?php echo $singular['camel_case']; ?> = new <?php echo $singular['pascal_case']; ?>();

        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::CREATE,
            $<?php echo $singular['camel_case']; ?>,
            'You cannot create a new <?php echo $singular['readable']; ?>.'
        );

        $form = $this->createForm(<?php echo $singular['pascal_case']; ?>Type::class, $<?php echo $singular['camel_case']; ?>);

        $form->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this-><?php echo $singular['camel_case']; ?>Repository->save($<?php echo $singular['camel_case']; ?>, true);

                $this->addFlash('notice', 'The <?php echo $singular['readable']; ?> was created successfully.');

                return $this->redirectForm($<?php echo $singular['camel_case']; ?>, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>/<?php echo $singular['snake_case']; ?>_create.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>' => $<?php echo $singular['camel_case']; ?>,
        ]);
    }
<?php if ($has_view_route) { ?>

    #[Route('/<?php echo $singular['kebab_case']; ?>/{id}', name: '<?php echo $singular['snake_case']; ?>_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?> $<?php echo $singular['camel_case']; ?>,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::VIEW,
            $<?php echo $singular['camel_case']; ?>,
            'You cannot view this <?php echo $singular['readable']; ?>.'
        );

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>/<?php echo $singular['snake_case']; ?>_view.html.twig', [
            '<?php echo $singular['snake_case']; ?>' => $<?php echo $singular['camel_case']; ?>,
            'attributes' => $this->getAttributes(),
        ]);
    }
<?php } ?>

    #[Route('/<?php echo $singular['kebab_case']; ?>/{id}/edit', name: '<?php echo $singular['snake_case']; ?>_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?> $<?php echo $singular['camel_case']; ?>,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::EDIT,
            $<?php echo $singular['camel_case']; ?>,
            'You cannot edit this <?php echo $singular['readable']; ?>.'
        );

        $form = $this->createForm(<?php echo $singular['pascal_case']; ?>Type::class, $<?php echo $singular['camel_case']; ?>);

        $form->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this-><?php echo $singular['camel_case']; ?>Repository->save($<?php echo $singular['camel_case']; ?>, true);

                $this->addFlash('notice', 'The <?php echo $singular['readable']; ?> was updated successfully.');

                return $this->redirectForm($<?php echo $singular['camel_case']; ?>, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>/<?php echo $singular['snake_case']; ?>_edit.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>' => $<?php echo $singular['camel_case']; ?>,
        ]);
    }

    private function redirectForm(<?php echo $singular['pascal_case']; ?> $<?php echo $singular['camel_case']; ?>, FormInterface $form): Response
    {
        if ($form->get('save')->get('keep_editing')->isClicked()) {
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_edit', [
                'id' => $<?php echo $singular['camel_case']; ?>->getId(),
            ]);
        } elseif ($form->get('save')->get('add_another')->isClicked()) {
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_create');
        } else {
<?php if ($has_view_route) { ?>
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_view', [
                'id' => $<?php echo $singular['camel_case']; ?>->getId(),
            ]);
<?php } else { ?>
            return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_index');
<?php } ?>
        }
    }

    #[Route('/<?php echo $singular['kebab_case']; ?>/{id}/delete', name: '<?php echo $singular['snake_case']; ?>_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] <?php echo $singular['pascal_case']; ?> $<?php echo $singular['camel_case']; ?>,
    ): Response {
        $this->denyAccessUnlessGranted(
            <?php echo $singular['pascal_case']; ?>Voter::DELETE,
            $<?php echo $singular['camel_case']; ?>,
            'You cannot delete this <?php echo $singular['readable']; ?>.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this-><?php echo $singular['camel_case']; ?>Repository->remove($<?php echo $singular['camel_case']; ?>, true);

                $this->addFlash('notice', 'The <?php echo $singular['readable']; ?> was deleted successfully.');

                return $this->redirectToRoute('<?php echo $singular['snake_case']; ?>_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/<?php echo $singular['snake_case']; ?>/<?php echo $singular['snake_case']; ?>_delete.html.twig', [
            'form' => $form->createView(),
            '<?php echo $singular['snake_case']; ?>' => $<?php echo $singular['camel_case']; ?>,
        ]);
    }

    private function getAttributes(): array
    {
        return [
<?php if ($has_view_route) { ?>
            'view' => <?php echo $singular['pascal_case']; ?>Voter::VIEW,
<?php } ?>
            'create' => <?php echo $singular['pascal_case']; ?>Voter::CREATE,
            'delete' => <?php echo $singular['pascal_case']; ?>Voter::DELETE,
            'edit' => <?php echo $singular['pascal_case']; ?>Voter::EDIT,
        ];
    }
}
