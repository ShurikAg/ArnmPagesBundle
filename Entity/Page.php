<?php

namespace Arnm\PagesBundle\Entity;

use Gedmo\Tree\Node;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Doctrine\ORM\Mapping as ORM;
use Arnm\CoreBundle\Entity\Entity;
use Arnm\WidgetBundle\Entity\Widget;

/**
 * Arnm\PagesBundle\Entity\Page
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="Arnm\PagesBundle\Entity\PageRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 */
class Page extends Entity implements Node
{
    use SoftDeleteableEntity;
    use TimestampableEntity;
    use BlameableEntity;

    const STATUS_DTAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $parentId
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     * min=3,
     * minMessage="Title must be at least {{ limit }} characters."
     * )
     */
    private $title;
    /**
     * @var string $slug
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Gedmo\Versioned
     *
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\Length(
     * min=3,
     * minMessage="Slug must be at least {{ limit }} characters."
     * )
     */
    private $slug;
    /**
     * @var string $pathSlug
     *
     * @ORM\Column(name="path_slug", type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $pathSlug;
    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=160, nullable=true)
     * @Gedmo\Versioned
     *
     * @Assert\Length(
     * min=3,
     * max=160,
     * minMessage="Description must have at least {{ limit }} characters."
     * )
     */
    private $description;
    /**
     * @var string $keywords
     *
     * @ORM\Column(name="keywords", type="string", length=160, nullable=true)
     * @Gedmo\Versioned
     *
     * @Assert\Length(
     * min=3,
     * max=160,
     * minMessage="Keywords must have at least {{ limit }} characters."
     * )
     */
    private $keywords;
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     * @Gedmo\Versioned
     */
    private $lft;
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     * @Gedmo\Versioned
     */
    private $lvl;
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     * @Gedmo\Versioned
     */
    private $rgt;
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     * @Gedmo\Versioned
     */
    private $root;
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     * @Gedmo\Versioned
     */
    private $parent;
    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;
    /**
     * @ORM\ManyToOne(targetEntity="Layout", inversedBy="pages", fetch="EAGER")
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id", nullable=true)
     * @Gedmo\Versioned
     */
    private $layout;
    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="pages", fetch="EAGER")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=true)
     * @Gedmo\Versioned
     */
    private $template;

    /**
     * @ORM\Column(name="status", type="string")
     * @Gedmo\Versioned
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Arnm\WidgetBundle\Entity\Widget", mappedBy="page")
     * @ORM\OrderBy({"sequence" = "ASC"})
     */
    private $widgets;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->widgets = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Set keywords
     *
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }
    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
    /**
     * Set lft
     *
     * @param integer $lft
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }
    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }
    /**
     * Set lvl
     *
     * @param integer $lvl
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    }
    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }
    /**
     * Set rgt
     *
     * @param integer $rgt
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }
    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }
    /**
     * Set root
     *
     * @param integer $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }
    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }
    /**
     * Set parent
     *
     * @param Arnm\PagesBundle\Entity\Page $parent
     */
    public function setParent(Page $parent)
    {
        $this->parent = $parent;
    }
    /**
     * Get parent
     *
     * @return Arnm\PagesBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * Add children
     *
     * @param Page $child
     */
    public function addPage(Page $child)
    {
        $this->children[] = $child;
    }
    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Retuns string that represents the page.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set pathSlug
     *
     * @param string $pathSlug
     */
    public function setPathSlug($pathSlug)
    {
        $this->pathSlug = $pathSlug;
    }

    /**
     * Get pathSlug
     *
     * @return string
     */
    public function getPathSlug()
    {
        return $this->pathSlug;
    }

    /**
     * Determines if the node is a root
     *
     * @return boolean
     */
    public function isRoot()
    {
        return ($this->getId() == $this->getRoot() && $this->getLvl() == 0);
    }

    /**
     * Set layout
     *
     * @param Arnm\PagesBundle\Entity\Layout $layout
     */
    public function setLayout(Layout $layout = null)
    {
        $this->layout = $layout;
    }

    /**
     * Get layout
     *
     * @return Arnm\PagesBundle\Entity\Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }
    /**
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        if(!in_array($status, array(self::STATUS_DTAFT, self::STATUS_PUBLISHED)))
        {
            throw new \RuntimeException("Not valid status!");
        }
        $this->status = $status;
    }

    /**
     * Set template
     *
     * @param Arnm\PagesBundle\Entity\Template $template
     */
    public function setTemplate(Template $template = null)
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
     * Add widgets
     *
     * @param Widget $widget
     * @return Page
     */
    public function addWidget(Widget $widget)
    {
        $this->widgets[] = $widget;
        return $this;
    }

    /**
     * Get widgets
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getWidgets()
    {
        return $this->widgets;
    }
}