<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Model\Translation as TranslationModel;
use AppBundle\Manager\TranslationInterface;

class Translation extends TranslationModel implements TranslationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var AppBundle\Entity\TransUnit
     */
    protected $transUnit;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transUnit
     *
     * @param AppBundle\Entity\TransUnit $transUnit
     */
    public function setTransUnit(\AppBundle\Model\TransUnit $transUnit)
    {
        $this->transUnit = $transUnit;
    }

    /**
     * Get transUnit
     *
     * @return AppBundle\Entity\TransUnit
     */
    public function getTransUnit()
    {
        return $this->transUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist()
    {
        $now             = new \DateTime("now");
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }
}
