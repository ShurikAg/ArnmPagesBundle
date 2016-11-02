<?php
namespace Arnm\PagesBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Arnm\SeoBundle\Event\SitemapGenerationEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Arnm\SeoBundle\Model\ChangeFreq;
use Arnm\PagesBundle\Entity\Page;
use Arnm\PagesBundle\Entity\PageRepository;

/**
 * This class is acting as a listener and built to provide data for sitemap generation
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class SitemapDataProvider
{

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var Router
     */
    private $router;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine, Router $router)
    {
        $this->setDoctrine($doctrine);
        $this->setRouter($router);
    }

    /**
     * @param SitemapGenerationEvent $event
     */
    public function onSitemapGeneration(SitemapGenerationEvent $event)
    {
        $urlSet = $event->getUrlSet();
        //get all relevant schools
        $pages = $this->getPageRepository()->findByStatus(Page::STATUS_PUBLISHED);

        foreach ($pages as $page) {
            $loc = null;
            if ($page->isRoot()) {
                $loc = $this->getRouter()->generate('ArnmPagesBundle_page_root', [], Router::ABSOLUTE_URL);
            } elseif (!empty($page->getSlug()) && empty($page->getPathSlug())) {
                $loc = $this->getRouter()->generate('ArnmPagesBundle_page_first_lvl', ['slug' => $page->getSlug()], Router::ABSOLUTE_URL);
            } elseif (!empty($page->getSlug()) && !empty($page->getPathSlug())) {
                $loc = $this->getRouter()->generate('ArnmPagesBundle_page_render', ['slug' => $page->getSlug(), 'path_slug' => $page->getPathSlug()], Router::ABSOLUTE_URL);
            } else {
                continue;
            }

            $now = new \DateTime('now');
            $monthAgo = $now->modify("-1 month");
            //school details
            $pageModDate = (is_null($page->getUpdatedAt())) ? new \DateTime('now') : $page->getUpdatedAt();
            if ($pageModDate < $monthAgo) {
                $pageModDate = $now;
            }

            $urlSet->createAndAddUrl($loc, $pageModDate, ChangeFreq::CF_MONTHLY, 0.3);
        }
    }

    /**
     * Gets page repository object instance
     *
     * @return PageRepository
     */
    public function getPageRepository()
    {
        return $this->getDoctrine()
            ->getManager()
            ->getRepository('ArnmPagesBundle:Page');
    }

    /**
     * @return Registry
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param Registry $doctrine
     *
     * @return SitemapDataProvider
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;

        return $this;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     *
     * @return SitemapDataProvider
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }
}
