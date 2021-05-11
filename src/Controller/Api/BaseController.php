<?php

declare(strict_types=1);

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseController
 * @package App\Controller\Api
 */
class BaseController extends AbstractFOSRestController
{
    /**
     * @param string $typeClass
     * @param Request $request
     * @param object|null $object
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createApiForm(string $typeClass, Request $request, object $object = null)
    {
        $data = \json_decode($request->getContent(), true);
        $form = $this->createForm($typeClass, $object);
        $form->submit($data);

        return $form;
    }
}