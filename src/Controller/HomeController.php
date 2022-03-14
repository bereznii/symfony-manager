<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        return $this->render('app/home.html.twig');
    }
}