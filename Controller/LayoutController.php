<?php

namespace Arnm\PagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Arnm\PagesBundle\Entity\Layout;
use Arnm\PagesBundle\Form\LayoutType;

/**
 * Layout controller.
 *
 */
class LayoutController extends Controller
{
    /**
     * Lists all Layout entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $entities = $em->getRepository('ArnmPagesBundle:Layout')->findAll();
        
        return $this->render('ArnmPagesBundle:Layout:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    /**
     * Finds and displays a Layout entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $entity = $em->getRepository('ArnmPagesBundle:Layout')->find($id);
        
        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Layout entity.');
        }
        
        return $this->render('ArnmPagesBundle:Layout:show.html.twig', array(
            'entity' => $entity
        ));
    }
    
    /**
     * Displays a form to create a new Layout entity.
     *
     */
    public function newAction()
    {
        $entity = new Layout();
        $form = $this->createForm(new LayoutType(), $entity);
        
        return $this->render('ArnmPagesBundle:Layout:new.html.twig', 
        array(
            'entity' => $entity, 
            'form' => $form->createView()
        ));
    }
    
    /**
     * Creates a new Layout entity.
     *
     */
    public function createAction()
    {
        $entity = new Layout();
        $request = $this->getRequest();
        $form = $this->createForm(new LayoutType(), $entity);
        $form->bindRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('Araneum_layout_show', array(
                'id' => $entity->getId()
            )));
        
        }
        
        return $this->render('ArnmPagesBundle:Layout:new.html.twig', 
        array(
            'entity' => $entity, 
            'form' => $form->createView()
        ));
    }
    
    /**
     * Displays a form to edit an existing Layout entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $entity = $em->getRepository('ArnmPagesBundle:Layout')->find($id);
        
        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Layout entity.');
        }
        
        $editForm = $this->createForm(new LayoutType(), $entity);
        
        return $this->render('ArnmPagesBundle:Layout:edit.html.twig', 
        array(
            'entity' => $entity, 
            'edit_form' => $editForm->createView()
        ));
    }
    
    /**
     * Edits an existing Layout entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $entity = $em->getRepository('ArnmPagesBundle:Layout')->find($id);
        
        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Layout entity.');
        }
        
        $editForm = $this->createForm(new LayoutType(), $entity);
        
        $request = $this->getRequest();
        
        $editForm->bindRequest($request);
        
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('Araneum_layout_edit', array(
                'id' => $id
            )));
        }
        
        return $this->render('ArnmPagesBundle:Layout:edit.html.twig', 
        array(
            'entity' => $entity, 
            'edit_form' => $editForm->createView()
        ));
    }
    
    /**
     * Deletes a Layout entity.
     *
     */
    public function deleteAction($id)
    {
        if ($id) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ArnmPagesBundle:Layout')->find($id);
            
            if (! $entity) {
                throw $this->createNotFoundException('Unable to find Layout entity.');
            }
            
            $em->remove($entity);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('Araneum_layout'));
    }
}
