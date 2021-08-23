<?php

namespace App\Form;

use App\Entity\Bal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\BalGroupe;
use Doctrine\ORM\EntityRepository;

class DestinataireGroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destinatairegroupes',EntityType::class,[
                "label"=>false,
                'class' => BalGroupe::class,
                "query_builder"=>function(EntityRepository $er) use($user) {
                    $qb=$er->createQueryBuilder('g');
                    return $qb->leftJoin('g.members','m')
                            ->addSelect("m")
                            ->where($qb->expr()->in(':userj',':members'))
                            ->setParameter('userj','m');
                },
                'choice_label' => function($user){
                    $u=$user->getUser();
                    return $u->getNom()." ".$u->getPrenom();
                },
                 "by_reference"=>false,
                "multiple"=>true,
                "required"=>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bal::class,
        ]);
    }
}
