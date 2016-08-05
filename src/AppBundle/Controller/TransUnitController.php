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


        return $transUnitService->countByDomains();
    }

    /**
     * Return total number of translations for each domain
     *
     * @FOS\View()
     * @FOS\Get("/count/domains")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return mixed
     */
    public function getCountByDomainsAction(ParamFetcherInterface $paramFetcher)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        return $transUnitService->countByDomains();
    }

    /**
     * Return total number of translations for each locale in every domain
     *
     * @FOS\View()
     * @FOS\Get("/count/{domain}")
     *
     * @return mixed
     */
    public function getCountTranslationByLocalesAction(ParamFetcherInterface $paramFetcher, $domain)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        return $transUnitService->getCountTranslationByLocales($domain);
    }

    /**
     * Get all locales
     *
     * @FOS\View()
     * @FOS\Get("/locales")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return mixed
     */
    public function getLocalesAction(ParamFetcherInterface $paramFetcher)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        return $transUnitService->getLocales();
    }

}
