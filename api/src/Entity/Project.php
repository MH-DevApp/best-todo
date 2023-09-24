<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV6;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[UniqueEntity('title', message: 'Ce titre existe déjà, veuillez en saisir un autre.')]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV6 $id = null;

    #[ORM\Column(length: 255)]
    #[NotBlank(message: 'Un titre est requis.')]
    #[Length(
        min: 1,
        max: 255,
        minMessage: 'Votre titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Votre titre doit contenir au maximum {{ limit }} caractères.'
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isFavorite = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastUpdatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'projectsOwner')]
    private ?User $owner = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projectsCollaborator')]
    private Collection $collaborators;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: PermissionProject::class, orphanRemoval: true)]
    private Collection $permissionProjects;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Label::class, orphanRemoval: true)]
    private Collection $labels;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class, orphanRemoval: true)]
    private Collection $tasks;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Log::class)]
    private Collection $logs;

    public function __construct()
    {
        $this->collaborators = new ArrayCollection();
        $this->permissionProjects = new ArrayCollection();
        $this->labels = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    public function getId(): ?UuidV6
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isIsFavorite(): ?bool
    {
        return $this->isFavorite;
    }

    public function setIsFavorite(bool $isFavorite): static
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->lastUpdatedAt;
    }

    public function setLastUpdatedAt(?\DateTimeImmutable $lastUpdatedAt): static
    {
        $this->lastUpdatedAt = $lastUpdatedAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    public function addCollaborator(User $collaborator): static
    {
        if (!$this->collaborators->contains($collaborator)) {
            $this->collaborators->add($collaborator);
        }

        return $this;
    }

    public function removeCollaborator(User $collaborator): static
    {
        $this->collaborators->removeElement($collaborator);

        return $this;
    }

    /**
     * @return Collection<int, PermissionProject>
     */
    public function getPermissionProjects(): Collection
    {
        return $this->permissionProjects;
    }

    public function addPermissionProject(PermissionProject $permissionProject): static
    {
        if (!$this->permissionProjects->contains($permissionProject)) {
            $this->permissionProjects->add($permissionProject);
            $permissionProject->setProject($this);
        }

        return $this;
    }

    public function removePermissionProject(PermissionProject $permissionProject): static
    {
        if ($this->permissionProjects->removeElement($permissionProject)) {
            // set the owning side to null (unless already changed)
            if ($permissionProject->getProject() === $this) {
                $permissionProject->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Label>
     */
    public function getLabels(): Collection
    {
        return $this->labels;
    }

    public function addLabel(Label $label): static
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
            $label->setProject($this);
        }

        return $this;
    }

    public function removeLabel(Label $label): static
    {
        if ($this->labels->removeElement($label)) {
            // set the owning side to null (unless already changed)
            if ($label->getProject() === $this) {
                $label->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
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
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProject($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProject() === $this) {
                $comment->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Log>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): static
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setProject($this);
        }

        return $this;
    }

    public function removeLog(Log $log): static
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getProject() === $this) {
                $log->setProject(null);
            }
        }

        return $this;
    }
}
