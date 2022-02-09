<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class, [
                'date_label' => 'Date et Heure de la Sortie: '
            ])
            ->add('duree', NumberType::class, [
                'label' => 'Durée: '
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', NumberType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('infosSortie')
            ->add('Campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'mapped' => false,
                'query_builder' => function(CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                }
            ])
            ->add('Ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'property_path' => 'nom',
                'placeholder' => '---Choisir une ville---',
                'mapped' => false,
                'query_builder' => function(VilleRepository $villeRepository) {
                    return $villeRepository->createQueryBuilder('v')->orderBy('v.nom', 'ASC');
                }
            ])
            ->add('Lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'property_path' => 'nom',
                'placeholder' => '---Choisir un lieu---',
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
