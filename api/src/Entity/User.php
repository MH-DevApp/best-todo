<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV6;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', message: 'Cette adresse email existe déjà.')]
#[UniqueEntity('pseudo', message: 'Ce pseudo existe déjà.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV6 $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[NotBlank(message: 'Une adresse email est requise.')]
    #[Email(message: 'Cette adresse email n\'est pas valide.')]
    #[Groups(['user:register'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]
    #[NotBlank(message: 'Un pseudo est requis.')]
    #[Regex(
        pattern: '/^[a-zA-Z]{2}[a-zA-Z0-9]*$/',
        message: 'Le pseudo doit commencer par au moins deux lettres et ne peut contenir que des lettres et des chiffres.'
    )]
    #[Length(
        min: 1,
        max: 255,
        minMessage: 'Votre pseudo doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Votre pseudo doit contenir au maximum {{ limit }} caractères.'
    )]
    #[Groups(['user:register'])]
    private ?string $pseudo = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    #[NotBlank(message: 'Un mot de passe est requis.')]
    #[Length(
        min: 8,
        max: 20,
        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Votre mot de passe doit contenir au maximum {{ limit }} caractères.'
    )]
    #[Regex(
        pattern: '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#\/\+\-\*\.])[a-zA-Z0-9@#\/\+\-\*]+$/',
        message: 'Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial (@#/*-+).'
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Length(
        min: 0,
        max: 255,
        maxMessage: 'Votre prénom ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Length(
        min: 0,
        max: 255,
        maxMessage: 'Votre nom ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $FPToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $FPExpiratedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CEToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $CEExpiratedAt = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Project::class)]
    private Collection $projectsOwner;

    #[ORM\OneToMany(mappedBy: 'collaborators', targetEntity: Project::class)]
    private Collection $projectsCollaborator;

    public function __construct()
    {
        $this->projectsOwner = new ArrayCollection();
        $this->projectsCollaborator = new ArrayCollection();
    }

    public function getId(): ?UuidV6
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

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

    public function getFPToken(): ?string
    {
        return $this->FPToken;
    }

    public function setFPToken(?string $FPToken): static
    {
        $this->FPToken = $FPToken;

        return $this;
    }

    public function getFPExpiratedAt(): ?\DateTimeImmutable
    {
        return $this->FPExpiratedAt;
    }

    public function setFPExpiratedAt(?\DateTimeImmutable $FPExpiratedAt): static
    {
        $this->FPExpiratedAt = $FPExpiratedAt;

        return $this;
    }

    public function getCEToken(): ?string
    {
        return $this->CEToken;
    }

    public function setCEToken(?string $CEToken): static
    {
        $this->CEToken = $CEToken;

        return $this;
    }

    public function getCEExpiratedAt(): ?\DateTimeImmutable
    {
        return $this->CEExpiratedAt;
    }

    public function setCEExpiratedAt(?\DateTimeImmutable $CEExpiratedAt): static
    {
        $this->CEExpiratedAt = $CEExpiratedAt;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjectsOwner(): Collection
    {
        return $this->projectsOwner;
    }

    public function addProjectOwner(Project $project): static
    {
        if (!$this->projectsOwner->contains($project)) {
            $this->projectsOwner->add($project);
            $project->setOwner($this);
        }

        return $this;
    }

    public function removeProjectOwner(Project $project): static
    {
        if ($this->projectsOwner->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getOwner() === $this) {
                $project->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjectsCollaborator(): Collection
    {
        return $this->projectsCollaborator;
    }

    public function addProjectsCollaborator(Project $project): static
    {
        if (!$this->projectsCollaborator->contains($project)) {
            $this->projectsCollaborator->add($project);
            $project->setOwner($this);
        }

        return $this;
    }

    public function removeProjectsCollaborator(Project $project): static
    {
        if ($this->projectsCollaborator->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getOwner() === $this) {
                $project->setOwner(null);
            }
        }

        return $this;
    }
}
