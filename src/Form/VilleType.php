<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('codePostal')
            ->add('Lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'mapped' => false,
                'placeholder' => '---Selectionner une ville---',
                'query_builder' => function(LieuRepository $lieuRepository) {
                    return $lieuRepository->createQueryBuilder('v')->orderBy('v.nom', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions (OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
