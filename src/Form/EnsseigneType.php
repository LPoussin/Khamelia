<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Departement;
use App\Entity\Ensseigne;
use App\Entity\Quartier;
use App\Entity\TypeEns;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnsseigneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle')
            ->add('telephone')
            ->add('urlsite')
            ->add('enseigneType', EntityType::class, [
                'class' => TypeEns::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Selectionner le type d\'enseigne',

            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'placeholder' => 'Selectionner le pays',
                'mapped' => false,
                'choice_label' => 'nicename',
                'empty_data' => ''

            ]);
        
        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->addDepartementField($form->getParent(), $form->getData());
            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();

                $quartier = $data->getQuartier();
                $form = $event->getForm();
                if($quartier){
                    $ville = $quartier->getVille();
                    $departement = $ville->getDepartement();
                    $country = $departement->getCountry();
                    $this->addDepartementField($form, $country);
                    $this->addVilleField($form, $departement);
                    $this->addQuartierField($form, $ville);
    
                    $form->get('country')->setData($country);
                    $form->get('departement')->setData($departement);
                    $form->get('ville')->setData($ville);
                } else{
                    $this->addDepartementField($form, null);
                    $this->addVilleField($form, null);
                    $this->addQuartierField($form, null);
                }
            }
        );
    }


    /**
     * Rajoute un champ departement au formulaire
     *
     * @param FormInterface $form
     * @param Country $country
     */
    private function addDepartementField(FormInterface $form, ?Country $country)
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'departement',
            EntityType::class,
            null,
            [
                'class' => Departement::class,
                'placeholder' => $country ? 'Selectionner un departement': 'Seletionner d\'abord un pays',
                'choices' => $country ? $country->getDepartements(): [],
                'choice_label' => 'libelle',
                'mapped' => false,
                'auto_initialize' => false,
                'required' => false,
                'empty_data' => ''
            ]
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                dump($form->getData());
                $this->addVilleField($form->getParent(),$form->getData());
            }
        );
        $form->add($builder->getForm());
    }


    /**
     * Rajoute un champ ville au formulaire
     *
     * @param FormInterface $form
     * @param Departement $departement
     */
    private function addVilleField(FormInterface $form,?Departement $departement)
    {
        dump($departement);
            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
                'ville',
                EntityType::class,
                null,
                [
                    'class' => Ville::class,
                    'placeholder' => $departement ? 'Selectionner une ville': 'Selectionner d\'abord un departement ',
                    'choices' => $departement ? $departement->getVilles(): [],
                    'choice_label' => 'libelle',
                    'mapped' => false,
                    'auto_initialize' => false,
                    'required' => false,
                    'empty_data' => ''
                ]
            );
    
            $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $this->addQuartierField($form->getParent(),$form->getData());
                }
            );
            $form->add($builder->getForm());
    }

    /**
     * Rajoute un champ quartier au formulaire
     *
     * @param FormInterface $form
     * @param Ville $ville
     */
    private function addQuartierField(FormInterface $form, ?Ville $ville)
    {
            $form->add(
                'quartier',
                EntityType::class,
                [
                    'class' => Quartier::class,
                    'placeholder' => $ville ? 'Selectionner un quartier': 'Selectionner une ville d\'abord !',
                    'choices' => $ville ? $ville->getQuartiers(): [],
                    'choice_label' => 'libelle',
                    'empty_data' => ''
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ensseigne::class,
        ]);
    }
}
