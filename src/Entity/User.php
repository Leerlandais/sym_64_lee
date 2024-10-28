<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $fullname = null;

    #[ORM\Column(length: 60)]
    private ?string $uniqid = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $activate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getUniqid(): ?string
    {
        return $this->uniqid;
    }

    public function setUniqid(string $uniqid): static
    {
        $this->uniqid = $uniqid;

        return $this;
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

    public function isActivate(): ?bool
    {
        return $this->activate;
    }

    public function setActivate(bool $activate): static
    {
        $this->activate = $activate;

        return $this;
    }
}
