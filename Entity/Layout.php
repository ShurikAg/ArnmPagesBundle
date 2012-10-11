<?php

namespace Arnm\PagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Arnm\PagesBundle\Entity\Page;
/**
 * Arnm\PagesBundle\Entity\Layout
 *
 * @ORM\Table(name="layout")
 * @ORM\Entity(repositoryClass="Arnm\PagesBundle\Entity\LayoutRepository")
 */
class Layout
{
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