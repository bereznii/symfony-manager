<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Add;

use App\ReadModel\Work\Employees\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Project\DepartmentFetcher;
use App\ReadModel\Work\Projects\RoleFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * @param MemberFetcher $members
     * @param RoleFetcher $roles
     * @param DepartmentFetcher $departments
     */
    public function __construct(
        private MemberFetcher $members,
        private RoleFetcher $roles,
        private DepartmentFetcher $departments
    ) {}

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $members = [];
        foreach ($this->members->activeGroupedList() as $item) {
            $members[$item['group']][$item['name']] = $item['id'];
        }

        $builder
            ->add('member', Type\ChoiceType::class, [
                'choices' => $members,
            ])
            ->add('departments', Type\ChoiceType::class, [
                'choices' => $this->departments->listOfProject($options['project']),
                'expanded' => true,
                'multiple' => true,
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input'];
                },
            ])
            ->add('roles', Type\ChoiceType::class, [
                'choices' => $this->roles->allList(),
                'expanded' => true,
                'multiple' => true,
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input'];
                },
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
        $resolver->setRequired(['project']);
    }
}
