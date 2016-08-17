<?php
/**
 * Created by PhpStorm.
 * User: adrian.ispas
 * Date: 8/4/2016
 * Time: 11:12 AM
 */

namespace AppBundle\Service;


use AppBundle\Repository\TransUnitRepository;
use AppBundle\Storage\AbstractDoctrineStorage;
use AppBundle\Storage\DoctrineORMStorage;
use AppBundle\Util\DataGrid\DataGridRequestHandler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\DateTime;

class TransUnitService
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * TransUnitService constructor.
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Return total number of translations for each domain
     * @return mixed
     */
    public function countByDomains()
    {
        $repository = $this->getRepository('TransUnit');
        $totalTranslations = $repository->countByDomains();

        return $totalTranslations;
    }

    /**
     * Return total number of translations for each locale in every domain
     * @param $domain
     * @return mixed
     */
    public function getCountTranslationByLocales($domain)
    {
        $repository = $this->getRepository('TransUnit');
        $totalTranslationsLocale = $repository->getCountTranslationByLocales($domain);

        return $totalTranslationsLocale;
    }

    /**
     * Return all locales
     * @return mixed
     */
    public function getLocales()
    {
        $repository = $this->getRepository('File');
        $locales = $repository->getLocales();

        return $locales;
    }

    /**
     * Return all domains
     * @return mixed
     */
    public function getDomains()
    {
        $repository = $this->getRepository('File');
        $domains = $repository->getDomains();

        return $domains;
    }

    /**
     * Return lastest updated date
     * @return \DateTime|null
     */
    public function getLatestTranslationUpdatedAt()
    {
        $repository = $this->getRepository('Translation');
        $latestUpdated = $repository->getLatestTranslationUpdatedAt();

        return $latestUpdated;
    }

    /**
     * Return all translations
     * @return mixed
     */
    public function getAllTranslations(array $locales = null, $rows = 20, $page = 1, array $filters = null)
    {
        $repository = $this->getRepository('TransUnit');
        $translations = $repository->getTransUnitList($locales,$rows,$page,$filters);

        return $translations;
    }

    /**
     * Return total number of translations
     * @return mixed
     */
    public function count()
    {
        $repository = $this->getRepository('TransUnit');
        $counts = $repository->count();

        return $counts;
    }

    /**
     * Get translation by id
     * @param $id
     * @return mixed
     */
    public function getTransUnitById($id)
    {
        $repository = $this->getRepository('TransUnit');
        $transUnit = $repository->findById($id);

        return $transUnit;
    }

    /**
     * Get file by id
     * @param $id
     * @return mixed
     */
    public function getFileById($id)
    {
        $repository = $this->getRepository('File');
        $file = $repository->findById($id);

        return $file;
    }

    /**
     * Get file by hash
     * @param $hash
     * @return mixed
     */
    public function getFileByHash($hash)
    {
        $repository = $this->getRepository('File');
        $file = $repository->findOneBy(array('hash' => $hash));

        return $file;
    }

    /**
     * Find TransUnit by criteria array
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria)
    {
        $repository = $this->getRepository('TransUnit');
        return $repository->findBy($criteria);
    }

    /**
     * Find files from locales and domains
     * @param $locales
     * @param $domains
     * @return mixed
     */
    public function findForLocalesAndDomains($locales, $domains)
    {
        $repository = $this->getRepository('File');

        if(empty($locales))
            $locales = $this->getLocales();

        if(empty($domains))
            $domains = $this->getDomains();
        
        return $repository->findForLocalesAndDomains($locales,$domains);
    }

    /**
     * Get translations for file
     * @param ModelFile $file
     * @param $onlyUpdated
     * @return mixed
     */
    public function getTranslationsForFile($file, $onlyUpdated)
    {
        $repository = $this->getRepository('TransUnit');
        return $repository->getTranslationsForFile($file,$onlyUpdated);
    }
    
    /**
     * Get specify repository for database
     * @param $repositoryName
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRepository($repositoryName)
    {
        $em = $this->doctrine->getManager();
        $repository = $em->getRepository('AppBundle:'.$repositoryName);

        return $repository;
    }
}