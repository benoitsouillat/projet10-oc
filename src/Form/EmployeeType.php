<?php

namespace App\Form;

use App\Entity\Employee;
use App\Enum\JobStatus;
use App\Enum\RoleUser;
use Composer\XdebugHandler\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roleATM = $options['roleATM'] ?? null;
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Prenom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('hireDate', DateType::class, [
                'label' => 'Date d\'entrée',
            ])
            ->add('status', EnumType::class, [
                'class' => JobStatus::class,
                'label' => 'Statut',
                'choice_label' => fn(JobStatus $status) => $status->label(),
            ])
            ->add('role', EnumType::class, [
                'class' => RoleUser::class,
                'label' => 'Rôle',
                'mapped' => false,
                'expanded' => false,
                'multiple' => false,
                'choice_label' => fn(RoleUser $role) => $role->label(),
                'data' => $roleATM ?? RoleUser::Collaborateur,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
        $resolver->setDefined(['roleATM']);
        $resolver->setAllowedTypes('roleATM', [RoleUser::class, 'null']);
    }
}
