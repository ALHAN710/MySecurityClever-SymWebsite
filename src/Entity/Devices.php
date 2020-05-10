<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\DevicesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Devices
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner un nom pour le périphérique")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alerte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moduleId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url
     */
    private $streamingUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notificationMessage;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $connectionState;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Ipaddress;

    /**
     * Permet d'initialiser le slug !
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void
     */
    public function initializeSlug()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAlerte(): ?string
    {
        return $this->alerte;
    }

    public function setAlerte(?string $alerte): self
    {
        $this->alerte = $alerte;

        return $this;
    }

    public function getModuleId(): ?string
    {
        return $this->moduleId;
    }

    public function setModuleId(?string $module): self
    {
        $this->moduleId = $module;

        return $this;
    }

    public function getStreamingUrl(): ?string
    {
        return $this->streamingUrl;
    }

    public function setStreamingUrl(?string $streamingUrl): self
    {
        $this->streamingUrl = $streamingUrl;

        return $this;
    }

    public function getNotificationMessage(): ?string
    {
        return $this->notificationMessage;
    }

    public function setNotificationMessage(?string $notificationMessage): self
    {
        $this->notificationMessage = $notificationMessage;

        return $this;
    }

    public function getConnectionState(): ?bool
    {
        return $this->connectionState;
    }

    public function setConnectionState(?bool $connectionState): self
    {
        $this->connectionState = $connectionState;

        return $this;
    }

    public function getActivation(): ?bool
    {
        return $this->activation;
    }

    public function setActivation(?bool $activation): self
    {
        $this->activation = $activation;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIpaddress(): ?string
    {
        return $this->Ipaddress;
    }

    public function setIpaddress(?string $Ipaddress): self
    {
        $this->Ipaddress = $Ipaddress;

        return $this;
    }
}
