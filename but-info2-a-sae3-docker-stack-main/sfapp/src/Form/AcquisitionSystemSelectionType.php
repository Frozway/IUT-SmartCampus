<?php

namespace App\Form;

use App\Entity\AcquisitionSystem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class AcquisitionSystemSelectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $unassignedAcquisitionSystems = $options['unassigned_acquisition_systems'];

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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'unassigned_acquisition_systems' => [],
        ]);
    }
}