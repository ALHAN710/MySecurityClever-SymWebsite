<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecuritySystemRepository")
 */
class SecuritySystem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivation(): ?bool
    {
        return $this->activation;
    }

    public function setActivation(bool $activation): self
    {
        $this->activation = $activation;

        return $this;
    }
}
