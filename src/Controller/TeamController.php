<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\RoleUser;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/team', name: 'app_team_')]
final class TeamController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(EmployeeRepository $repository): Response
    {
        $employees = $repository->findAll();
        return $this->render('team/index.html.twig', [
            'title' => 'Équipe',
            'employees' => $employees,
        ]);
    }

    #[IsGranted('ROLE_PROJECT_OWNER')]
    #[Route('/{id}', name: 'edit', requirements:  ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request,?Employee $employee): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee, [
            'roleATM' => RoleUser::from($employee->getUser()->getRoles()[0]),
        ])
                ->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $employee->getUser();
            $newRole[] = $form->get('role')->getData();
            $user->setRoles($newRole);
            $this->manager->persist($user);
            $this->manager->persist($employee);
            $this->manager->flush();
            $this->addFlash('success', sprintf("L'employé %s a bien été mis à jour", $employee->getFirstname() . ' ' . $employee->getLastname()));
            return $this->redirectToRoute('app_team_index');
        }
        return $this->render('team/edit.html.twig', [
            'title' => $employee->getFirstname() . ' ' . $employee->getLastname() ,
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_PROJECT_OWNER' )]
    #[Route('/delete/{id}', name: 'delete', requirements:  ['id' => '\d+'], methods: ['GET'])]
    public function delete(Request $request, Employee $employee): Response
    {
        $this->manager->remove($employee);
        $this->manager->flush();
        $this->addFlash('success', sprintf("L'employé %s a bien été supprimé", $employee->getFirstname() . ' ' . $employee->getLastname()));
        return $this->redirectToRoute('app_team_index');
    }
}
