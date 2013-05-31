<?php
namespace Arnm\PagesBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Arnm\PagesBundle\Entity\Template;
use Arnm\PagesBundle\Entity\Layout;
use Arnm\PagesBundle\Entity\Page;

/**
 * Pages manager service
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PagesManager
{
    /**
     *
     * Enter description here ...
     * @var Registry
     */
    protected $doctrine = null;

    /**
     * @var PageRepository
     */
    protected $pageRepository = null;
    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * Gets entity manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }
    /**
     * Gets doctrin service object
     *
     * @return Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
    /**
     * Gets PageRepository object
     *
     * @return PageRepository
     */
    protected function getPageRepository()
    {
        if(is_null($this->pageRepository)) {
            $this->pageRepository = $this->getEntityManager()->getRepository('ArnmPagesBundle:Page');
        }
        return $this->pageRepository;
    }
    /**
     * Fetches the whole hyrarchy of existing pages in form of a tree tructure
     *
     * @return array
     */
    public function fetchPagesTree()
    {
        return $this->getPageRepository()->fetchFullHirarchy();
    }

    /**
     * Finds a single page by it's slug and path slug if any.
     *
     * @param string $slug
     * @param string $pathSlug
     *
     * @return Page
     */
    public function findPageBySlugs($slug = null, $pathSlug = null)
    {
        $page = null;
        if(is_null($slug) && is_null($pathSlug)) {
            $page = $this->getPageRepository()->findOneByParent(null);
        } else {
            $page = $this->getPageRepository()->findOneBy(array(
                'slug' => $slug,
                'pathSlug' => $pathSlug
            ));
        }

        return $page;
    }
    /**
     * Create new page in DB. The logic is driven by the data of the page itself
     *
     * @param Page $page
     *
     * @return Page
     */
    public function createNewPage(Page $page)
    {
        //set status
        $page->setStatus(Page::STATUS_DTAFT);
        $parentId = $page->getParentId();
        if(! empty($parentId)) {
            //get the parent
            $parent = $this->getPageById($parentId);
            $page->setParent($parent);
        }
        $eMgr = $this->getEntityManager();
        $eMgr->persist($page);
        $eMgr->flush();

        //update the slugs
        $this->updateSlugs($page);

        return $page;
    }

    /**
     * Gets a page object instance by it's ID
     *
     * @param int $id
     *
     * @return Page
     */
    public function getPageById($id)
    {
        return $this->getPageRepository()->findOneById($id);
    }

    /**
     * Persists the changes in the page object into DB
     *
     * @param Page $page
     *
     * @return Page
     */
    public function updatePage(Page $page)
    {
        $this->getEntityManager()->flush();

        //update the slugs
        $this->updateSlugs($page);

        return $page;
    }

    /**
     * Handles the sorting logic
     *
     * @param int $nodeId
     * @param int $parentId
     * @param int $index
     *
     * @throws \InvalidArgumentException
     *
     * @return boolean True on success, false on failure
     */
    public function sort($nodeId, $parentId, $index)
    {
        //first get all required nodes
        $page = $this->getPageById($nodeId);
        $parent = $this->getPageById($parentId);

        if(! ($page instanceof Page) || ! ($parent instanceof Page) || $index < 0) {
            throw new \InvalidArgumentException('Could not find one(or more) of required nodes!');
        }

        $repo = $this->getPageRepository();
        if($index == 0) {
            $repo->persistAsFirstChildOf($page, $parent);
        } else {
            //get all the children for parent
            $children = $repo->children($parent, true);

            //find the sibling the will eventually become a previous sibling to the page
            if(! ($children[$index - 1] instanceof Page)) {
                throw new \InvalidArgumentException('Could not find one(or more) of required nodes!');
            }

            $sibling = $children[$index - 1];

            //put the page as a next sibling of $sibling
            $repo->persistAsNextSiblingOf($page, $sibling);
        }

        $this->getEntityManager()->flush();

        //update the slugs
        $this->updateSlugs($page);

        return true;
    }

    /**
     * Does the whole job to update the slugs (any slugs if needed) for given page node
     * Also runs the same logic for children pages if any
     *
     * @param Page $page
     */
    public function updateSlugs(Page $page)
    {
        $this->updatePathSlugs($page);
        $this->getEntityManager()->flush();
    }

    /**
     * Validates the data of the page to be enough to be pubished.
     *
     * @param Page $page
     * @throws \InvalidArgumentException
     *
     * @return boolean
     */
    public function validate(Page $page)
    {
        if(! ($page->getLayout() instanceof Layout)) {
            throw new \InvalidArgumentException("Layout for the page is not set!");
        } else {
            $layoutName = $page->getLayout()->getLayout();
            if(empty($layoutName)) {
                throw new \InvalidArgumentException("Layout for the page is not set!");
            }
        }
        if(! ($page->getTemplate() instanceof Template)) {
            throw new \InvalidArgumentException("Template for the page is not set!");
        } else {
            $templateName = $page->getTemplate()->getName();
            if(empty($templateName)) {
                throw new \InvalidArgumentException("Template for the page is not set!");
            }
        }

        return true;
    }

    /**
     * Updates the path slug for the element ans all the decendants
     *
     * @param Page $page
     */
    public function updatePathSlugs(Page $page)
    {
        $page->setPathSlug($this->computePathSlug($page));
        $children = $page->getChildren();
        if(count($children) > 0) {
            foreach ($children as $child) {
                $this->updatePathSlugs($child);
            }
        }
    }

    /**
     * Determones the path slug for the element
     *
     * @param Page $page
     *
     * @return string
     */
    protected function computePathSlug(Page $page)
    {
        //there is not path slug for the root or the first level nodes
        if($page->isRoot() || $page->getLvl() == 1) {
            return null;
        }

        //get an path array
        $path = $this->getPageRepository()->getPath($page);
        //just to make sure
        if(count($path) <= 2) {
            return null;
        }

        //iterate throught the path collection ignoring the last one.
        $pathSlugs = array();
        foreach ($path as $node) {
            if($node->isRoot()) {
                continue;
            }

            if($node->getId() == $page->getId()) {
                break;
            }

            $pathSlugs[] = $node->getSlug();
        }

        return implode('/', $pathSlugs);
    }
}
