<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class, [
                'date_label' => 'Date et Heure de la Sortie: ',
                'html5' => true,
                'widget' => 'single_text'
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
