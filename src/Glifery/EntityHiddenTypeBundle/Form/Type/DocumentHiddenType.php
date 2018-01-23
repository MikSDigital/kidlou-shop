<?php

namespace App\Glifery\EntityHiddenTypeBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use App\Glifery\EntityHiddenTypeBundle\Form\DataTransformer\ObjectToIdTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentHiddenType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ObjectToIdTransformer(
                $this->registry, $options['dm'], $options['class'], $options['property'], $options['multiple']
        );
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['class'])
            ->setDefaults(
                    [
                        'data_class' => null,
                        'invalid_message' => 'The document does not exist.',
                        'property' => 'id',
                        'dm' => 'default',
                        'multiple' => false,
                    ]
            )
            ->setAllowedTypes('invalid_message', ['null', 'string'])
            ->setAllowedTypes('property', ['null', 'string'])
            ->setAllowedTypes('multiple', ['boolean'])
            ->setAllowedTypes('dm', ['null', 'string', 'Doctrine\Common\Persistence\ObjectManager']);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
