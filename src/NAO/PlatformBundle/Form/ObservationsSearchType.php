<?php

namespace NAO\PlatformBundle\Form;

use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ObservationsSearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('espece', SearchType::class, array(
                'label' => 'Nom de l\'espèce',
                'required' => false
            ))
            ->add('dateObs', DateType::class, array(
                'label' => 'Jour de l\'observation (format JJ-MM-AAAA)',
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd/MM/yyyy',
                'required' => false,
                'attr' => ['class' => 'datepicker']
            ))
            ->add('user', SearchType::class, array(
                'label' => 'Nom d\'utilisateur de l\'observateur',
                'required' => false
            ))
            ->add('validateur', SearchType::class, array(
                'label' => 'Nom d\'utilisateur du naturaliste ayant traité l\'observation',
                'required' => false
            ))
            ->add('rechercher', SubmitType::class, array(
                'attr' => [ 'class' => 'btn btn-customp']
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
