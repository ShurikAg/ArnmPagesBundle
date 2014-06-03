<?php
namespace Arnm\PagesBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Layout form use to manage layouts as well as gets embedded into page form
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class LayoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('layout', 'text', array(
            'label' => 'layout.form.layout.label',
            'attr' => array(
                'data-toggle' => 'popover',
            	'content' => 'layout.form.layout.help',
                'class' => 'form-control'
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
        return 'layout';
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::getDefaultOptions()
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Arnm\PagesBundle\Entity\Layout'
        );
    }
}
