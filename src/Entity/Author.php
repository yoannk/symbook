<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
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
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\OneToMany(targetEntity="Book", mappedBy="author", cascade={"persist"})
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $name): void
    {
        $this->firstname = $name;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $name): void
    {
        $this->lastname = $name;
    }

    /**
     * @return Book[]|ArrayCollection
     */
    public function getBooks(): ArrayCollection
    {
        return $this->books;
    }

    public function addBook(Book $book): void
    {
        $book->setAuthor($this);
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
        }
    }

    public function removeBook(Book $book): void
    {
        $this->books->removeElement($book);
    }

    public function fullname(): string
    {
        return $this->firstname.' '.$this->lastname;
    }
}
