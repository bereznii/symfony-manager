<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\UseCase\Name;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/profile/name')]
class NameController extends AbstractController
{
    /**
     * @param UserFetcher $users
     * @param LoggerInterface $logger
     */
    public function __construct(
        private UserFetcher $users,
        private LoggerInterface $logger
    ) {}

    /**
     * @param Request $request
     * @param Name\Handler $handler
     * @return Response
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    #[Route(path: '', name: 'profile.name')]
    public function request(Request $request, Name\Handler $handler): Response
    {
        $user = $this->users->getDetail($this->getUser()->getId());

        $command = new Name\Command($user->id);
        $command->firstName = $user->first_name;
        $command->lastName = $user->last_name;

        $form = $this->createForm(Name\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('profile');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/profile/name.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}