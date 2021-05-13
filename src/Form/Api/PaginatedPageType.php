<?php

declare(strict_types=1);

namespace App\Form\Api;

use App\Dto\Api\Form\PaginatedPageTypeDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class PaginatedPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('page', IntegerType::class, [
            'constraints' => [
                new Positive(),
            ],
        ])->add('limit', ChoiceType::class, [
            'choices' => [
                10,
                15,
                20,
                25,
                50,
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaginatedPageTypeDto::class,
        ]);
    }
}
