<?php

namespace Closas\AdminBundle\Form;

use Closas\AdminBundle\Entity\Upload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UploadType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('products', FileType::class, array('label' => 'Products (CSV file)'));
        $builder->add('save', SubmitType::class, array('label' => 'Save'));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => Upload::class,
        ));
    }

}
