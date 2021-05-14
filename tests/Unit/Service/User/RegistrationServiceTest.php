<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\User;

use App\Dto\Api\User\RegisterUserDto;
use App\Entity\User;
use App\Manager\DoctrineManager;
use App\Service\User\RegistrationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationServiceTest extends TestCase
{
    private DoctrineManager $managerMock;
    private UserPasswordEncoderInterface $passwordEncoderMock;

    /**
     * @covers \App\Service\User\RegistrationService::register
     *
     * @dataProvider dataProviderForRegister
     */
    public function testRegister(string $email, string $password)
    {
        $registerUserDto = (new RegisterUserDto())->setEmail($email)
            ->setPassword($password)
            ->setPasswordConfirmation($password)
        ;
        $expectedUser = (new User())->setEmail($email)
            ->setRoles(['ROLE_API', 'ROLE_USER'])
        ;
        $expectedSavedUser = (new User())->setEmail($email)
            ->setRoles(['ROLE_API', 'ROLE_USER'])
            ->setPassword($password)
        ;

        $this->passwordEncoderMock
            ->expects($this->once())
            ->method('encodePassword')
            ->with(...[$expectedUser, $password])
            ->willReturn($password)
        ;

        $this->managerMock
            ->expects($this->once())
            ->method('save')
            ->with(...[$expectedSavedUser])
        ;

        $user = $this->getRegistrationService()->register($registerUserDto);

        $this->assertEquals($expectedSavedUser, $user);
    }

    public function dataProviderForRegister(): array
    {
        return [
            [
                'email' => 'admin@example.com',
                'password' => 'admin@example.com',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->managerMock = $this->createMock(DoctrineManager::class);
        $this->passwordEncoderMock = $this->createMock(UserPasswordEncoderInterface::class);
    }

    protected function getRegistrationService()
    {
        return new RegistrationService(
            $this->managerMock,
            $this->passwordEncoderMock
        );
    }
}