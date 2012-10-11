<?php

namespace Arnm\PagesBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Arnm\PagesBundle\Entity\Page;
/**
 * This class is responsible for creating PageForm object
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PageType extends AbstractType
{
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.AbstractType::buildForm()
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('title', 'text', array(
        'label' => 'page.form.title.label', 
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'page.form.title.help', 
            'class' => 'input-xlarge'
        ), 
        'required' => false
    ));
    $builder->add('slug', 'text', array(
        'label' => 'page.form.slug.label', 
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'page.form.slug.help', 
            'class' => 'input-xlarge'
        ), 
        'required' => false
    ));
    $builder->add('description', 'textarea', array(
        'label' => 'page.form.description.label', 
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'page.form.description.help', 
            'class' => 'input-xlarge'
        ), 
        'required' => false
    ));
    $builder->add('keywords', 'textarea', array(
        'label' => 'page.form.keywords.label', 
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'page.form.keywords.help', 
            'class' => 'input-xlarge'
        ), 
        'required' => false
    ));
    $builder->add('layout', null, array(
        'label' => 'page.form.layout.label', 
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'page.form.layout.help', 
            'class' => 'input-xlarge'
        ), 
        'required' => false
    ));
    $builder->add('status', 'choice', array(
        'label' => 'page.form.status.label', 
        'choices' => array(
            Page::STATUS_DTAFT => Page::STATUS_DTAFT, 
            Page::STATUS_PUBLISHED => Page::STATUS_PUBLISHED
        ), 
        'empty_value' => false,
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'page.form.status.help', 
            'class' => 'input-xlarge'
        ), 
        'required' => false
    ));
    $builder->add('parent_id', 'hidden');
  }
  
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.FormTypeInterface::getName()
   */
  public function getName()
  {
    return 'page';
  }
  
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.AbstractType::getDefaultOptions()
   */
  public function getDefaultOptions(array $options)
  {
    return array(
        'data_class' => 'Arnm\PagesBundle\Entity\Page'
    );
  }
}
