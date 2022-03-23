<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Membership\Member\Create;

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
     * @throws \Doctrine\DBAL\Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('group', Type\ChoiceType::class, ['choices' => array_column($this->groups->all(), 'id', 'name')])
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class)
            ->add('email', Type\EmailType::class);
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