<?php

namespace App\Form;

use App\Entity\NiveauxEtudes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\TypeEns;
use App\Repository\TypeEnsRepository;

class NiveauxEtudesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typeEnseigne', EntityType::class, [
                'label' => 'Type d\'enseigne',
                'class' => TypeEns::class,
                'query_builder' => function (TypeEnsRepository $typeEnsRepository)
                {
                    return $typeEnsRepository->createQueryBuilder('type')
                                             ->andWhere('type.active = 1');
                },
                'choice_label' => 'libelle',
            ])
            ->add('code')
            ->add('libelle')
            ->add('slug')
            ->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NiveauxEtudes::class,
        ]);
    }
}
