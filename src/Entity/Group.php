<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="groups")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etherpadId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $finalText;

    /**
     * @ORM\ManyToOne(targetEntity=Step::class, inversedBy="groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $step;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getRandomUser()
    {
        return $this->users[rand(0, count($this->users) - 1)];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    public function getEtherpadId(): ?string
    {
        return $this->etherpadId;
    }

    public function setEtherpadId(string $etherpadId): self
    {
        $this->etherpadId = $etherpadId;

        return $this;
    }

    public function getFinalText(): ?string
    {
        return $this->finalText;
    }

    public function setFinalText(?string $finalText): self
    {
        $this->finalText = $finalText;

        return $this;
    }

    public function getStep(): ?Step
    {
        return $this->step;
    }

    public function setStep(?Step $step): self
    {
        $this->step = $step;

        return $this;
    }
}
