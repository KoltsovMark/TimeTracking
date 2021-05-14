<?php

declare(strict_types=1);

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController.
 */
class BaseController extends AbstractFOSRestController
{
    public function createApiForm(string $typeClass, Request $request, object $object = null): FormInterface
    {
        $data = \json_decode($request->getContent(), true);
        $form = $this->createForm($typeClass, $object);
        $form->submit($data);

        return $form;
    }

    public function createGetForm(string $typeClass, Request $request, object $object = null): FormInterface
    {
        $form = $this->createForm($typeClass, $object);
        $form->submit($request->query->all());

        return $form;
    }

    public function successResponse($data): View
    {
        return $this->view($data, Response::HTTP_OK);
    }

    public function createdResponse($data, string $url): View
    {
        return $this->view($data, Response::HTTP_CREATED, ['Content-Location' => $url]);
    }

    public function failedValidationResponse(FormInterface $form): View
    {
        return $this->view($form, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
