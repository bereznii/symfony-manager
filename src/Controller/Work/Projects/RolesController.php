<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects;

use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\UseCase\Projects\Role\Copy;
use App\Model\Work\UseCase\Projects\Role\Create;
use App\Model\Work\UseCase\Projects\Role\Edit;
use App\Model\Work\UseCase\Projects\Role\Remove;
use App\ReadModel\Work\Projects\RoleFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_WORK_MANAGE_PROJECTS')]
#[Route(path: 'work/projects/roles', name: 'work.projects.roles')]
class RolesController extends AbstractController
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * @param RoleFetcher $fetcher
     * @return Response
     */
    #[Route(path: '', name: '')]
    public function index(RoleFetcher $fetcher): Response
    {
        $roles = $fetcher->all();
        $permissions = Permission::names();

        return $this->render('app/work/projects/roles/index.html.twig', compact('roles', 'permissions'));
    }

    /**
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    #[Route(path: '/create', name: '.create')]
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.roles');
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/roles/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Role $role
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    #[Route(path: '/{id}/edit', name: '.edit')]
    public function edit(Role $role, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromRole($role);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.roles.show', ['id' => $role->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/roles/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Role $role
     * @param Request $request
     * @param Copy\Handler $handler
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    #[Route(path: '/{id}/copy', name: '.copy')]
    public function copy(Role $role, Request $request, Copy\Handler $handler): Response
    {
        $command = new Copy\Command($role->getId()->getValue());

        $form = $this->createForm(Copy\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.roles');
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/roles/copy.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name=".delete", methods={"POST"})
     * @param Role $role
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    #[Route(path: '/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(Role $role, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.roles.show', ['id' => $role->getId()]);
        }

        $command = new Remove\Command($role->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.roles');
    }

    /**
     * @Route("/{id}", name=".show")
     * @param Role $role
     * @return Response
     */
    #[Route(path: '/{id}', name: '.show')]
    public function show(Role $role): Response
    {
        return $this->render('app/work/projects/roles/show.html.twig', compact('role'));
    }
}