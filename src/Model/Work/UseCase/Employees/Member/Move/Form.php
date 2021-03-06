<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Employees\Member\Move;

use App\ReadModel\Work\Employees\GroupFetcher;
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
            ->add('group', Type\ChoiceType::class, [
                'choices' => $this->groups->assoc()
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