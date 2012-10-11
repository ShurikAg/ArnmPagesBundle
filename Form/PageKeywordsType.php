<?php

namespace Arnm\PagesBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * This class is responsible for creating Keywords of the a page object
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PageKeywordsType extends AbstractType
{
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.AbstractType::buildForm()
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('keywords', 'textarea', array(
        'label' => 'page.form.keywords.label', 
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'page.form.keywords.help', 
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
