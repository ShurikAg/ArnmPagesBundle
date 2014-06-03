<?php
namespace Arnm\PagesBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Layout form use to manage layouts as well as gets embedded into page form
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PageLayoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('layout', 'entity', array(
            'class' => 'ArnmPagesBundle:Layout',
            'property' => 'layout',
            'label' => 'page.form.layout.label',
            'attr' => array(
                'data-toggle' => 'popover',
                'content' => 'page.form.layout.help',
                'class' => 'form-control',
                'ng-model' => 'page.layout.id'
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
        return 'page';
    }

    /**
     * {@inheritdoc}
     * @see Symfony\Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arnm\PagesBundle\Entity\Page',
            'csrf_protection' => false
        ));
    }
}
