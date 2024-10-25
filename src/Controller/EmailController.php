<?php

namespace OHMedia\BackendBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BackendBundle\Security\Voter\EmailVoter;
use OHMedia\BootstrapBundle\Service\Paginator;
use OHMedia\EmailBundle\Entity\Email;
use OHMedia\EmailBundle\Repository\EmailRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class EmailController extends AbstractController
{
    #[Route('/emails', name: 'email_index', methods: ['GET'])]
    public function index(
        EmailRepository $emailRepository,
        Paginator $paginator
    ): Response {
        $newEmail = new Email();

        $this->denyAccessUnlessGranted(
            EmailVoter::INDEX,
            $newEmail,
            'You cannot access the list of emails.'
        );

        $qb = $emailRepository->createQueryBuilder('e');
        $qb->orderBy('e.id', 'desc');

        return $this->render('@OHMediaBackend/email/email_index.html.twig', [
            'pagination' => $paginator->paginate($qb, 20),
            'view_attribute' => EmailVoter::VIEW,
        ]);
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
