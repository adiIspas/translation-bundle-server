<?php

namespace AppBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use AppBundle\Model\File as ModelFile;
use AppBundle\Entity\Translation;


class TransUnitRepository extends EntityRepository
{
    /**
     * Returns all domain available in database.
     *
     * @return array
     */
    public function getAllDomainsByLocale()
    {
        return $this->createQueryBuilder('tu')
            ->select('te.locale, tu.domain')
            ->leftJoin('tu.translations', 'te')
            ->addGroupBy('te.locale')
            ->addGroupBy('tu.domain')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Returns all domains for each locale.
     *
     * @return array
     */
    public function getAllByLocaleAndDomain($locale, $domain)
    {
        return $this->createQueryBuilder('tu')
            ->select('tu, te')
            ->leftJoin('tu.translations', 'te')
            ->where('tu.domain = :domain')
            ->andWhere('te.locale = :locale')
            ->setParameter('domain', $domain)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Returns all trans unit with translations for the given domain and locale.
     *
     * @return array
     */
    public function getAllDomains()
    {
        $this->loadCustomHydrator();

        return $this->createQueryBuilder('tu')
            ->select('DISTINCT tu.domain')
            ->orderBy('tu.domain', 'ASC')
            ->getQuery()
            ->getResult('SingleColumnArrayHydrator');
    }

    /**
     * Returns some trans units with their translations.
     *
     * @param array $locales
     * @param int $rows
     * @param int $page
     * @param array $filters
     * @return array
     */
    public function getTransUnitList(array $locales = null, $rows = 20, $page = 1, array $filters = null)
    {
        $this->loadCustomHydrator();

        $sortColumn = isset($filters['sidx']) ? $filters['sidx'] : 'id';
        $order = isset($filters['sord']) ? $filters['sord'] : 'ASC';

        $builder = $this->createQueryBuilder('tu')
            ->select('tu.id');

        $this->addTransUnitFilters($builder, $filters);
        $this->addTranslationFilter($builder, $locales, $filters);

        $ids = $builder->orderBy(sprintf('tu.%s', $sortColumn), $order)
            ->setFirstResult($rows * ($page - 1))
            ->setMaxResults($rows)
            ->getQuery()
            ->getResult('SingleColumnArrayHydrator');

        $transUnits = array();

        if (count($ids) > 0) {
            $qb = $this->createQueryBuilder('tu');

            $transUnits = $qb->select('tu, te')
                ->leftJoin('tu.translations', 'te')
                ->andWhere($qb->expr()->in('tu.id', $ids))
                ->andWhere($qb->expr()->in('te.locale', $locales))
                ->orderBy(sprintf('tu.%s', $sortColumn), $order)
                ->getQuery()
                ->getArrayResult();
        }

        foreach($transUnits as &$transUnit){
            $transUnit['key'] = $transUnit['keyName'];
            unset($transUnit['keyName']);
        }

        return $transUnits;
    }

    /**
     * Count the number of trans unit. - WORK with non param
     *
     * @param array $locales
     * @param array $filters
     * @return int
     */
    public function count(array $locales = null, array $filters = null)
    {
        $this->loadCustomHydrator();

        $builder = $this->createQueryBuilder('tu')
            ->select('COUNT(DISTINCT tu.id) AS number');

        $this->addTransUnitFilters($builder, $filters);
        $this->addTranslationFilter($builder, $locales, $filters);

        return (int)$builder->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @return array
     */
    public function countByLocaleAndDomain()
    {
        $qb = $this->createQueryBuilder('tu');

        $counts = $qb->select('tu.domain', 'ts.locale', 'count(ts.locale) as total')
            ->join('AppBundle:Translation', 'ts')
            ->where('tu.id = ts.transUnit')
            ->groupBy('ts.locale')
            ->orderBy('tu.domain')
            ->getQuery()
            ->getArrayResult();

        return $counts;
    }

    /**
     * Return total number of translations for each domain - WORK (IN USE)
     *
     * @return array
     */
    public function countByDomains()
    {
        $response = $this->createQueryBuilder('tu')
            ->select('COUNT(DISTINCT tu.id) AS number, tu.domain')
            ->groupBy('tu.domain')
            ->getQuery()
            ->getArrayResult();

        $counts = array();
        foreach ($response as $domain) {
            $counts[$domain['domain']] = (int)$domain['number'];
        }

        return $counts;
    }

    /**
     * Return all translations for each locales by one domain - WORK (IN USE)
     *
     * @param $domain
     * @return array
     */
    public function getCountTranslationByLocales($domain)
    {
        $qb = $this->createQueryBuilder('tu');

        $translationsByLocales = $qb->select('tu.domain', 'ts.locale', 'count(ts.locale) as total')
            ->join('AppBundle:Translation', 'ts')
            ->where('tu.id = ts.transUnit')
            ->andWhere('tu.domain = :domain')
            ->setParameter('domain', $domain)
            ->groupBy('ts.locale')
            ->getQuery()
            ->getArrayResult();

        $counts = array();
        foreach ($translationsByLocales as $row) {
            $counts[$row['locale']] = (int)$row['total'];
        }

        return $counts;
    }

    /**
     * Returns all translations for the given file.
     *
     * @param ModelFile $file
     * @param boolean $onlyUpdated
     * @return array
     */
    public function getTranslationsForFile($file, $onlyUpdated)
    {
        $builder = $this->createQueryBuilder('tu')
            ->select('tu.keyName, te.content')
            ->leftJoin('tu.translations', 'te')
            ->where('te.file = :file')
            ->setParameter('file', $file->getId())
            ->orderBy('te.id', 'asc');

        if ($onlyUpdated) {
            $builder->andWhere($builder->expr()->gt('te.updatedAt', 'te.createdAt'));
        }

        $results = $builder->getQuery()->getArrayResult();

        $translations = array();
        foreach ($results as $result) {
            $translations[$result['keyName']] = $result['content'];
        }

        return $translations;
    }

    /**
     * Add conditions according to given filters.
     *
     * @param QueryBuilder $builder
     * @param array        $filters
     */
    protected function addTransUnitFilters(QueryBuilder $builder, array $filters = null)
    {
        if (isset($filters['_search']) && $filters['_search']) {
            if (!empty($filters['domain'])) {
                $builder->andWhere($builder->expr()->like('tu.domain', ':domain'))
                    ->setParameter('domain', sprintf('%%%s%%', $filters['domain']));
            }

            if (!empty($filters['key'])) {
                $builder->andWhere($builder->expr()->like('tu.key', ':key'))
                    ->setParameter('key', sprintf('%%%s%%', $filters['key']));
            }
        }
    }

    /**
     * Add conditions according to given filters.
     *
     * @param QueryBuilder $builder
     * @param array        $locales
     * @param array        $filters
     */
    protected function addTranslationFilter(QueryBuilder $builder, array $locales = null, array $filters = null)
    {
        if (null !== $locales) {
            $qb = $this->createQueryBuilder('tu');

            $qb->select('DISTINCT tu.id')
                ->leftJoin('AppBundle:Translation', 't')
                ->where($qb->expr()->in('t.locale', $locales));

            foreach ($locales as $locale) {
                if (!empty($filters[$locale])) {
                    $qb->andWhere($qb->expr()->like('t.content', ':content'))
                        ->setParameter('content', sprintf('%%%s%%', $filters[$locale]));

                    $qb->andWhere($qb->expr()->eq('t.locale', ':locale'))
                        ->setParameter('locale', sprintf('%s', $locale));
                }
            }

            $ids = $qb->getQuery()->getResult('SingleColumnArrayHydrator');

            if (count($ids) > 0) {
                $builder->andWhere($builder->expr()->in('tu.id', $ids));
            }
        }
    }

    /**
     * Load custom hydrator.
     */
    protected function loadCustomHydrator()
    {
        $config = $this->getEntityManager()->getConfiguration();
        $config->addCustomHydrationMode('SingleColumnArrayHydrator', 'AppBundle\Util\Doctrine\SingleColumnArrayHydrator');
    }
}
