<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\User;
use App\Enum\JobStatus;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();
            if ($plainPassword !== $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas');
                return $this->redirectToRoute('app_register');
            }
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $employee = new Employee();
            $employee->setFirstname($user->getFirstname())
                    ->setLastname($user->getLastname())
                    ->setEmail($user->getEmail())
                    ->setHireDate(new \DateTime())
                    ->setStatus(JobStatus::toDefine)
                    ->setUser($user);
            $user->setEmployee($employee);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'title' => 'S\'inscrire'
        ]);
    }
}
