<?php
namespace Arnm\PagesBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Arnm\WidgetBundle\Entity\Widget;
use Symfony\Component\HttpFoundation\Request;
use Arnm\PagesBundle\Entity\Template;
use Arnm\CoreBundle\Controllers\ArnmAjaxController;
use Arnm\PagesBundle\Entity\Page;
/**
 * Controller for template organization functionality
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class TemplateOrganizerController extends ArnmAjaxController
{
    /**
     * Renders template organizer for page
     *
     * @param Page $page
     *
     * @return string
     */
    public function renderAction(Page $page)
    {
        $tmplObj = $page->getTemplate();
        $pageTemplate = ($tmplObj instanceof Template) ? $tmplObj->getName() : null;
        
        if (empty($pageTemplate)) {
            return '';
        }
        
        //constract the name for the template admin
        $templateNameParts = explode(':', $pageTemplate);
        $templateNameParts[1] = $templateNameParts[1] . '/Organizer';
        $pageTemplate = implode(':', $templateNameParts);
        
        //get list of available widgets
        $widgets = $this->get('arnm_widget.manager')->getAvailableWidgets();
        
        return $this->render('ArnmPagesBundle:Pages/Template:templateOrganizer.html.twig', 
        array(
            'widgets' => $widgets, 
            'template' => $pageTemplate, 
            'page' => $page
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
        $widgets = $page->getWidgetsForArea($areaCode);
        
        return $this->render('ArnmPagesBundle:Pages/Widget:list.html.twig', array(
            'widgets' => $widgets
        ));
    }
    
    /**
     * Renders a single widget representation of araneum
     * 
     * @param Widget|array $widget
     * 
     * @throws \InvalidArgumentException
     * 
     * @return Response
     */
    public function widgetAction($widget, $status = null)
    {
        if ($widget instanceof Widget) {
            $routePrefix = 'widget_' . $widget->getBundle() . '_' . $widget->getController();
        } else {
            $routePrefix = 'widget_' . $widget['bundle'] . '_' . $widget['controller'];
        }
        
        $editRoute = $routePrefix . '_edit';
        $deleteRoute = $routePrefix . '_delete';
        $params = array(
            'widget' => $widget, 
            'editRoute' => $editRoute, 
            'deleteRoute' => $deleteRoute
        );
        if (! empty($status)) {
            $params['status'] = $status;
        }
        
        return $this->render('ArnmPagesBundle:Pages/Widget:widget.html.twig', $params);
    }
    
    /**
     * Handles the sorting of the widgets (when widget placed in the template)
     *
     * @param int $id Page ID
     */
    public function sortWidgetAction($id)
    {
        $this->validateRequest();
        
        $reply = array();
        try {
            //get the page
            $page = $this->get('arnm_pages.manager')->getPageById($id);
            if (! ($page instanceof Page)) {
                throw new \InvalidArgumentException("Page entity with ID '" . (string) $id . "' was not found!");
            }
            
            //get all the post params
            $params = $this->getRequest()->request->all();
            
            $newWidget = null;
            //if it's a new widget for this page
            if (empty($params['widgetId'])) {
                //add it
                //return the ID of new widget
                $newWidget = $this->get('arnm_widget.manager')->addNewWidgetToPage($page, $params['title'], $params['bundle'], 
                $params['controller'], $params['area'], $params['index']);
                $reply['widget']['id'] = $newWidget->getId();
                $routePrefix = 'widget_' . $newWidget->getBundle() . '_' . $newWidget->getController();
                $editRoute = $routePrefix . '_edit';
                $deleteRoute = $routePrefix . '_delete';
                $reply['widget']['edit_action'] = $this->get('router')->generate($editRoute, 
                array(
                    'id' => $newWidget->getId()
                ));
                $reply['widget']['delete_action'] = $this->get('router')->generate($deleteRoute, 
                array(
                    'id' => $newWidget->getId()
                ));
            } else {
                //else validate that this widget exists for the page and sort it into different place
                $widget = $this->get('arnm_widget.manager')->moveWidget($page, $params['widgetId'], $params['area'], $params['index']);
            }
            
            $reply['status'] = 'OK';
        
        } catch (Exception $e) {
            $reply['status'] = 'FAIL';
            $reply['reason'] = $e->getMessage();
        }
        
        return $this->createResponse($reply);
    }
}
