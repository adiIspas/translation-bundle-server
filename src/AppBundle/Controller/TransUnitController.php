<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TransUnit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as FOS;
use AppBundle\Service\TransUnitService;

class TransUnitController extends RestController
{
    /**
     * @FOS\View()
     * @FOS\Post("/translations/{translationId}")
     *
     * @param $translationId
     * @return Response
     */
    public function postFileAction(ParamFetcherInterface $paramFetcher, $translationId)
    {

//        $view = $this->view('My first API with id ' . $fileId, Response::HTTP_OK);
//        return $this->handleView($view);

//        $param = $paramFetcher->get('id');

        $transUnitService = $this->container->get('app_bundle.service.trans_unit');


        return $transUnitService->getTranslation($translationId);
    }

}
