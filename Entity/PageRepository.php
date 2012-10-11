<?php
namespace Arnm\PagesBundle\Entity;

use Doctrine\ORM\NonUniqueResultException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends NestedTreeRepository
{
  
  /**
   * Fetched the whole hirarchy of the pages tree
   * 
   * @return array
   */
  public function fetchFullHirarchy()
  {
    $tree = $this->childrenHierarchy();
    return $tree;
  }
  
  /**
   * Get a single root node, in this system, at this point should exist only one such node
   * 
   * @throws NonUniqueResultException
   *
   * @return Page
   */
  public function getRootNode()
  {
    return $this->getRootNodesQuery()->getSingleResult();
  }
}