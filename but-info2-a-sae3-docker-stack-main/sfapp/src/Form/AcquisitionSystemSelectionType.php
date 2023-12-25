<?php

namespace App\Form;

use App\Entity\AcquisitionSystem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de sélection du Système d'acquisition pour une salle.
 *
 * @package App\Form
 */
class AcquisitionSystemSelectionType extends AbstractType
{
    /**
     * Construit le formulaire avec la liste des systèmes d'acquisition non assignés.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Récupère la liste des systèmes d'acquisition non assignés depuis les options
        $unassignedAcquisitionSystems = $options['unassigned_acquisition_systems'];

        // Ajoute un champ pour la sélection du système d'acquisition
        $builder
            ->add('acquisitionSystem', EntityType::class, [
                'label' => 'Sélectionner un Système d\'acquisition',
                'class' => AcquisitionSystem::class,
                'choices' => $unassignedAcquisitionSystems,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un système d\'acquisition',
                'required' => false,
            ]);
    }

    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'unassigned_acquisition_systems' => [], // Valeur par défaut pour la liste des systèmes d'acquisition non assignés
        ]);
    }
}
