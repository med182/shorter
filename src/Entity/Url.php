<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
#[ORM\Table(name: 'Urls')]
class Url
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $original = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $shortener = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginal(): ?string
    {
        return $this->original;
    }

    public function setOriginal(string $original): self
    {
        $this->original = $original;

        return $this;
    }

    public function getShortener(): ?string
    {
        return $this->shortener;
    }

    public function setShortener(string $shortener): self
    {
        $this->shortener = $shortener;

        return $this;
    }
}
