<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ResetPasswordToken
{
    const EXPIRES_IN_HOURS = 6;

    /**
     * @ORM\Column(type="text", length=40)
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity="User")
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->createdAt = new \DateTime();
        $this->value = sha1(random_bytes(32));
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isValid()
    {
        $interval = (new \DateTime())->diff($this->createdAt);
        $totalHours = ($interval->days * 24) + $interval->h;

        return $totalHours <= self::EXPIRES_IN_HOURS;
    }
}