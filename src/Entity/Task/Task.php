<?php

declare(strict_types=1);

namespace App\Entity\Task;

use App\Entity\User;
use App\Repository\Task\TaskRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @ORM\Table(name="tasks", indexes={
 *  @ORM\Index(columns={"user_id", "date"}),
 * })
 * @JMS\ExclusionPolicy("all")
 */
class Task
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @JMS\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    private $title;

    /**
     * @todo move to a separate table on a big data sets
     *
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose()
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
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

    /**
     * @return Collection|TasksReport[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(TasksReport $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setTask($this);
        }

        return $this;
    }

    public function removeReport(TasksReport $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getTask() === $this) {
                $report->setTask(null);
            }
        }

        return $this;
    }
}
