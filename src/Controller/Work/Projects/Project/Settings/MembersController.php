<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Model\Work\Entity\Employees\Member\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Membership;
use App\Security\Voter\Work\ProjectAccess;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[ParamConverter('project', options: ['id' => 'project_id'])]
#[Route(path: '/work/projects/{project_id}/settings/members', name: 'work.projects.project.settings.members')]
class MembersController extends AbstractController
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * @param Project $project
     * @return Response
     */
    #[Route(path: '', name: '')]
    public function index(Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        return $this->render('app/work/projects/project/settings/members/index.html.twig', [
            'project' => $project,
            'memberships' => $project->getMemberships(),
        ]);
    }

    /**
     * @param Project $project
     * @param Request $request
     * @param Membership\Add\Handler $handler
     * @return Response
     * @throws \Exception
     */
    #[Route(path: '/assign', name: '.assign')]
    public function assign(Project $project, Request $request, Membership\Add\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        if (!$project->getDepartments()) {
            $this->addFlash('error', 'Add departments before adding members.');
            return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
        }

        $command = new Membership\Add\Command($project->getId()->getValue());

        $form = $this->createForm(Membership\Add\Form::class, $command, ['project' => $project->getId()->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/members/assign.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Project $project
     * @param string $member_id
     * @param Request $request
     * @param Membership\Edit\Handler $handler
     * @return Response
     */
    #[Route(path: '/{member_id}/edit', name: '.edit')]
    public function edit(Project $project, string $member_id, Request $request, Membership\Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        $membership = $project->getMembership(new Id($member_id));

        $command = Membership\Edit\Command::fromMembership($project, $membership);

        $form = $this->createForm(Membership\Edit\Form::class, $command, ['project' => $project->getId()->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/members/edit.html.twig', [
            'project' => $project,
            'membership' => $membership,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Project $project
     * @param string $member_id
     * @param Request $request
     * @param Membership\Remove\Handler $handler
     * @return Response
     */
    #[Route(path: '/{member_id}/revoke', name: '.revoke', methods: ['POST'])]
    public function revoke(Project $project, string $member_id, Request $request, Membership\Remove\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        if (!$this->isCsrfTokenValid('revoke', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
        }

        $command = new Membership\Remove\Command($project->getId()->getValue(), $member_id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
    }

    /**
     * @param Project $project
     * @return Response
     */
    #[Route(path: '/{member_id}', name: '.show', methods: ['POST'])]
    public function show(Project $project): Response
    {
        return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
    }
}
