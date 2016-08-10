<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Model\TransUnit as TransUnitModel;
use AppBundle\Manager\TransUnitInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * LexikTransUnit
 *
 * @ORM\Table(name="lexik_trans_unit", uniqueConstraints={@ORM\UniqueConstraint(name="key_domain_idx", columns={"key_name", "domain"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransUnitRepository")
 */
class TransUnit extends TransUnitModel implements TransUnitInterface
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
     * @ORM\Column(name="key_name", type="string", length=255, nullable=false)
     */
    protected $keyName;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255, nullable=false)
     */
    protected $domain;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var \Translation
     * @ORM\OneToMany(targetEntity="Translation", mappedBy="transUnit", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="trans_unit_id")
     * })
     */
    protected $translations;

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

    public function setKey($key)
    {
        $this->keyName = $key;
    }

    public function getKey()
    {
        return $this->keyName;
    }
}
