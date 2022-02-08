<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('idCampus', EntityType::class, [
                'label' => 'Campus: ',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'query_builder' => function(CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                }
            ])
            ->add('idLieu', CollectionType::class, [
                'entry_type' => LieuType::class,
                'allow_add' => true,
                'entry_options' => ['label' => false]
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
