<?php

namespace App\Form;

use App\Entity\Bal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use App\Service\AppropriateUserForChat;
use App\Entity\BalGroupe;
use Doctrine\ORM\EntityManagerInterface;


class BalType extends AbstractType
{
    private $appropriate;
    private $em;
    
    function __construct(AppropriateUserForChat $app,EntityManagerInterface $em) {
        $this->appropriate=$app;
        $this->em=$em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('subject',TextType::class,[
                "label"=>false,
            ])
            ->add('destinatairegroupes',EntityType::class,[
                "label"=>false,
                'class' => BalGroupe::class,
                "query_builder"=>function(EntityRepository $er) use($user) {
                    $gs=$er->getAllGroupeWithMembers();
                    $groupes=[];
                    $mid=$user->getUser()->getId();
                    foreach ($gs as $g){
                        $mems=$g->getMembers();
                        foreach ($mems as $m){
                            if($m->getId()==$mid){
                               $groupes[]=$g->getId(); 
                            }
                        }
                    }
                    $qb=$er->createQueryBuilder('g');
                    return $qb->where($qb->expr()->in('g.id',':gs'))
                            ->setParameter('gs',$groupes);
                },
                'choice_label' => function($g){
                    return $g->getLibelle();
                },
                 "by_reference"=>false,
                "multiple"=>true,
                "required"=>false,
            ])
            ->add('destinataires',EntityType::class,[
                "label"=>false,
                'class' => User::class,
                "query_builder"=>function(EntityRepository $er) use($user) {
                    $rs=$this->appropriate->getUsers($user);
                    //dd();
                    if (in_array($user->getIdUser(), $rs)) {
                       unset($rs[array_search($user->getIdUser(), $rs)]);
                    }
                        $qb=$er->createQueryBuilder('u');
                    return $qb->where($qb->expr()->in('u.id',':arr'))
                            ->setParameter('arr',$rs);
                },
                'choice_label' => function($u){
                    //$u=$user->getUser();
                    return $u->getNom()." ".$u->getPrenom();
                },
                 "by_reference"=>false,
                "multiple"=>true,
                "required"=>false,
            ])
            ->add('message',TextareaType::class,[])                
            ->add('f',FileType::class,[
                'label'=>false,
                    'required'=>false,
                    'multiple'=>false,
                    'attr'=>[
                        'accept'=>'image/jpg,image/jpeg,image/gif,image/png,application/pdf',
                    ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bal::class,
        ]);
        $resolver->setRequired(['user']);
    }
}
