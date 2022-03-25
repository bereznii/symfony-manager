<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Edit;

use App\Model\Work\Entity\Projects\Role\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class)
            ->add('permissions', Type\ChoiceType::class, [
                'choices' => array_combine(Permission::names(), Permission::names()),
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'translation_domain' => 'work_permissions',
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
    }
}
