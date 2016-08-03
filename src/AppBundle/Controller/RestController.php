<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class RestController extends FOSRestController
{
    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        $handler = $this->getHandler();
        $data = $handler->get($id);

        if (null === $data) {
            throw new NotFoundHttpException();
        }
        return $data;
    }
}
