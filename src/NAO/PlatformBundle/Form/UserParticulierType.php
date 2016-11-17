<?php

namespace NAO\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class UserParticulierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('profession')
            ->remove('cv')
            ->remove('motivation')
        ;
    }

    public function getParent()
    {
        return UserType::class;
    }

}
