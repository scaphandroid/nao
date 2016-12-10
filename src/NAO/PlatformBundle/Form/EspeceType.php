<?php

namespace NAO\PlatformBundle\Form;

use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EspeceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomConcat', AutocompleteType::class, ['class' => 'NAO\PlatformBundle\Entity\Espece'])
            ->add('rechercher', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NAO\PlatformBundle\Entity\Espece'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'nao_platformbundle_espece';
    }


}
