<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SidebarMenu
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
     * @return ItemInterface
     */
    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'nav nav-pills nav-sidebar flex-column',
                'data-widget' => 'treeview',
                'role' => 'menu',
                'data-accordion' => 'false',
            ]);

        $menu->addChild('', ['route' => 'home'])
            ->setExtra('icon', 'nav-icon fas fa-tachometer-alt')
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
            ->setLabel('<p>Dashboard</p>')
            ->setExtra('safe_label',true);

        $menu->addChild('Work')->setAttribute('class', 'nav-header');

        if ($this->auth->isGranted('ROLE_WORK_MANAGE_MEMBERS')) {
            $menu->addChild('Membership', ['route' => 'work.membership.members'])
                ->setExtra('routes', [
                    ['route' => 'work.membership.members'],
                    ['pattern' => '/^work\.membership\..+/']
                ])
                ->setExtra('icon', 'nav-icon fas fa-users')
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link')
                ->setLabel('<p>Membership</p>')
                ->setExtra('safe_label',true);
        }

        $menu->addChild('Control')->setAttribute('class', 'nav-header');

        if ($this->auth->isGranted('ROLE_MANAGE_USERS')) {
            $menu->addChild('Users', ['route' => 'users'])
                ->setExtra('icon', 'nav-icon fas fa-users-cog')
                ->setExtra('routes', [
                    ['route' => 'users'],
                    ['pattern' => '/^users\..+/']
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link')
                ->setLabel('<p>Users</p>')
                ->setExtra('safe_label',true);
        }

        return $menu;
    }
}