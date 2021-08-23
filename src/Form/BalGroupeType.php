<?php

namespace App\Form;

use App\Entity\BalGroupe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\AppropriateUserForChat;
use App\Repository\UserJoinedEnseigneRepository;
use Doctrine\ORM\EntityRepository;

class BalGroupeType extends AbstractType
{
    private $appropriate;
    private $em;
    
    function __construct(AppropriateUserForChat $app,EntityManagerInterface $em) {
        $this->appropriate=$app;
         $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('libelle',TextType::class,[
                "label"=>"Nom du groupe",
                "attr"=>[
                    "placeholder"=>"Entrez un nom",
                    "length"=>255,
                ]
            ])
            ->add('members',EntityType::class,[
                "label"=>"Ajouter au moins un membre",
                'class' => User::class,
                "query_builder"=>function(EntityRepository $er) use($user) {
                    $rs=$this->appropriate->getUsers($user);
                   /* $tmp=[];
                    foreach ($rs as $r){
                       $tmp[]=$r->getIdUser(); 
                    }*/
                    $qb= $this->em->getRepository(User::class)->createQueryBuilder('u');
                    return $qb->where($qb->expr()->in('u.id',':arr'))
                            ->setParameter('arr',$rs);
                },
                'choice_label' => function($user){
                    //$u=$user->getUser();
                    return $user->getNom()." ".$user->getPrenom();
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
            'data_class' => BalGroupe::class,
        ]);
        $resolver->setRequired(['user']);
    }
}
