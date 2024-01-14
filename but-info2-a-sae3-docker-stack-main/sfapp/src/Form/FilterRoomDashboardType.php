<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterRoomDashboardType extends AbstractType
{
    /**
     * @brief Define the form
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Floor', IntegerType::class,
                [
                    'label' => 'Etage',
                    'required' => false,
                    'empty_data' => null,
                ])
            ->add('isAssigned', CheckboxType::class,
                [
                    'label' => 'Système d\'aquisition attribué',
                    'required' => false,
                ])
            ->add('SearchAS', SearchType::class,
                [
                    'label' => 'Nom du système d\'aquisition',
                    'required' => false,
                    'autocomplete' => true,
                    'empty_data' => null,

                ])
            ->add('SearchRoom', Searchtype::class,
                [
                    'label' => 'Nom de la salle',
                    'required' => false,
                    'autocomplete' => true,
                    'empty_data' => null,

                ])
            ->add('Valid', Submittype::class,
                [
                    'label' => 'Valider',
                ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
