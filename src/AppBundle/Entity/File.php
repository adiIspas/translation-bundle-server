<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Model\File as FileModel;
use AppBundle\Manager\FileInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * LexikTranslationFile
 *
 * @ORM\Table(name="lexik_translation_file", uniqueConstraints={@ORM\UniqueConstraint(name="hash_idx", columns={"hash"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 */
class File extends FileModel implements FileInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255, nullable=false)
     */
    protected $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=10, nullable=false)
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="extention", type="string", length=10, nullable=false)
     */
    protected $extention;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     */
    protected $path;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255, nullable=false)
     */
    protected $hash;

    /**
     * {@inheritdoc}
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }
}
