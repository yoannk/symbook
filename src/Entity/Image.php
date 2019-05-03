<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Image
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
    private $name;

    /**
     * @var UploadedFile
     * @Assert\File(
     *     maxSize = "1024k",
     *     maxSizeMessage="L'image ne doit pas dépasser 1Mo",
     *     mimeTypes={"image/jpeg", "image/png"},
     *     mimeTypesMessage="L'image doit être au format .jpg ou .png"
     * )
     */
    private $file;

    private $path;

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

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path): void
    {
        $this->path = $path;
    }

    /**
     * @ORM\PreFlush()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }
        if ($this->id && file_exists($this->path.'/'.$this->name)) {
            unlink($this->path.'/'.$this->name);
        }

        $fileName = md5(uniqid()).'.'.$this->file->guessExtension();
        $this->setName($fileName);

        try {
            $this->file->move($this->path, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
    }
}
