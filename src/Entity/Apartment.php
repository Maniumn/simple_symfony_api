<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApartmentRepository")
 */
class Apartment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"basic", "uploads"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups("basic")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups("basic")
     */
    private $legacyId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups("basic")
     */
    private $address;

    /**
     * Unidirectional - Many apartments have many uploads
     *
     * @ORM\ManyToMany(targetEntity="Upload")
     * @ORM\JoinTable(name="apartments_uploads")
     * @Serializer\Groups("uploads")
     */
    private $uploads;

    public function __construct()
    {
        $this->uploads = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Apartment
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLegacyId(): ?string
    {
        return $this->legacyId;
    }

    /**
     * @param null|string $legacyId
     * @return Apartment
     */
    public function setLegacyId(?string $legacyId): self
    {
        $this->legacyId = $legacyId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Apartment
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return ArrayCollection<Upload>
     */
    public function getUploads()
    {
        return $this->uploads;
    }

    /**
     * @param array<Upload> $uploads
     * @return Apartment
     */
    public function setUploads(array $uploads): self
    {
        $this->uploads = $uploads;

        return $this;
    }

    /**
     * @param Upload $upload
     * @return $this
     */
    public function addUpload(Upload $upload): self
    {
        if ($this->uploads->contains($upload))
            return $this;

        $this->uploads->add($upload);

        return $this;
    }

    public function removeUpload(Upload $upload): self
    {
        if ($this->uploads->contains($upload))
            $this->uploads->removeElement($upload);

        return $this;
    }
}
