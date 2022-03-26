<?php

declare(strict_types=1);

namespace App\Controller\Work\Employees;

use App\Model\Work\Entity\Employees\Group\Group;
use App\Model\Work\UseCase\Employees\Group\Create;
use App\Model\Work\UseCase\Employees\Group\Edit;
use App\Model\Work\UseCase\Employees\Group\Remove;
use App\ReadModel\Work\Employees\GroupFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_WORK_MANAGE_MEMBERS')]
#[Route(path: '/work/employees/groups', name: 'work.employees.groups')]
class GroupsController extends AbstractController
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * @param GroupFetcher $fetcher
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route(path: '', name: '')]
    public function index(GroupFetcher $fetcher): Response
    {
        $groups = $fetcher->all();

        return $this->render('app/work/employees/groups/index.html.twig', compact('groups'));
    }

    /**
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
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
                return $this->redirectToRoute('work.employees.groups');
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/employees/groups/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Group $group
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    #[Route(path: '/{id}/edit', name: '.edit')]
    public function edit(Group $group, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromGroup($group);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.employees.groups.show', ['id' => $group->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/employees/groups/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Group $group
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    #[Route(path: '/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(Group $group, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.employees.groups.show', ['id' => $group->getId()]);
        }

        $command = new Remove\Command($group->getId()->getValue());

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.employees.groups');
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.employees.groups.show', ['id' => $group->getId()]);
    }

    /**
     * @return Response
     */
    #[Route(path: '/{id}', name: '.show')]
    public function show(): Response
    {
        return $this->redirectToRoute('work.employees.groups');
    }
}