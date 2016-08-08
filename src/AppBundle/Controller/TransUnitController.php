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
use FOS\RestBundle\Controller\Annotations\RequestParam;


class TransUnitController extends RestController
{
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

    /**
     * Get all domains
     *
     * @FOS\View()
     * @FOS\Get("/domains")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return mixed
     */
    public function getDomainsAction(ParamFetcherInterface $paramFetcher)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        return $transUnitService->getDomains();
    }

    /**
     * Get latest updated translation
     *
     * @FOS\View()
     * @FOS\Get("/latest_updated")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return \DateTime|null
     */
    public function getLatestTranslationUpdatedAtAction(ParamFetcherInterface $paramFetcher)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        return $transUnitService->getLatestTranslationUpdatedAt();
    }

    /**
     * Get all translations
     *
     * @RequestParam(name="locales", requirements="\w+", nullable=true, allowBlank=true, description="Locales")
     *
     * @FOS\View()
     * @FOS\Post("/all_translations")
     *
     * @return mixed
     */
    public function postAllTranslationsAction(Request $request)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');

        $requestParams = $request->request->all();

        $locales = $requestParams['locales'];
        $rows = $requestParams['rows'];
        $page = $requestParams['page'];
        $filters = $requestParams['filters'];

        return $transUnitService->getAllTranslations($locales, $rows, $page, $filters);
    }

    /**
     * Return number of translations
     * @FOS\View()
     * @FOS\Get("/count")
     * @return mixed
     */
    public function getCountAction()
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        return $transUnitService->count();
    }


}
