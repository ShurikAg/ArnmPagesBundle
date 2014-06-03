<?php
namespace Arnm\PagesBundle\Controller;

use Arnm\PagesBundle\Entity\Template;

use Arnm\PagesBundle\Form\PageTemplateType;

use Arnm\PagesBundle\Form\PageLayoutType;

use Arnm\PagesBundle\Form\PageHeaderType;

use Arnm\PagesBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Arnm\CoreBundle\Controllers\ArnmController;
use Arnm\PagesBundle\Form\PageType;
use Arnm\PagesBundle\Form\NewPageType;
use Arnm\PagesBundle\Manager\PagesManager;
/**
 * Pages controller is responsible for pages administration flows
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PagesViewController extends ArnmController
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
     * Gets pages manager object
     *
     * @return PagesManager
     */
    protected function getPagesManager()
    {
        return $this->get('arnm_pages.manager');
    }
}
