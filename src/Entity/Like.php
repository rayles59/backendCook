<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\User;
use App\Entity\Recette;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isLike = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?user $user = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?recette $recette = null;

    public function __construct()
    {
        $this->recette = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsLike(): ?bool
    {
        return $this->isLike;
    }

    public function setIsLike(bool $isLike): self
    {
        $this->isLike = $isLike;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRecette(): ?recette
    {
        return $this->recette;
    }

    public function setRecette(?recette $recette): self
    {
        $this->recette = $recette;

        return $this;
    }
}
