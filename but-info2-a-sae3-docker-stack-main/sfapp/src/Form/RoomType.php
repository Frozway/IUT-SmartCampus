<?php

namespace App\Form;

use App\Entity\Room;
use App\Repository\DepartmentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création pour la classe Room.
 *
 * @package App\Form
 */
class RoomType extends AbstractType
{
    private DepartmentRepository $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Construit le formulaire avec un nom, un étage et un département.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('floor', IntegerType::class, [
                'label' => 'Étage',
            ])
            ->add('department', EntityType::class, [
                'class' => 'App\Entity\Department',
                'choices' => $this->departmentRepository->findAll(),
                'choice_label' => 'name',
                'placeholder' => 'Aucun département selectionné', // Texte d'invite
                'choice_value' => function ($department) {
                    return $department ? $department->getId() : null;
                },
                'required' => false,
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
            'data_class' => Room::class,
        ]);
    }
}
