<?php

declare(strict_types=1);

namespace App\Form\Api\Task;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class CreateTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 255]),
            ],
        ])->add('comment', TextType::class, [
            'constraints' => [
                new Length(['max' => 10000]),
            ],
        ])->add('time_spent', IntegerType::class, [
            'constraints' => [
                new NotBlank(),
                new Positive(),
                new Range(['max' => 4294967295]),
            ],
        ])->add('date', DateTimeType::class, [
            'widget' => 'single_text',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }
}
