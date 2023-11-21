<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\AcquisitionSystem;
use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Unique;

/**
 * Formulaire de création pour la classe AcquisitionSystem.
 *
 * @package App\Form
 */
class AcquisitionSystemType extends AbstractType
{
    /**
     * Construit le formulaire avec un nom et une room dans la liste des rooms.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            // Ajoute un champ pour le nom du système d'acquisition
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            // Ajoute un champ pour la sélection de la salle associé
            ->add('room', EntityType::class, [
                'label' => 'Salle',
                'class' => Room::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez une salle',
                'required' => false,
                'choices' => $options['unassociated_rooms'],
            ]);
    }

    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AcquisitionSystem::class,
            'unassociated_rooms' => [], // Ajoutez cette ligne pour définir la valeur par défaut
        ]);
    }
}
