<?php
namespace App\Form;

use App\Entity\Room;
use App\Entity\TechNotification;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', EntityType::class, [
                'label' => 'Salle',
                'class' => Room::class,
                'choice_label' => 'name',
                'choices' => $options['rooms']
            ])
            ->add('text', TextareaType::class, ['label' => 'Message'])
            // ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TechNotification::class,
            'rooms' => array()
        ]);
    }
}
