<?php
namespace Arnm\PagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;

/**
 * Arnm\PagesBundle\Entity\Area
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="Arnm\PagesBundle\Entity\AreaRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 *
 * @UniqueEntity("code")
 */
class Area
{
    use SoftDeleteableEntity;
    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=50)
     * @Gedmo\Versioned
     *
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @var integer $templateId
     *
     * @ORM\Column(name="template_id", type="integer", nullable=true)
     */
    private $templateId;

    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="areas")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false)
     * @Gedmo\Versioned
     */
    private $template;

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
     * Sets the ID
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Set template
     *
     * @param Arnm\PagesBundle\Entity\Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * Get template
     *
     * @return Arnm\PagesBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set templateId
     *
     * @param integer $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * Get templateId
     *
     * @return integer
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Area
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}