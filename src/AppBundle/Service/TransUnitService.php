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
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

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

    public function getTranslation($translationId)
    {
        $files = array();
        $files['id'] = $translationId;
        $files['key'] = 'translations.key';
        $files['locale'] = array();
        $files['locale']['ro'] = 'cheie';
        $files['locale']['en'] = 'key';

        $em = $this->doctrine->getManager();
        $repository = $em->getRepository('AppBundle:TransUnit');

        $transUnits = $repository->countByDomains();
        //$transUnits = $repository->countByDomains();
        //$transUnits = $repository->findAll();
        
        return $transUnits;
    }

    /**
     * Return total number of translations for each domain
     * @return mixed
     */
    public function countByDomains()
    {
        $repository = $this->getRepository();
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
        $repository = $this->getRepository();
        $totalTranslations = $repository->getCountTranslationByLocales($domain);

        return $totalTranslations;
    }

    /**
     * Get repository for database
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRepository()
    {
        $em = $this->doctrine->getManager();
        $repository = $em->getRepository('AppBundle:TransUnit');

        return $repository;
    }
}