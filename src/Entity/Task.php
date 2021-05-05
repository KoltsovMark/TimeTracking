<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @ORM\Table(name="tasks")
 * @JMS\ExclusionPolicy("all")
 */
class Task
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose()
     */
    private $comment;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    private $timeSpent;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose()
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTimeSpent(): ?int
    {
        return $this->timeSpent;
    }

    public function setTimeSpent(int $timeSpent): self
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
