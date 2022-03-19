<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
    /**
     * @param UserFetcher $users
     */
    public function __construct(
        private UserFetcher $users
    ) {}

    /**
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route(path: '/profile', name: 'profile')]
    public function show(): Response
    {
        $user = $this->users->findDetail($this->getUser()->getId());
        return $this->render('app/profile/show.html.twig', compact('user'));
    }
}