<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Model\Translation as TranslationModel;
use AppBundle\Manager\TranslationInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Translations
 *
 * @ORM\Table(name="lexik_trans_unit_translations", uniqueConstraints={@ORM\UniqueConstraint(name="trans_unit_locale_idx", columns={"trans_unit_id", "locale"})}, indexes={@ORM\Index(name="IDX_B0AA394493CB796C", columns={"file_id"}), @ORM\Index(name="IDX_B0AA3944C3C583C9", columns={"trans_unit_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TranslationRepository")
 */
class Translation extends TranslationModel implements TranslationInterface
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
     * @ORM\Column(name="locale", type="string", length=10, nullable=false)
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    protected $content;

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
     * @var \File
     *
     * @ORM\ManyToOne(targetEntity="File", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     * })
     */
    protected $file;

    /**
     * @var \TransUnit
     *
     * @ORM\ManyToOne(targetEntity="TransUnit", inversedBy="translations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="trans_unit_id", referencedColumnName="id")
     * })
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
