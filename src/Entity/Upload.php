<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UploadRepository")
 */
class Upload
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"basic"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"basic"})
     */
    private $internalFileName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"basic"})
     */
    private $originalFileName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"basic"})
     */
    private $title;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getInternalFileName()
    {
        return $this->internalFileName;
    }

    /**
     * @param string $internalFileName
     * @return Upload
     */
    public function setInternalFileName(string $internalFileName): self
    {
        $this->internalFileName = $internalFileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }

    /**
     * @param string $originalFileName
     * @return Upload
     */
    public function setOriginalFileName(string $originalFileName): self
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     * @return Upload
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
