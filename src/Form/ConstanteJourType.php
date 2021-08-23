<?php

namespace App\Form;

use App\Entity\ConstanteJour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConstanteJourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_constante')
            ->add('id_infirmier')
            ->add('id_specialite')
            ->add('id_patient')
            ->add('libelle_cst')
            ->add('created_at')
            ->add('id_enseigne')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConstanteJour::class,
        ]);
    }
}
