<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Membership\Member\Filter;

use App\Model\Work\Entity\Membership\Member\Status;
use App\ReadModel\Work\Membership\GroupFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * @param GroupFetcher $groups
     */
    public function __construct(
        private GroupFetcher $groups
    ) {}

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Name',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('email', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Email',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('group', Type\ChoiceType::class, [
                'choices' => $this->groups->assoc(),
                'required' => false,
                'placeholder' => 'All groups',
                'attr' => ['onchange' => 'this.form.submit()']
            ])
            ->add('status', Type\ChoiceType::class, ['choices' => [
                'Active' => Status::ACTIVE,
                'Archived' => Status::ARCHIVED,
            ], 'required' => false, 'placeholder' => 'All statuses', 'attr' => ['onchange' => 'this.form.submit()']]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}