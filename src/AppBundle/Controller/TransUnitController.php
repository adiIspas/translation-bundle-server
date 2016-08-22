<?php

namespace AppBundle\Controller;

use AppBundle\Entity\File;
use AppBundle\Entity\Translation;
use AppBundle\Entity\TransUnit;
use AppBundle\Manager\FileInterface;
use AppBundle\Manager\FileManager;
use AppBundle\Translation\Importer\FileImporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as FOS;
use AppBundle\Service\TransUnitService;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use AppBundle\Manager\TransUnitManager;


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
    public function getCountAction(ParamFetcherInterface $paramFetcher)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        return $transUnitService->count();
    }

    /**
     * Get translation by id
     * @FOS\View()
     * @FOS\Get("/find_by_id/{id}")
     * @return mixed
     */
    public function getTransUnitByIdAction(ParamFetcherInterface $paramFetcher,$id)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        return $transUnitService->getTransUnitById($id);
    }


    /**
     * Update translation
     * @FOS\View()
     * @FOS\Post("/update")
     * @param Request $request
     * @return mixed
     */
    public function postUpdateFromRequestAction(Request $request)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');

        $requestParams = $request->request->all();
        $id = $requestParams['id'];
        $locale = $requestParams['locale'];
        $content = $requestParams['content'];

        $transUnitResponse = $transUnitService->getTransUnitById($id);
        $transUnit = $transUnitResponse[0];

        $translation = $transUnit->getTranslation($locale);
        $translation->setContent($content);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $translation;
    }

    /**
     * Update translation
     * @FOS\View()
     * @FOS\Post("/add_translation_content")
     * @param Request $request
     * @return mixed
     */
    public function postAddTranslationContentAction(Request $request)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');

        $requestParams = $request->request->all();
        $id = $requestParams['id'];
        $locale = $requestParams['locale'];
        $content = $requestParams['content'];
        $fileId = $requestParams['idFile'];

        $transUnitResponse = $transUnitService->getTransUnitById($id);
        $transUnit = $transUnitResponse[0];

        $translation = new Translation();
        $translation->setLocale($locale);
        $translation->setContent($content);
        $translation->setTransUnit($transUnit);

        $file = $transUnitService->getFileById($fileId);

        $translation->setFile($file[0]);
        $transUnit->addTranslation($translation);

        $em = $this->getDoctrine()->getManager();
        $em->persist($transUnit);
        $em->flush();

        return $translation;
    }

    /**
     * Add new translation
     * @FOS\View()
     * @FOS\Post("/add_new_translation")
     * @param Request $request
     * @return bool
     */
    public function postAddNewTranslationAction(Request $request)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        $requestParams = $request->request->all();

        $em = $this->getDoctrine()->getManager();
        $fileManager = new FileManager();

        $key = $requestParams['key'];
        $domain = $requestParams['domain'];
        $locales = $requestParams['locales'];
        $rootDir = $requestParams['rootDir'];

        $transUnit = new TransUnit();
        $transUnit->setKey($key);
        $transUnit->setDomain($domain);

        foreach ($locales as $locale => $content) {
            $translation = new Translation();
            $translation->setTransUnit($transUnit);
            $translation->setLocale($locale);
            $translation->setContent($content);

            $file = $fileManager->getFor(sprintf('%s.%s.yml', $transUnit->getDomain(), $translation->getLocale()), $rootDir.'/Resources/translations', $em, $transUnitService);

            if ($file instanceof FileInterface) {
                $translation->setFile($file);
            }

            $transUnit->addTranslation($translation);
        }

        $em->persist($transUnit);
        $em->flush();

        return $transUnit;
    }

    /**
     * Find TransUnit by criteria array
     * @FOS\View()
     * @FOS\Post("/find_by")
     * @param Request $request
     * @return mixed
     */
    public function postFindByAction(Request $request)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        $requestParams = $request->request->all();

        $criteria = array();
        $criteria['keyName'] = $requestParams['key'];
        $criteria['domain'] = $requestParams['domain'];

        return $transUnitService->findBy($criteria);
    }

    /**
     * @param Request $request
     * @FOS\View()
     * @FOS\Post("/get_file")
     * @return File|mixed
     */
    public function postGetFileAction(Request $request)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');
        $requestParams = $request->request->all();

        $name = $requestParams['name'];
        $path = $requestParams['path'];

        $em = $this->getDoctrine()->getManager();
        $fileManager = new FileManager();

        $file = $fileManager->getFor($name, $path, $em, $transUnitService);

        return $file;
    }

    /**
     * @param Request $request
     * @FOS\View()
     * @FOS\Post("/find_for_locales_and_domains")
     * @return mixed
     */
    public function postFindForLocalesAndDomainsAction(Request $request)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');

        $requestParams = $request->request->all();
        $locales = $requestParams['locales'];
        $domains = $requestParams['domains'];

        $files = $transUnitService->findForLocalesAndDomains($locales, $domains);
        return $files;
    }

    /**
     * @param Request $request
     * @FOS\View()
     * @FOS\Post("/get_translations_for_file")
     * @return mixed
     */
    public function postGetTranslationsForFileAction(Request $request)
    {
        $transUnitService = $this->container->get('app_bundle.service.trans_unit');

        $requestParams = $request->request->all();
        $id = $requestParams['id'];
        $locale = $requestParams['locale'];
        $domain = $requestParams['domain'];
        $extention = $requestParams['extention'];
        $path = $requestParams['path'];
        $hash = $requestParams['hash'];
        $onlyUpdated = ($requestParams['onlyUpdated'] == 'true' ? true : false );

        $file = new File();
        $file->setId($id);
        $file->setLocale($locale);
        $file->setDomain($domain);
        $file->setExtention($extention);
        $file->setPath($path);
        $file->setHash($hash);
        
        $translations = $transUnitService->getTranslationsForFile($file, $onlyUpdated);


        return $translations;
    }


    /**
     * @param Request $request
     * @FOS\View()
     * @FOS\Post("/import_translations")
     * @return string
     */
    public function postImportTranslationsAction(Request $request)
    {
        /** @var FileImporter $fileImporter */
        $fileImporter = $this->container->get('app_bundle.importer.file');
        $requestParams = $request->request->all();

        $relativePath = $requestParams['relativePath'];
        $relativePathname = $requestParams['relativePathname'];
        $pathName = $requestParams['pathName'];
        $fileName = $requestParams['fileName'];

        $fileInfo = new SplFileInfo($fileName,$relativePath,$relativePathname);

        return $fileImporter->import($fileInfo, false, false);

        //return 1;
    }
}
