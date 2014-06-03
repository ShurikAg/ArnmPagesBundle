<?php
namespace Arnm\PagesBundle\Controller;

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
class PagesController extends ArnmController
{

    /**
     * Renders pages tree hyrarchy
     *
     * return string Rendered page HTML
     */
    public function treeAction()
    {
        $pagesTree = $this->getPagesManager()->fetchPagesTree();

        return $this->render('ArnmPagesBundle:Pages:tree.html.twig', array(
            'tree' => $pagesTree
        ));
    }

    /**
     * Renders and handles new page form
     *
     * @param Request $request
     */
    public function newAction(Request $request)
    {
        $page = new Page();

        //check if we've got the parent page
        $parentPage = null;

        if ($request->query->has('parent_id')) {
            //try to find the parent page
            $parentPage = $this->getPagesManager()->getPageById($request->query->get('parent_id'));
            if (! $parentPage) {
                $this->getSession()->setFlash('error', 'page.form.error.parent_page_not_found');

                return $this->redirect($this->generateUrl('ArnmPagesBundle_pages'));
            }

            $page->setParentId($parentPage->getId());
        }

        $pageForm = $this->createForm(new NewPageType(), $page);

        if ($request->getMethod() == 'POST') {
            $pageForm->bind($request);

            if ($pageForm->isValid()) {
                //handle populated page object
                $this->getPagesManager()->createNewPage($page);

                $this->getSession()->setFlash('notice', 'page.form.create.success');

                return $this->redirect($this->generateUrl('ArnmPagesBundle_page_show', array(
                    'id' => $page->getId()
                )));
            }
        }
        return $this->render('ArnmPagesBundle:Pages:newPage.html.twig', array(
            'form' => $pageForm->createView()
        ));
    }

    /**
     * Renders page details page.
     * This page enables to update any details of the page
     *
     * @param int $id
     */
    public function showAction($id)
    {
        $page = $this->getPagesManager()->getPageById($id);

        if (! $page) {
            throw $this->createNotFoundException('No page found for id ' . $id);
        }

        return $this->render('ArnmPagesBundle:Pages:showPage.html.twig', array(
            'page' => $page
        ));
    }

    /**
     * Handles ajax request for sorting of a tree nodes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sortAction(Request $request)
    {
        if (! $request->isXmlHttpRequest() || $request->getMethod() != 'POST') {
            throw $this->createNotFoundException('Page does not exists');
        }

        $nodeId = $request->request->get('node');
        $parentId = $request->request->get('parent');
        $index = $request->request->get('index');

        $reply = array();
        try {
            if ($this->getPagesManager()->sort($nodeId, $parentId, $index) === true) {
                $reply['status'] = 'SUCCESS';
            }

        } catch (\InvalidArgumentException $e) {
            $reply['status'] = 'FAIL';
            $reply['error'] = $e->getMessage();
        }

        $response = new Response(json_encode($reply));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Updates status of athe page
     *
     * @param int $id
     * @param string $action
     */
    public function updateStatusAction($id, $action)
    {
        $request = $this->getRequest();
        if (! $request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $reply = array();

        try {
            $mgr = $this->getPagesManager();
            $page = $mgr->getPageById($id);
            $reply['status'] = 'OK';
            if ($action == 'publish') {
                $mgr->validate($page);
                $status = Page::STATUS_PUBLISHED;
            } elseif ($action == 'unpublish') {
                $status = Page::STATUS_DTAFT;
            }

            $page->setStatus($status);
            $page = $mgr->updatePage($page);

            $reply['content'] = $this->statusControlAction($page)->getContent();

        } catch (\InvalidArgumentException $e) {
            $reply['status'] = 'FAILED';
            $reply['error'] = $e->getMessage();
        }

        $response = new Response(json_encode($reply));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
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
