<?php

namespace App\Form;

use App\Entity\Participant;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', EntityType::class, [
                'mapped' => false,
                'label' => "Pseudo de l'utilisateur à supprimer: ",
                'class' => Participant::class,
                'choice_label' => 'pseudo'
            ]);
    }

}
