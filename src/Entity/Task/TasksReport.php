<?php

declare(strict_types=1);

namespace App\Entity\Task;

use App\Entity\User;
use App\Repository\Task\TasksReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=TasksReportRepository::class)
 * @ORM\Table(name="tasks_reports")
 * @JMS\ExclusionPolicy("all")
 */
class TasksReport
{
    use TimestampableEntity;

    public const STORAGE_FILE = 1;

    public const STORAGE_TYPE_LOCAL = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @JMS\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     */
    private $storage;

    /**
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     */
    private $storageType;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $storageName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $storageFullPath;

    /**
     * @ORM\Column(type="json", length=255, nullable=true)
     */
    private $reportOptions;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reports")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStorage(): ?int
    {
        return $this->storage;
    }

    public function setStorage(int $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getStorageType(): ?int
    {
        return $this->storageType;
    }

    public function setStorageType(int $storageType): self
    {
        $this->storageType = $storageType;

        return $this;
    }

    public function getStorageName(): ?string
    {
        return $this->storageName;
    }

    public function setStorageName(string $storageName): self
    {
        $this->storageName = $storageName;

        return $this;
    }

    public function getReportOptions(): ?array
    {
        return $this->reportOptions;
    }

    public function setReportOptions(array $reportOptions): self
    {
        $this->reportOptions = $reportOptions;

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

    public function getStorageFullPath(): ?string
    {
        return $this->storageFullPath;
    }

    public function setStorageFullPath(string $storageFullPath): self
    {
        $this->storageFullPath = $storageFullPath;

        return $this;
    }
}
