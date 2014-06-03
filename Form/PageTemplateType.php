<?php
namespace Arnm\PagesBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Template form use to manage layouts as well as gets embedded into page form
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PageTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('template', 'entity', array(
            'class' => 'ArnmPagesBundle:Template',
            'property' => 'name',
            'label' => 'page.form.template.label',
            'attr' => array(
                'data-toggle' => 'popover',
                'content' => 'page.form.template.help',
                'class' => 'form-control',
                'ng-model' => 'page.template.id'
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
        return 'template';
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
