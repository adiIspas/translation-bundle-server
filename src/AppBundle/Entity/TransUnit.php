<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Model\TransUnit as TransUnitModel;
use AppBundle\Manager\TransUnitInterface;
use Doctrine\ORM\Mapping as ORM;


class TransUnit extends TransUnitModel implements TransUnitInterface
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\domain
     * @ORM\Column(name="domain", type="string", nullable=false, options={"unsigned": true})
     */
    protected $domain;

    /**
     * Add translations
     *
     * @param AppBundle\Entity\Translation $translations
     */
    public function addTranslation(\AppBundle\Model\Translation $translation)
    {
        $translation->setTransUnit($this);

        $this->translations[] = $translation;
    }

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
