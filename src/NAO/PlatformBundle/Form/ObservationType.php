<?php

namespace NAO\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObservationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('espece', 'PUGX\AutocompleterBundle\Form\Type\AutocompleteType', ['class' => 'NAO\PlatformBundle\Entity\Espece'])
            ->add('lat', NumberType::class)
            ->add('lon', NumberType::class)
            ->add('localise', CheckboxType::class, array(
                'label'    => 'Je suis sur place',
                'required' => false,
            ))
            ->add('dateObs', DateType::class, array(
                'input' => 'datetime',
                'format' => 'dd/MM/yyyy H:m',
                'widget' => 'single_text'

            ))
            ->add('photo', FileType::class, array(
                'required' => false
            ))
            ->add('commentaireP', TextareaType::class, array(
                'required' => false
            ))
            ->add('valider',SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NAO\PlatformBundle\Entity\Observation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'nao_platformbundle_observation';
    }


}
