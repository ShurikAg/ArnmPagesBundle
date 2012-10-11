<?php

namespace Arnm\PagesBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * This class is responsible for creating NewPageForm object
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class NewPageType extends AbstractType
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
            'class' => 'span4'
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
