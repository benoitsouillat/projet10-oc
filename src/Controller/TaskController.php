<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task', name: 'app_task_')]
final class TaskController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {

    }
    #[Route('/{id}', name: 'edit', requirements:  ['id' => '\d+'], methods: ['GET',  'POST'])]
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task, [
            'teamList' =>  $task->getProject()->getTeamList(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($task);
            $this->manager->flush();
            $this->addFlash('warning', sprintf("La tâche %s a bien été mise à jour", $task->getTitle()));

            return $this->redirectToRoute('app_project_show', ['id' => $task->getProject()->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'title' => $task->getTitle(),
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements:  ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delete(Request $request, Task $task): Response
    {
        $this->manager->remove($task);
        $this->manager->flush();
        $this->addFlash('danger', sprintf("La tâche %s a bien été supprimée", $task->getTitle()));
        return $this->redirectToRoute('app_project_index');
    }
}
