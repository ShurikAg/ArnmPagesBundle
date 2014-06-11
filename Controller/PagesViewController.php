<?php
namespace Arnm\PagesBundle\Controller;

use Arnm\CoreBundle\Controllers\ArnmAjaxController;
use Arnm\WidgetBundle\Entity\Widget;
use Arnm\PagesBundle\Entity\Template;
use Arnm\PagesBundle\Form\PageTemplateType;
use Arnm\PagesBundle\Form\PageLayoutType;
use Arnm\PagesBundle\Form\PageHeaderType;
use Arnm\PagesBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Arnm\PagesBundle\Form\PageType;
use Arnm\PagesBundle\Form\NewPageType;
use Arnm\PagesBundle\Manager\PagesManager;
use Arnm\WidgetBundle\Manager\WidgetsManager;
/**
 * Pages controller is responsible for pages administration flows
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PagesViewController extends ArnmAjaxController
{

    /**
     * Renders manage page template
     *
     * @return Response
     */
    public function managePageAction()
    {
        return $this->render('ArnmPagesBundle:Pages:managePage.html.twig');
    }

    /**
     * Renderspage header template
     *
     * @return Response
     */
    public function headerAction()
    {
        $pageHeaderForm = $this->createForm(new PageHeaderType());

        return $this->render('ArnmPagesBundle:Pages:header.html.twig', array(
            'form' => $pageHeaderForm->createView()
        ));
    }

    /**
     * Renders page layout template
     *
     * @return Response
     */
    public function layoutAction()
    {
        $pageLayoutForm = $this->createForm(new PageLayoutType());

        return $this->render('ArnmPagesBundle:Pages:layout.html.twig', array(
            'form' => $pageLayoutForm->createView()
        ));
    }

    /**
     * Renders page template template
     *
     * @return Response
     */
    public function templateAction()
    {
        $pageTemplateForm = $this->createForm(new PageTemplateType());

        return $this->render('ArnmPagesBundle:Pages:template.html.twig', array(
            'form' => $pageTemplateForm->createView()
        ));
    }

    /**
     * Renders page template organizer of a page
     *
     * @return Response
     */
    public function templateOrganizerAction($id)
    {
        $page = $this->getPagesManager()->getPageById($id);

        if (!($page instanceof Page)) {
            throw $this->createNotFoundException("Page with id: '" . $id . "' not found!");
        }

        $tmplObj = $page->getTemplate();
        $pageTemplate = ($tmplObj instanceof Template) ? $tmplObj->getName() : null;

        if (empty($pageTemplate)) {
            return '';
        }

        //constract the name for the template admin
        $templateNameParts = explode(':', $pageTemplate);
        $templateNameParts[1] = $templateNameParts[1] . '/Organizer';
        $pageTemplate = implode(':', $templateNameParts);

        return $this->render($pageTemplate);
    }

    /**
     * Renders widget config form
     *
     * @return Response
     */
    public function widgetConfigFormAction($id)
    {
        //find widget
        $widgetsMgr = $this->getWidgetsManager();
        $widget = $widgetsMgr->findWidgetById($id);
        if (!($widget instanceof Widget)) {
            throw $this->createNotFoundException("Widget with id: '" . $id . "' not found!");
        }

        //resolve template and title
        $editRoute = 'widget_'.$widget->getBundle().'_'.$widget->getController().'_edit';
        $updateRoute = 'widget_'.$widget->getBundle().'_'.$widget->getController().'_update';
        $dataRoute = 'widget_'.$widget->getBundle().'_'.$widget->getController().'_data';
        $formTemplate = $this->get('router')->generate($editRoute);
        $submitTarget = $this->get('router')->generate($updateRoute, array('id' => $widget->getId()));
        $dataSource = $this->get('router')->generate($dataRoute, array('id' => $widget->getId()));
        $formTitle = 'Configure '. $widget->getTitle();

        $response = array(
            'formTmpl' => $formTemplate,
            'formSubmitTarget' => $submitTarget,
            'dataSource' => $dataSource,
            'formTitle' => $formTitle
        );

        return $this->createResponse($response);
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

    /**
     * Gets widgets manager object
     *
     * @return WidgetsManager
     */
    protected function getWidgetsManager()
    {
        return $this->get('arnm_widget.manager');
    }
}
