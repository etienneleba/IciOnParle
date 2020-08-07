<?php

namespace App\Entity;

use App\Repository\StepRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StepRepository::class)
 */
class Step
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $rank;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="steps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity=Group::class, mappedBy="step", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $groups;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finalStep = false;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getNbParticipants()
    {
        $nb = 0;
        foreach ($this->getGroups() as $group) {
            $nb += count($group->getUsers());
        }

        return $nb;
    }

    public function getNbGroups()
    {
        return count($this->getGroups());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setStep($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            // set the owning side to null (unless already changed)
            if ($group->getStep() === $this) {
                $group->setStep(null);
            }
        }

        return $this;
    }

    public function getFinalStep(): ?bool
    {
        return $this->finalStep;
    }

    public function setFinalStep(bool $finalStep): self
    {
        $this->finalStep = $finalStep;

        return $this;
    }
}
