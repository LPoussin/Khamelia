<?php

namespace App\Form;

use App\Entity\TypeEns;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class TypeEnsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class)
            ->add('libelle', TextType::class)
            ->add('slug', TextType::class)
            ->add('description', TextareaType::class)
            ->add('serie', CheckboxType::class, [
                'label' => 'Subdivision des niveaux d\'étude en série',
                'required' => false
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Activé',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypeEns::class,
        ]);
    }
}
