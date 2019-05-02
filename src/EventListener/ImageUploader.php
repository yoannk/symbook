<?php

namespace App\EventListener;

use App\Entity\Image;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploader
{
    private $path;

    public function __construct($imagesPath)
    {
        $this->path = $imagesPath;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->upload($args);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->upload($args);
    }

    public function upload(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Image) {
            return;
        }

        $image = $entity;

        $fileName = md5(uniqid()).'.'.$image->getFile()->guessExtension();
        $image->setName($fileName);

        try {
            $image->getFile()->move($this->path, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
    }
}