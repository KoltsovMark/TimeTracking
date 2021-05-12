<?php

declare(strict_types=1);

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DoctrineManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param object $object the instance to make managed and persistent
     *
     * @return object
     */
    public function save(object $object)
    {
        $this->entityManager->getConnection()->beginTransaction(); // suspend auto-commit

        try {
            $this->entityManager->persist($object);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }

        return $object;
    }
}
