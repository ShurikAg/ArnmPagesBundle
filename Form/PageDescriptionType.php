<?php

namespace Arnm\PagesBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * This class is responsible for creating NewPageForm object
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PageDescriptionType extends AbstractType
{
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.AbstractType::buildForm()
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('description', 'textarea', array(
        'label' => 'page.form.description.label', 
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'page.form.description.help', 
            'class' => 'span4'
        ), 
        'required' => false,
        'trim' => true
    ));
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
