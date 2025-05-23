<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[IsGranted('ROLE_ADMIN' )]
    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
    #[Route('/{id}', name: 'edit', requirements:  ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request,?Employee $employee = null): Response
    {
        if (!$employee) {
            $employee = new Employee();
        }

        $form = $this->createForm(EmployeeType::class, $employee)
                ->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($employee);
            $this->manager->flush();
            $this->addFlash('success', sprintf("L'employé %s a bien été mis à jour", $employee->getFirstname() . ' ' . $employee->getLastname()));
            return $this->redirectToRoute('app_team_index');
        }
        return $this->render('team/edit.html.twig', [
            'title' => !empty($employee) ? $employee->getFirstname() . ' ' . $employee->getLastname() : 'Ajouter un employé',
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN' )]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[Route('/delete/{id}', name: 'delete', requirements:  ['id' => '\d+'], methods: ['GET'])]
    public function delete(Request $request, Employee $employee): Response
    {
        $this->manager->remove($employee);
        $this->manager->flush();
        $this->addFlash('success', sprintf("L'employé %s a bien été supprimé", $employee->getFirstname() . ' ' . $employee->getLastname()));
        return $this->redirectToRoute('app_team_index');
    }
}
