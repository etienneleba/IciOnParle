<?php

namespace App\Entity;

use App\Repository\SocialNetworkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SocialNetworkRepository::class)
 */
class SocialNetwork
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity=SocialNetworkType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $socialNetworkType;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="socialNetworks")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getSocialNetworkType(): ?SocialNetworkType
    {
        return $this->socialNetworkType;
    }

    public function setSocialNetworkType(?SocialNetworkType $socialNetworkType): self
    {
        $this->socialNetworkType = $socialNetworkType;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }
}
