<?php
namespace Arnm\PagesBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Template form use to manage Templates as well as gets embedded into page form
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class AreaType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('code', 'text', array(
        'label' => 'area.form.code.label',
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'area.form.code.help',
            'domain' => 'area', 
            'class' => 'span4'
        ), 
        'required' => false
    ));
  }
  
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.FormTypeInterface::getName()
   */
  public function getName()
  {
    return 'area';
  }
  
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.AbstractType::getDefaultOptions()
   */
  public function getDefaultOptions(array $options)
  {
    return array(
        'data_class' => 'Arnm\PagesBundle\Entity\Area'
    );
  }
}
