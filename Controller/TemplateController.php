<?php

namespace Arnm\PagesBundle\Controller;

use Arnm\CoreBundle\Controllers\ArnmController;
use Arnm\PagesBundle\Entity\Template;
use Arnm\PagesBundle\Form\TemplateType;

/**
 * template controller.
 *
 */
class TemplateController extends ArnmController
{

    /**
     * Lists all template entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('ArnmPagesBundle:Template')->findAll();

        return $this->render('ArnmPagesBundle:Template:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a template entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ArnmPagesBundle:Template')->find($id);

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Layout entity.');
        }

        return $this->render('ArnmPagesBundle:Template:show.html.twig', array(
            'entity' => $entity
        ));
    }

    /**
     * Displays a form to create a new template entity.
     *
     */
    public function newAction()
    {
        $entity = new Template();
        $form = $this->createForm(new TemplateType(), $entity);

        return $this->render('ArnmPagesBundle:Template:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Creates a new template entity.
     *
     */
    public function createAction()
    {
        $entity = new Template();
        $request = $this->getRequest();
        $form = $this->createForm(new TemplateType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('Araneum_template_show', array(
                'id' => $entity->getId()
            )));

        }

        return $this->render('ArnmPagesBundle:Template:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing template entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ArnmPagesBundle:Template')->find($id);

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Layout entity.');
        }

        $editForm = $this->createForm(new TemplateType(), $entity);

        return $this->render('ArnmPagesBundle:Template:edit.html.twig',
        array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Edits an existing template entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ArnmPagesBundle:Template')->find($id);

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Layout entity.');
        }

        $editForm = $this->createForm(new TemplateType(), $entity);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('Araneum_template_edit', array(
                'id' => $id
            )));
        }

        return $this->render('ArnmPagesBundle:Template:edit.html.twig',
        array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a template entity.
     *
     */
    public function deleteAction($id)
    {
        if ($id) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ArnmPagesBundle:Template')->find($id);

            if (! $entity) {
                throw $this->createNotFoundException('Unable to find Layout entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('Araneum_template'));
    }
}
