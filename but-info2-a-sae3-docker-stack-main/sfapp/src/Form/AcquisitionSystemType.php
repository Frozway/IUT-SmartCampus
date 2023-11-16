<?php

namespace App\Form;

use App\Entity\AcquisitionSystem;
use App\Entity\Room;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AcquisitionSystemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'name', // Remplacez 'name' par le champ de Room que vous souhaitez afficher
                'placeholder' => 'Sélectionnez une salle', // Optionnel : affiche un placeholder
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AcquisitionSystem::class,
        ]);
    }
}
