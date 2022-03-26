<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Edit;

use App\ReadModel\Work\Projects\Project\DepartmentFetcher;
use App\ReadModel\Work\Projects\RoleFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * @param RoleFetcher $roles
     * @param DepartmentFetcher $departments
     */
    public function __construct(
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
        $builder
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
