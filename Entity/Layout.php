<?php
namespace Arnm\PagesBundle\Entity;

use Arnm\CoreBundle\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Arnm\PagesBundle\Entity\Page;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
/**
 * Arnm\PagesBundle\Entity\Layout
 *
 * @ORM\Table(name="layout")
 * @ORM\Entity(repositoryClass="Arnm\PagesBundle\Entity\LayoutRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 */
class Layout extends Entity
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
     * @var string $layout
     *
     * @ORM\Column(name="layout", type="string", length=255)
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     */
    private $layout;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="layout")
     */
    private $pages;

    /**
     * Constractor
     */
    public function __construct()
    {
        $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set layout
     *
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Get layout
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Add pages
     *
     * @param Arnm\PagesBundle\Entity\Page $page
     */
    public function addPage(Page $page)
    {
        $this->pages[] = $page;
    }

    /**
     * Get pages
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getLayout();
    }
}