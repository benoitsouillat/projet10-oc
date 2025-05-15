<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre du projet',
                'required' => true,
            ])
            ->add('teamList', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => function (Employee $employee) {
                    return $employee->getFirstname() . ' ' . $employee->getLastname();
                },
                'multiple' => true,
                'label' => "Inviter des membres",
                'by_reference' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.firstname', 'ASC');
                },
                'attr' => [
                    'class' => 'select2 select2-selection--multiple select2-selection--multiple select2-selection__choice',
                    ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
