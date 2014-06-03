<?php
namespace Arnm\PagesBundle\Controller;

use Arnm\WidgetBundle\Entity\Widget;

use Symfony\Component\HttpFoundation\Request;
use Arnm\WidgetBundle\Manager\WidgetsManager;
use Arnm\PagesBundle\Form\PageTemplateType;
use Arnm\PagesBundle\Form\PageLayoutType;
use Symfony\Component\Form\FormError;
use Arnm\PagesBundle\Form\PageHeaderType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Arnm\PagesBundle\Entity\Page;
use Arnm\CoreBundle\Controllers\ArnmAjaxController;
use Symfony\Component\HttpFoundation\Response;
/**
 * REST controller for page management functionality
 *
 * @author: Alex Agulyansky <alex.agulyansky@quickmobile.com>
 * @copyright 2014 Quickmobile Inc.
 */
class PageRestController extends ArnmAjaxController
{
    /**
     * Renders page object in json format
     *
     * @param int $id
     *
     * @return Response
     */
    public function indexAction($id)
    {
        $page = $this->getPagesManager()->getPageById($id);

        if (!($page instanceof Page)) {
            throw $this->createNotFoundException("Page with id: '" . $id . "' not found!");
        }

        $response = $page->toArray();

        return $this->createResponse($response);
    }

    /**
     * Updates page data
     *
     * @param int $id
     */
    public function updateHeaderAction($id)
    {
        try {
            $page = $this->getPagesManager()->getPageById($id);

            if (!($page instanceof Page)) {
                throw $this->createNotFoundException("Page with id: '" . $id . "' not found!");
            }

            $data = $this->extractArrayFromRequest();

            $pageHeaderForm = $this->createForm(new PageHeaderType(), $page);
            $pageHeaderForm->bind($data);

            $response = array();
            if ($pageHeaderForm->isValid()) {
                $page = $this->getPagesManager()->updatePage($page);
                $response = $page->toArray();
            } else {
                $errors = $pageHeaderForm->getErrors();
                foreach ($errors as $key => $error) {
                    if ($error instanceof FormError) {
                        $response[$key] = $error->getMessage();
                    }
                }
            }

            return $this->createResponse($response);
        } catch (\Exception $e) {
            return $this->createResponse(array(
                'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Handles page layout form submission
     *
     * @param int $id
     *
     * @return Response
     */
    public function updateLayoutAction($id)
    {
        try {
            $page = $this->getPagesManager()->getPageById($id);

            if (!($page instanceof Page)) {
                throw $this->createNotFoundException("Page with id: '" . $id . "' not found!");
            }

            $data = $this->extractArrayFromRequest();

            $pageLayoutForm = $this->createForm(new PageLayoutType(), $page);
            $pageLayoutForm->bind($data);

            $response = array();
            if ($pageLayoutForm->isValid()) {
                $page = $this->getPagesManager()->updatePage($page);

                $response = $page->toArray();
            } else {
                $errors = $pageLayoutForm->getErrors();
                foreach ($errors as $key => $error) {
                    if ($error instanceof FormError) {
                        $response[$key] = $error->getMessage();
                    }
                }
            }

            return $this->createResponse($response);
        } catch (\Exception $e) {
            return $this->createResponse(array(
                'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Handles page template form submission
     *
     * @param int $id
     *
     * @return Response
     */
    public function updateTemplateAction($id)
    {
        try {
            $page = $this->getPagesManager()->getPageById($id);

            if (!($page instanceof Page)) {
                throw $this->createNotFoundException("Page with id: '" . $id . "' not found!");
            }

            $data = $this->extractArrayFromRequest();

            $pageLayoutForm = $this->createForm(new PageTemplateType(), $page);
            $pageLayoutForm->bind($data);

            $response = array();
            if ($pageLayoutForm->isValid()) {
                $page = $this->getPagesManager()->updatePage($page);

                $response = $page->toArray();
            } else {
                $errors = $pageLayoutForm->getErrors();
                foreach ($errors as $key => $error) {
                    if ($error instanceof FormError) {
                        $response[$key] = $error->getMessage();
                    }
                }
            }

            return $this->createResponse($response);
        } catch (\Exception $e) {
            return $this->createResponse(array(
                'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Gets a list of all available widgets
     *
     * @return Response
     */
    public function widgetListAction()
    {
        //get list of available widgets
        $widgets = $this->get('arnm_widget.manager')->getAvailableWidgets();

        return $this->createResponse($widgets);
    }

    /**
     * Gets list of widgets organized on the page
     *
     * @param int $id
     *
     * @return Response
     */
    public function pageWidgetsAction($id)
    {
        try {
            $page = $this->getPagesManager()->getPageById($id);

            if (!($page instanceof Page)) {
                throw $this->createNotFoundException("Page with id: '" . $id . "' not found!");
            }

            $widgetsMgr = $this->getWidgetsManager();
            $widgets = $widgetsMgr->findAllWidgetForPage($page);
            $areas = $widgetsMgr->reorganizeByArea($page, $widgets, false);

            $array = array();
            foreach ($areas as $area => $wList) {
                if (!isset($array[$area])) {
                    $array[$area] = array();
                }

                foreach ($wList as $widget) {
                    $array[$area][] = $this->createMinArray($widget);
                }
            }

            return $this->createResponse($array);
        } catch (\Exception $e) {
            return $this->createResponse(array(
                'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Adds new widget into the page
     *
     * @param int $id
     *
     * @return Response
     */
    public function addWidgetAction($id, Request $request)
    {
        try {
            $page = $this->getPagesManager()->getPageById($id);

            if (!($page instanceof Page)) {
                throw $this->createNotFoundException("Page with id: '" . $id . "' not found!");
            }

            $params = json_decode($request->getContent(), true);

            $widgetsMgr = $this->getWidgetsManager();
            $newWidget = $widgetsMgr->addNewWidgetToPage($page, $params['title'], $params['bundle'], $params['controller'], $params['area'], $params['index']);

            return $this->createResponse($newWidget->toArray());
        } catch (\Exception $e) {
            return $this->createResponse(array(
                'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Handles widget sorting
     *
     * @param int $id
     * @param int $widgetId
     */
    public function sortWidgetAction($id, $widgetId, Request $request)
    {
        try {
            //find the page
            $page = $this->getPagesManager()->getPageById($id);
            if (!($page instanceof Page)) {
                throw $this->createNotFoundException("Page with id: '" . $id . "' not found!");
            }
            //find widget
            $widgetsMgr = $this->getWidgetsManager();
            $widget = $widgetsMgr->findWidgetById($widgetId);
            if (!($widget instanceof Widget)) {
                throw $this->createNotFoundException("Widget with id: '" . $widgetId . "' not found!");
            }

            $params = json_decode($request->getContent(), true);
            $widget = $widgetsMgr->moveWidget($page, $widget->getId(), ((string) trim($params['area'])), ((int) trim($params['index'])));

            return $this->createResponse($this->createMinArray($widget));
        } catch (\Exception $e) {
            return $this->createResponse(array(
                'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Creates an array with minimal data from wodget object
     *
     * @param Widget $widget
     *
     * @return array
     */
    private function createMinArray(Widget $widget)
    {
        return array(
            'id' => $widget->getId(),
            'title' => $widget->getTitle(),
            'bundle' => $widget->getBundle(),
            'controller' => $widget->getController(),
            'area' => $widget->getAreaCode(),
            'sequence' => $widget->getSequence()
        );
    }

    /**
     * Extracts an array based on content passed in request as json
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    private function extractArrayFromRequest()
    {
        $content = $this->getRequest()->getContent();
        if (empty($content)) {
            throw new BadRequestHttpException("Empty payload!");
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            throw new BadRequestHttpException("Payload is not parsable!");
        }

        return $data;
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
