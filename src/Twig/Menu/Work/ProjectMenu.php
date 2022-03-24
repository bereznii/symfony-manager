<?php

declare(strict_types=1);

namespace App\Twig\Menu\Work;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjectMenu
{
    /**
     * @param FactoryInterface $factory
     * @param AuthorizationCheckerInterface $auth
     */
    public function __construct(
        private FactoryInterface $factory,
        private AuthorizationCheckerInterface $auth
    ) {}

    /**
     * @param array $options
     * @return ItemInterface
     */
    public function build(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes(['class' => 'nav nav-pills mb-4']);

        $menu
            ->addChild('Dashboard', [
                'route' => 'work.projects.project.show',
                'routeParameters' => ['id' => $options['project_id']]
            ])
            ->setExtra('routes', [
                ['route' => 'work.projects.project.show'],
                ['pattern' => '/^work.projects.project.show\..+/']
            ])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link');

        if ($this->auth->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
            $menu
                ->addChild('Settings', [
                    'route' => 'work.projects.project.settings',
                    'routeParameters' => ['project_id' => $options['project_id']]
                ])
                ->setExtra('routes', [
                    ['route' => 'work.projects.project.settings'],
                    ['pattern' => '/^work.projects.project.settings\..+/']
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
        }

        return $menu;
    }
}