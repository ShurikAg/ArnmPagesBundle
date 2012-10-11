<?php

namespace Arnm\PagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Arnm\CoreBundle\Entity\Entity;

/**
 * Arnm\PagesBundle\Entity\Template
 *
 * @ORM\Table(name="template")
 * @ORM\Entity(repositoryClass="Arnm\PagesBundle\Entity\TemplateRepository")
 */
class Template extends Entity
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
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=255)
   * 
   * @Assert\NotBlank()
   */
  private $name;
  
  /**
   * @ORM\OneToMany(targetEntity="Page", mappedBy="template")
   */
  private $pages;
  
  /**
   * @ORM\OneToMany(targetEntity="Area", mappedBy="template")
   */
  private $areas;
  
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
   * Set name
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  
  /**
   * Get name
   *
   * @return string 
   */
  public function getName()
  {
    return $this->name;
  }
  
  public function __construct()
  {
    $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
    $this->areas = new \Doctrine\Common\Collections\ArrayCollection();
  }
  
  /**
   * Add page
   *
   * @param Page $page
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
    return $this->getName();
  }

    /**
     * Add areas
     *
     * @param Arnm\PagesBundle\Entity\Area $areas
     */
    public function addArea(\Arnm\PagesBundle\Entity\Area $areas)
    {
        $this->areas[] = $areas;
    }

    /**
     * Get areas
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAreas()
    {
        return $this->areas;
    }
}