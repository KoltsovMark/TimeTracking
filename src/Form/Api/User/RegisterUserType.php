<?php

declare(strict_types=1);

namespace App\Form\Api\User;

use App\Dto\Api\User\RegisterUserDto;
use App\Entity\User;
use App\Service\User\UserService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterUserType extends AbstractType
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                        new Length(['max' => User::EMAIL_MAX_LENGTH]),
                    ],
                ]
            )
            ->add(
                'password', PasswordType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegisterUserDto::class,
            'csrf_protection' => false,
            'constraints' => [
                new Callback([$this, 'validate']),
            ],
        ]);
    }

    public function validate(RegisterUserDto $registerUserDto, ExecutionContextInterface $context): void
    {
        if ($this->userService->isEmailExist($registerUserDto->getEmail())) {
            $context->buildViolation('Email already in use')
                ->atPath('email')
                ->addViolation();
        }
    }
}
