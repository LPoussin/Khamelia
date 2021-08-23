<?php

namespace App\Form;

use App\Entity\PatientApi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientApiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenoms')
            ->add('email', EmailType::class)
            ->add('telephone')
            ->add('genre',ChoiceType::class, [
                'choices'  => [
                    'Choisir le genre' => null,
                    'Masculin' => 'm',
                    'Feminin' => 'f',
                ],
            ])
            ->add('mdp',PasswordType::class,[
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('confirm_mdp',PasswordType::class,[
                'label' => 'Mot de passe de confirmation',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PatientApi::class,
        ]);
    }
}
