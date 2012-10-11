<?php
namespace Arnm\PagesBundle\Controller;

use Arnm\PagesBundle\Entity\Area;
use Arnm\CoreBundle\Controllers\ArnmController;
use Arnm\PagesBundle\Entity\Template;
use Arnm\PagesBundle\Form\AreaType;

/**
 * template controller.
 *
 */
class AreaController extends ArnmController
{
    /**
     * Lists all template entities.
     *
     */
    public function indexAction($templateId)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $template = $em->getRepository('ArnmPagesBundle:Template')->findOneById($templateId);
        $entities = $em->getRepository('ArnmPagesBundle:Area')->findByTemplateId($templateId);

        return $this->render('ArnmPagesBundle:Area:index.html.twig', array(
            'entities' => $entities,
            'template' => $template
        ));
    }

    /**
     * Finds and displays a area entity.
     *
     */
    public function showAction($templateId, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $template = $em->getRepository('ArnmPagesBundle:Template')->find($templateId);
        $entity = $em->getRepository('ArnmPagesBundle:Area')->findOneById($id);

        if (! $entity || $template->getId() != $entity->getTemplate()->getId()) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }

        return $this->render('ArnmPagesBundle:Area:show.html.twig', array(
            'entity' => $entity,
            'template' => $template
        ));
    }

    /**
     * Displays a form to create a new template entity.
     *
     */
    public function newAction($templateId)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $template = $em->getRepository('ArnmPagesBundle:Template')->find($templateId);
        if (! $template) {
            throw $this->createNotFoundException('Unable to find related Template entity.');
        }

        $entity = new Area();
        $entity->setTemplate($template);
        $form = $this->createForm(new AreaType(), $entity);

        return $this->render('ArnmPagesBundle:Area:new.html.twig',
        array(
            'entity' => $entity,
            'template' => $template,
            'form' => $form->createView()
        ));
    }

    /**
     * Creates a new area entity.
     *
     */
    public function createAction($templateId)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $template = $em->getRepository('ArnmPagesBundle:Template')->find($templateId);
        if (! $template) {
            throw $this->createNotFoundException('Unable to find related Template entity.');
        }

        $entity = new Area();
        $entity->setTemplate($template);

        $request = $this->getRequest();
        $form = $this->createForm(new AreaType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
            $this->generateUrl('Araneum_area_show',
            array(
                'templateId' => $template->getId(),
                'id' => $entity->getId()
            )));

        }

        return $this->render('ArnmPagesBundle:Area:new.html.twig',
        array(
            'entity' => $entity,
            'template' => $template,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing template entity.
     *
     */
    public function editAction($templateId, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $template = $em->getRepository('ArnmPagesBundle:Template')->find($templateId);
        $entity = $em->getRepository('ArnmPagesBundle:Area')->findOneById($id);

        if (! $entity || $template->getId() != $entity->getTemplate()->getId()) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }

        $editForm = $this->createForm(new AreaType(), $entity);

        return $this->render('ArnmPagesBundle:Area:edit.html.twig',
        array(
            'entity' => $entity,
            'template' => $template,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Edits an existing template entity.
     *
     */
    public function updateAction($templateId, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $template = $em->getRepository('ArnmPagesBundle:Template')->find($templateId);
        $entity = $em->getRepository('ArnmPagesBundle:Area')->findOneById($id);

        if (! $entity || $template->getId() != $entity->getTemplate()->getId()) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }

        $editForm = $this->createForm(new AreaType(), $entity);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
            $this->generateUrl('Araneum_area_edit', array(
                'templateId' => $template->getId(),
                'id' => $id
            )));
        }

        return $this->render('ArnmPagesBundle:Area:edit.html.twig',
        array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a template entity.
     *
     */
    public function deleteAction($templateId, $id)
    {
        if ($id) {
            $em = $this->getDoctrine()->getEntityManager();

            $template = $em->getRepository('ArnmPagesBundle:Template')->find($templateId);
            $entity = $em->getRepository('ArnmPagesBundle:Area')->findOneById($id);

            if (! $entity || $template->getId() != $entity->getTemplate()->getId()) {
                throw $this->createNotFoundException('Unable to find Area entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('Araneum_area', array(
            'templateId' => $template->getId()
        )));
    }
}
