<?php

namespace AppBundle\Manager;

use AppBundle\Entity\File;
use AppBundle\Service\TransUnitService;
use AppBundle\Storage\StorageInterface;
use Doctrine\ORM\EntityManager;

/**
 * Manager for translations files.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Nikola Petkanski <nikola@petkanski.com>
 */
class FileManager implements FileManagerInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $rootDir;

//    /**
//     * Construct.
//     *
//     * @param StorageInterface $storage
//     * @param string           $rootDir
//     */
//    public function __construct(StorageInterface $storage, $rootDir)
//    {
//        $this->storage = $storage;
//        $this->rootDir = $rootDir;
//    }

    /**
     * {@inheritdoc}
     */
    public function getFor($name, $path, EntityManager $em, TransUnitService $transUnitService)
    {
        $hash = $this->generateHash($name, $this->getFileRelativePath($path));
        $file = $transUnitService->getFileByHash($hash);

        if (!($file instanceof FileInterface)) {
            $file = $this->create($name, $path, $em);
        }

        file_put_contents("add.txt",print_r($file,true));
        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $path, EntityManager $em)
    {
        $path = $this->getFileRelativePath($path);

        $file = new File();
        $file->setName($name);
        $file->setPath($path);
        $file->setHash($this->generateHash($name, $path));
    
        $em->persist($file);
        $em->flush();

        return $file;
    }

    /**
     * Returns the has for the given file.
     *
     * @param string $name
     * @param string $relativePath
     * @return string
     */
    protected function generateHash($name, $relativePath)
    {
        return md5($relativePath.DIRECTORY_SEPARATOR.$name);
    }

    /**
     * Returns the relative according to the kernel.root_dir value.
     *
     * @param string $filePath
     * @return string
     */
    protected function getFileRelativePath($filePath)
    {
        $filePath = "Resources/translations";
        return $filePath;
    }
}






