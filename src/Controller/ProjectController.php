<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\ProjectType;
use App\Form\TaskType;
use App\Security\Voter\ProjectVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProjectController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('', name: 'index')]
    #[Route('/project', name: 'app_project_index', methods: ['GET'])]
    public function index(Security $security): Response
    {
        $projects = $this->isGranted('ROLE_PROJECT_OWNER') ?
            $this->manager->getRepository(Project::class)->findAllActive() :
            $this->manager->getRepository(Project::class)->findAllByUser($security->getUser()->getId());

        return $this->render('project/index.html.twig', [
            'title' => 'Projets',
            'projects' => $projects,
        ]);
    }

    #[Route('/project/{id}', name: 'app_project_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ACCESS', subject: 'project')]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'title' => $project->getName(),
            'project' => $project,
            'todo' => $this->manager->getRepository(Task::class)->findAllByStatus('to do', $project->getId()),
            'doing' => $this->manager->getRepository(Task::class)->findAllByStatus('doing', $project->getId()),
            'done' => $this->manager->getRepository(Task::class)->findAllByStatus('done', $project->getId()),
        ]);
    }

    #[IsGranted('ROLE_PROJECT_OWNER')]
    #[Route('/project/add', name: 'app_project_add', methods: ['GET', 'POST'])]
    #[Route('/project/{id}/edit', name: 'app_project_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Project $project = null): Response
    {
        if ($project === null) {
            $project = new Project();
        }
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($project);
            $this->manager->flush();

            return $this->redirectToRoute('app_project_index');
        }
        return $this->render('project/edit.html.twig', [
            'form' => $form,
            'project' => $project,
            'title' => !empty($project->getName()) ? $project->getName() : "Créer un nouveau projet",
        ]);
    }

    #[IsGranted('ROLE_PROJECT_OWNER' )]
    #[Route('/project/delete/{id}', name: 'app_project_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(Project $project): Response
    {
        $project->setArchive(true);
        $this->manager->persist($project);
        $this->manager->flush();
        $this->addFlash('success', sprintf("Le projet %s a bien été archivé avec toutes ses tâches", htmlspecialchars($project->getName())));
        return $this->redirectToRoute('app_project_index');
    }

    /*#[IsGranted('ROLE_PROJECT_OWNER' )]*/
    #[IsGranted('ACCESS', subject: 'project')]
    #[Route('/project/{id}/add-task', name: 'app_project_add-task', methods: ['GET', 'POST'])]
    public function addTask(Request $request, Project $project): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, [
            'teamList' =>  $project->getTeamList(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setProject($project);
            $this->manager->persist($task);
            $this->manager->flush();
            $this->addFlash('success', sprintf("La tâche %s a bien été ajoutée", $task->getTitle()));
            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }
        return $this->render('task/edit.html.twig', [
            'title' => !empty($task->getTitle()) ? $task->getTitle() : 'Créer une tâche',
            'form' => $form->createView(),
        ]);
    }
}
