<?php

namespace Arnm\PagesBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * This class is responsible for page header form
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PageHeaderType extends AbstractType
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
                'data-toggle' => 'popover',
            	'content' => 'page.form.title.help',
                'class' => 'form-control',
                'ng-model' => 'page.title'
            ),
            'required' => false
        ));
        $builder->add('description', 'textarea', array(
            'label' => 'page.form.description.label',
            'attr' => array(
                'data-toggle' => 'popover',
            	'content' => 'page.form.description.help',
                'class' => 'form-control',
                'ng-model' => 'page.description'
            ),
            'required' => false,
            'trim' => true
        ));
        $builder->add('keywords', 'textarea', array(
            'label' => 'page.form.keywords.label',
            'attr' => array(
                'data-toggle' => 'popover',
            	'content' => 'page.form.keywords.help',
                'class' => 'form-control',
                'ng-model' => 'page.keywords'
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
