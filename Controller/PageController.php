<?php
namespace Arnm\PagesBundle\Controller;

use Symfony\Component\Validator\Constraints\Count;

use Arnm\PagesBundle\Model\PageContentResplacement;
use Arnm\CoreBundle\Controllers\ArnmController;
use Arnm\PagesBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Response;
/**
 * This cotroller is responsible for pages representation on the frontend
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PageController extends ArnmController
{

    /**
     * This the main entry point action for rendering pages.
     *
     * @param string $slug
     * @param string $path_slug
     */
    public function renderAction($slug = null, $path_slug = null)
    {
        //find a page based on the slugs
        $pagesMgr = $this->getPagesManager();
        $page = $pagesMgr->findPageBySlugs($slug, $path_slug);

        if (! ($page instanceof Page) || $page->getStatus() !== Page::STATUS_PUBLISHED) {
            throw $this->createNotFoundException("Page not found");
        }

        //get the layout that the page will be rendered in
        $layout = $page->getLayout()->getLayout();
        //get the template for this page
        $template = $page->getTemplate()->getName();

        return $this->render($template, array(
            'page' => $page,
            'layout' => $layout
        ));
    }

    /**
     * Inherits the given page and replaces a specific area with a given content
     *
     * @param Page $page
     * @param string $area
     * @param string $content
     *
     * @return Response
     */
    public function inheritPageAction(Page $page, $area, PageContentResplacement $replacement)
    {
        //get the layout that the page will be rendered in
        $layout = $page->getLayout()->getLayout();
        //get the template for this page
        $template = $page->getTemplate()->getName();

        return $this->render($template,
            array(
                'page' => $page,
                'layout' => $layout,
                'replace' => $area,
                'replacement' => $replacement
            ));
    }

    /**
     * Renders a list of widgets for a given area code
     *
     * @param Page $page
     * @param string $areaCode
     *
     * @return Response
     */
    public function widgetListAction(Page $page, $areaCode)
    {
        $widgetsMgr = $this->get('arnm_widget.manager');
        $allWidgets = $widgetsMgr->findAllWidgetForPage($page);
        $widgets = $widgetsMgr->filterWidgetsByArea($allWidgets, $areaCode);

        $wArray = array();
        foreach ($widgets as $widget) {
            $widgetController = $widget->getBundle() . "Bundle:Widgets\\" . $widget->getController() . ":render";
            $wArray[] = array(
                'controller' => $widgetController,
                'widget' => $widget
            );
        }

        return $this->render('ArnmPagesBundle:Pages/Widget:renderList.html.twig', array(
            'widgets' => $wArray
        ));
    }

    /**
     * Gets pages manager object
     *
     * @return PagesManager
     */
    protected function getPagesManager()
    {
        return $this->get('arnm_pages.manager');
    }
}
