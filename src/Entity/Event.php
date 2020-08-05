<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbDaysFirstStep;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbDaysStep;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbDaysLastStep;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbMaxUser;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbMaxUsersPerGroup;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbMinUsersPerGroup;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbMinUsersFinalGroup;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Step::class, mappedBy="event", orphanRemoval=true)
     */
    private $steps;

    /**
     * @ORM\OneToMany(targetEntity=Source::class, mappedBy="event", orphanRemoval=true)
     */
    private $sources;

    /**
     * @ORM\OneToMany(targetEntity=UserEvent::class, mappedBy="event", orphanRemoval=true, cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $userEvents;

    public function __construct()
    {
        $this->steps = new ArrayCollection();
        $this->sources = new ArrayCollection();
        $this->userEvents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function isRegistered(User $user)
    {
        /** @var UserEvent $userEvent */
        foreach ($this->userEvents as $userEvent) {
            if ($userEvent->getUser()->getId() == $user->getId()) {
                return true;
            }
        }

        return false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getNbDaysFirstStep(): ?int
    {
        return $this->nbDaysFirstStep;
    }

    public function setNbDaysFirstStep(int $nbDaysFirstStep): self
    {
        $this->nbDaysFirstStep = $nbDaysFirstStep;

        return $this;
    }

    public function getNbDaysStep(): ?int
    {
        return $this->nbDaysStep;
    }

    public function setNbDaysStep(int $nbDaysStep): self
    {
        $this->nbDaysStep = $nbDaysStep;

        return $this;
    }

    public function getNbDaysLastStep(): ?int
    {
        return $this->nbDaysLastStep;
    }

    public function setNbDaysLastStep(int $nbDaysLastStep): self
    {
        $this->nbDaysLastStep = $nbDaysLastStep;

        return $this;
    }

    public function getNbMaxUser(): ?int
    {
        return $this->nbMaxUser;
    }

    public function setNbMaxUser(int $nbMaxUser): self
    {
        $this->nbMaxUser = $nbMaxUser;

        return $this;
    }

    public function getNbMaxUsersPerGroup(): ?int
    {
        return $this->nbMaxUsersPerGroup;
    }

    public function setNbMaxUsersPerGroup(int $nbMaxUsersPerGroup): self
    {
        $this->nbMaxUsersPerGroup = $nbMaxUsersPerGroup;

        return $this;
    }

    public function getNbMinUsersPerGroup(): ?int
    {
        return $this->nbMinUsersPerGroup;
    }

    public function setNbMinUsersPerGroup(int $nbMinUsersPerGroup): self
    {
        $this->nbMinUsersPerGroup = $nbMinUsersPerGroup;

        return $this;
    }

    public function getNbMinUsersFinalGroup(): ?int
    {
        return $this->nbMinUsersFinalGroup;
    }

    public function setNbMinUsersFinalGroup(int $nbMinUsersFinalGroup): self
    {
        $this->nbMinUsersFinalGroup = $nbMinUsersFinalGroup;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Step[]
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps[] = $step;
            $step->setEvent($this);
        }

        return $this;
    }

    public function removeStep(Step $step): self
    {
        if ($this->steps->contains($step)) {
            $this->steps->removeElement($step);
            // set the owning side to null (unless already changed)
            if ($step->getEvent() === $this) {
                $step->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Source[]
     */
    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addSource(Source $source): self
    {
        if (!$this->sources->contains($source)) {
            $this->sources[] = $source;
            $source->setEvent($this);
        }

        return $this;
    }

    public function removeSource(Source $source): self
    {
        if ($this->sources->contains($source)) {
            $this->sources->removeElement($source);
            // set the owning side to null (unless already changed)
            if ($source->getEvent() === $this) {
                $source->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserEvent[]
     */
    public function getUserEvents(): Collection
    {
        return $this->userEvents;
    }

    public function addUserEvent(UserEvent $userEvent): self
    {
        if (!$this->userEvents->contains($userEvent)) {
            $this->userEvents[] = $userEvent;
            $userEvent->setEvent($this);
        }

        return $this;
    }

    public function removeUserEvent(UserEvent $userEvent): self
    {
        if ($this->userEvents->contains($userEvent)) {
            $this->userEvents->removeElement($userEvent);
            // set the owning side to null (unless already changed)
            if ($userEvent->getEvent() === $this) {
                $userEvent->setEvent(null);
            }
        }

        return $this;
    }
}
