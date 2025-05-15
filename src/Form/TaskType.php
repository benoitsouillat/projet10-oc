<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Repository\EmployeeRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la tâche',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'label' => 'Statut',
                'class' => TaskStatus::class,
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'firstname',
                'multiple' => false,
                'label' => "Membre",
                'required' => false,
                'choices' => $options['teamList'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'teamList' => null,
        ]);
    }
}
