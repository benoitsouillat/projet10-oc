<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('name', message: "Ce nom de projet est déjà utilisé")]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le nom du projet est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom du projet doit contenir au minimum 3 caractères",
        maxMessage: "Le nom du projet ne peut excéder 255 caractères"
    )]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\ManyToMany(targetEntity: Employee::class, inversedBy: 'projects')]
    private Collection $teamList;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project', cascade: ['persist', 'remove'])]
    private Collection $tasks;

    #[ORM\Column(options: ['default' => false])]
    private bool $archive = false;

    public function __construct()
    {
        $this->teamList = new ArrayCollection();
        $this->tasks = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getTeamList(): Collection
    {
        return $this->teamList;
    }

    public function addTeamList(Employee $teamList): static
    {
        if (!$this->teamList->contains($teamList)) {
            $this->teamList->add($teamList);
        }

        return $this;
    }

    public function removeTeamList(Employee $teamList): static
    {
        $this->teamList->removeElement($teamList);

        return $this;
    }
    public function getTasks(): ?Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);
        }
        return $this;
    }
    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // Set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }
        return $this;
    }

    public function isArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): static
    {
        $this->archive = $archive;

        return $this;
    }

}
