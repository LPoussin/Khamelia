<?php

namespace App\Form;

use App\Entity\TypeEval;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\CategorieEval;
use App\Repository\CategorieEvalRepository;


class TypeEvalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('libelle')
            ->add('slug')
            ->add('description')
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie d\'évaluation',
                'class' => CategorieEval::class,
                'query_builder' => function (CategorieEvalRepository $categorieEvalRepository)
                {
                    return $categorieEvalRepository->createQueryBuilder('categorie');
                },
                'choice_label' => 'libelle',
            ])
            ->add('isActif')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypeEval::class,
        ]);
    }
}
