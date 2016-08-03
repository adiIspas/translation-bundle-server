<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as FOS;

class DefaultController extends RestController
{
    /**
     * @FOS\View()
     * @FOS\Post("/files/{fileId}")
     *
     * @param $fileId
     * @return Response
     */
    public function postFileAction(ParamFetcherInterface $paramFetcher, $fileId)
    {

        $view = $this->view('My first API.', Response::HTTP_OK);
        return $this->handleView($view);
    }

}
