<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Controller\Api\BaseController;
use App\Dto\Api\User\RegisterUserDto;
use App\Entity\User;
use App\Form\Api\User\RegisterUserType;
use App\Service\User\RegistrationService;
use Nelmio\ApiDocBundle\Annotation as SWG;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegisterController extends BaseController
{
    private RegistrationService $registrationService;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(RegistrationService $registrationService, UrlGeneratorInterface $urlGenerator)
    {
        $this->registrationService = $registrationService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("users/register", name="users_register", methods={"POST"})
     *
     * @OA\Post(
     *     tags={"Users"},
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref=@SWG\Model(type=RegisterUserType::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created",
     *         @SWG\Model(type=User::class)
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="code", type="integer", example=400),
     *                 @OA\Property(property="message", type="string", example="Validation Failed"),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                         property="children",
     *                         type="object",
     *                         @OA\Property(
     *                             property="email",
     *                             type="array",
     *                             @OA\Items(example="This value is required")
     *                         ),
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $form = $this->createApiForm(RegisterUserType::class, $request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->failedValidationResponse($form);
        }

        /**
         * @var RegisterUserDto $registerUserTypeDto
         */
        $registerUserTypeDto = $form->getData();
        $user = $this->registrationService->register($registerUserTypeDto);

        $url = $this->urlGenerator->generate('api_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->createdResponse($user, $url);
    }
}
