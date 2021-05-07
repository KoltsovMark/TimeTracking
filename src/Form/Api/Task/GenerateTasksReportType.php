<?php

namespace App\Form\Api\Task;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class GenerateTasksReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_date', DateTimeType::class, [
                'widget' => 'single_text',
            ])->add('end_date', DateTimeType::class, [
                'widget' => 'single_text',
            ])->add('format', ChoiceType::class, [
                'choices' => [
                    'csv' => 'csv',
                    'excel' => 'excel',
                    'pdf' => 'pdf'
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ]);
    }
}
