<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchCampusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', CampusType::class, [
                'choices' => [
                    array_combine(Campus::nom, Campus::nom)
                ]
            ])
            ->add('recherche', SubmitType::class);
    }
}