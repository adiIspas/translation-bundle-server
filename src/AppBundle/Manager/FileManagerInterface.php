<?php

namespace AppBundle\Manager;
use AppBundle\Service\TransUnitService;
use Doctrine\ORM\EntityManager;

/**
 * File manager interface.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface FileManagerInterface
{
    /**
     * Create a new file.
     *
     * @param $name
     * @param $path
     * @param EntityManager $em
     * @return mixed
     */
    public function create($name, $path, EntityManager $em);

    /**
     * Returns a translation file according to the given name and path.
     * 
     * @param $name
     * @param $path
     * @param EntityManager $em
     * @param TransUnitService $transUnitService
     * @return mixed
     */
    public function getFor($name, $path, EntityManager $em, TransUnitService $transUnitService);
}
