<?php

namespace NAO\PlatformBundle\Form;

use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ObservationsSearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('espece', SearchType::class, array(
                'label'=> 'Nom de l\'espèce',
                'required' => false
            ))
            ->add('validateur', SearchType::class, array(
                'label'=> 'Nom d\'utilisateur du naturaliste ayant validé l\'observation',
                'required' => false
            ))
            ->add('dateObs', SearchType::class, array(
                'label'=> 'Date de l\'observation (format JJ/MM/AAAA)',
                'required' => false
            ))
            ->add('rechercher', SubmitType::class);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }
}
