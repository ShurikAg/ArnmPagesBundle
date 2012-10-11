<?php
namespace Arnm\PagesBundle\Controller;

use Arnm\CoreBundle\Controllers\ArnmAjaxController;
use Arnm\CoreBundle\Entity\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Arnm\PagesBundle\Entity\Page;
/**
 * This class is responsible for AJAX driven pages management
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class AjaxPagesController extends ArnmAjaxController
{
    
    /**
     * Configured existing widgets
     *
     * @var array
     */
    protected $config = array(
        'head' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:head.show.html.twig'
        ), 
        'page_layout' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'entityType' => 'Arnm\PagesBundle\Form\PageLayoutType', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:pageLayout.show.html.twig'
        ), 
        'layout' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'entityType' => 'Arnm\PagesBundle\Form\PageLayoutType', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:layout.show.html.twig', 
            'editTemplate' => 'ArnmPagesBundle:Pages/Ajax:layout.edit.html.twig'
        ), 
        'page_template' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'entityType' => 'Arnm\PagesBundle\Form\PageTemplateType', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:pageTemplate.show.html.twig'
        ), 
        'template' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'entityType' => 'Arnm\PagesBundle\Form\PageTemplateType', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:template.show.html.twig', 
            'editTemplate' => 'ArnmPagesBundle:Pages/Ajax:template.edit.html.twig'
        ), 
        'template_organizer' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:templateOrganizer.show.html.twig'
        ), 
        'title' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'entityType' => 'Arnm\PagesBundle\Form\PageTitleType', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:title.show.html.twig', 
            'editTemplate' => 'ArnmPagesBundle:Pages/Ajax:title.edit.html.twig'
        ), 
        'description' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'entityType' => 'Arnm\PagesBundle\Form\PageDescriptionType', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:description.show.html.twig', 
            'editTemplate' => 'ArnmPagesBundle:Pages/Ajax:description.edit.html.twig'
        ), 
        'keywords' => array(
            'entityClass' => 'Page', 
            'fullEntityClassName' => 'Arnm\PagesBundle\Entity\Page',  //used for validations
            'entitySerive' => 'arnm_pages.manager', 
            'entityType' => 'Arnm\PagesBundle\Form\PageKeywordsType', 
            'showTemplate' => 'ArnmPagesBundle:Pages/Ajax:keywords.show.html.twig', 
            'editTemplate' => 'ArnmPagesBundle:Pages/Ajax:keywords.edit.html.twig'
        )
    );
    
    /**
     * Gets config for given entity
     *
     * @param string $enitity
     *
     * @throws \RuntimeException
     *
     * @return array Configuration array
     */
    protected function getConfigForEntity($entity)
    {
        if (! isset($this->config[$entity])) {
            throw new \RuntimeException("No config defined for entity: '" . $entity . "'!");
        }
        
        return $this->config[$entity];
    }
    
    /**
     * Gets service object instance that responsible for the entity
     *
     * @param array $config
     *
     * @return object
     */
    protected function getEntityServiceInstance($config)
    {
        return $this->get($config['entitySerive']);
    }
    
    /**
     * Gets an entity using manager object and validates if the entity is actually instaince of correct class.
     *
     * @param int    $id
     * @param object $mgr
     * @param array  $config
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    protected function getEntityObjectAndValidate($id, $mgr, $config)
    {
        $className = $config['entityClass'];
        $getter = 'get' . $className . 'ById';
        if (! method_exists($mgr, $getter)) {
            throw new \RuntimeException("Method '" . $getter . "' does not exists in '" . get_class($mgr) . "'");
        }
        
        $obj = call_user_func(array(
            $mgr, 
            $getter
        ), $id);
        
        if (! ($obj instanceof $config['fullEntityClassName'])) {
            throw new \InvalidArgumentException("Could not find an object of type '" . $className . "' by id '" . $id . "' from '" . get_class($mgr) . "'!");
        }
        
        return $obj;
    }
    
    /**
     * Create an form for the entity
     *
     * @param object $obj
     * @param array  $config
     *
     * @return Symfony\Component\Form\AbstractType
     */
    protected function createEntityForm($obj, $config)
    {
        if (empty($config['entityType'])) {
            throw new \RuntimeException("The type for an entity is not defined");
        }
        $typeClassName = $config['entityType'];
        
        $form = $this->createForm(new $typeClassName(), $obj);
        
        return $form;
    }
    
    /**
     * This function renders required entity representation of requested element
     *
     * @param int $id
     */
    public function showAction($id, $entity)
    {
        $reply = array();
        try {
            $this->validateRequest();
            
            //get config for this entity
            $config = $this->getConfigForEntity($entity);
            
            //get manager object
            $mgr = $this->getEntityServiceInstance($config);
            
            $obj = $this->getEntityObjectAndValidate($id, $mgr, $config);
            
            //render response content string
            $content = $this->renderView($config['showTemplate'], array(
                'obj' => $obj
            ));
            
            $reply['status'] = 'OK';
            $reply['content'] = $content;
            if ($obj instanceof Entity) {
                $reply['entity'] = $obj->toArray();
            }
        
        } catch (\Exception $e) {
            $reply['status'] = 'FAIL';
            $reply['reason'] = $e->getMessage();
        }
        
        return $this->createResponse($reply);
    }
    
    /**
     * This action serves the edit form for en entity
     *
     * @param int    $id
     * @param string $entity
     */
    public function editAction($id, $entity)
    {
        $reply = array();
        try {
            $this->validateRequest();
            
            $config = $this->getConfigForEntity($entity);
            
            $mgr = $this->getEntityServiceInstance($config);
            
            $obj = $this->getEntityObjectAndValidate($id, $mgr, $config);
            
            //create the form for the entity
            $form = $this->createEntityForm($obj, $config);
            
            //render response content string
            $content = $this->renderView($config['editTemplate'], 
            array(
                'obj' => $obj, 
                'form' => $form->createView()
            ));
            
            $reply['status'] = 'OK';
            $reply['content'] = $content;
        
        } catch (\Exception $e) {
            $reply['status'] = 'FAIL';
            $reply['reason'] = $e->getMessage();
        }
        
        return $this->createResponse($reply);
    }
    
    /**
     * This action serves the edit form for en entity
     *
     * @param int    $id
     * @param string $entity
     */
    public function updateAction($id, $entity)
    {
        $reply = array();
        try {
            $this->validateRequest();
            
            $config = $this->getConfigForEntity($entity);
            
            $mgr = $this->getEntityServiceInstance($config);
            
            $obj = $this->getEntityObjectAndValidate($id, $mgr, $config);
            
            //create the form for the entity
            $form = $this->createEntityForm($obj, $config);
            
            //check if it is post request and binf the form
            //get request
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                //binf the form
                $form->bindRequest($request);
                //if valid
                if ($form->isValid()) {
                    //calculate update method name
                    $className = $config['entityClass'];
                    $updateMethod = 'update' . $className;
                    $obj = $mgr->$updateMethod($obj);
                    //generate the new content
                    $content = $this->renderView($config['showTemplate'], 
                    array(
                        'obj' => $obj
                    ));
                } else {
                    //render response content string with not valid form
                    $content = $this->renderView($config['editTemplate'], 
                    array(
                        'obj' => $obj, 
                        'form' => $form->createView()
                    ));
                }
            }
            
            $reply['status'] = 'OK';
            $reply['content'] = $content;
            if ($obj instanceof Entity) {
                $reply['entity'] = $obj->toArray();
            }
        
        } catch (\Exception $e) {
            $reply['status'] = 'FAIL';
            $reply['reason'] = $e->getMessage();
        }
        
        return $this->createResponse($reply);
    }
}
