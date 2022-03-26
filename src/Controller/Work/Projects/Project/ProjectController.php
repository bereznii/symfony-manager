<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project;

use App\Model\Work\Entity\Projects\Project\Project;
use App\Security\Voter\Work\ProjectAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/work/projects/project/{id}', name: 'work.projects.project')]
class ProjectController extends AbstractController
{
    /**
     * @param Project $project
     * @return Response
     */
    #[Route(path: '', name: '.show')]
    public function show(Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        return $this->render('app/work/projects/project/show.html.twig', compact('project'));
    }
}