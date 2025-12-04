<?php

namespace OHMedia\BackendBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Security\Voter\EmailVoter;
use OHMedia\BootstrapBundle\Service\Paginator;
use OHMedia\EmailBundle\Entity\Email;
use OHMedia\EmailBundle\Repository\EmailRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class EmailController extends AbstractController
{
    #[Route('/emails', name: 'email_index', methods: ['GET'])]
    public function index(
        EmailRepository $emailRepository,
        Paginator $paginator,
        Request $request,
    ): Response {
        $newEmail = new Email();

        $this->denyAccessUnlessGranted(
            EmailVoter::INDEX,
            $newEmail,
            'You cannot access the list of emails.'
        );

        $qb = $emailRepository->createQueryBuilder('e');
        $qb->orderBy('e.id', 'desc');

        $searchForm = $this->getSearchForm($request);

        $this->applySearch($searchForm, $qb);

        return $this->render('@OHMediaBackend/email/email_index.html.twig', [
            'pagination' => $paginator->paginate($qb, 20),
            'view_attribute' => EmailVoter::VIEW,
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

        $formBuilder->add('search', SearchType::class, [
            'required' => false,
            'label' => 'To, cc, bcc, from, subject, content',
        ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        return $form;
    }

    private function applySearch(FormInterface $form, QueryBuilder $qb): void
    {
        $search = $form->get('search')->getData();

        if ($search) {
            $searchFields = [
                'e.to',
                'e.cc',
                'e.bcc',
                'e.from',
                'e.subject',
                'e.html',
            ];

            $searchLikes = [];
            foreach ($searchFields as $searchField) {
                $searchLikes[] = "$searchField LIKE :search";
            }

            $qb->andWhere('('.implode(' OR ', $searchLikes).')')
                ->setParameter('search', '%'.$search.'%');
        }
    }

    #[Route('/email/{id}', name: 'email_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] Email $email,
    ): Response {
        $this->denyAccessUnlessGranted(
            EmailVoter::VIEW,
            $email,
            'You cannot view this email.'
        );

        return new Response($email->getHtml());
    }
}
