<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\UuidV6;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
class Label
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV6 $id = null;

    #[ORM\Column(length: 255)]
    #[NotBlank(message: 'Un nom est requis.')]
    #[Length(
        min: 1,
        max: 255,
        minMessage: 'Le nom du label doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom du label doit contenir au maximum {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 6)]
    #[Regex(
        pattern: '/^[0-9A-Fa-f]{6}$/',
        message: 'La couleur doit être un nombre hexadécimal.'
    )]
    private ?string $color = null;

    #[ORM\Column(length: 6)]
    #[Regex(
        pattern: '/^[0-9A-Fa-f]{6}$/',
        message: 'La couleur de fond doit être un nombre hexadécimal.'
    )]
    private ?string $bgColor = null;

    #[ORM\ManyToOne(inversedBy: 'labels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'labels')]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?UuidV6
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getBgColor(): ?string
    {
        return $this->bgColor;
    }

    public function setBgColor(string $bgColor): static
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

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
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        $this->tasks->removeElement($task);

        return $this;
    }
}
