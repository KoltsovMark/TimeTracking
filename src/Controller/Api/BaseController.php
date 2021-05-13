<?php

declare(strict_types=1);

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

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
}
