<?php

declare(strict_types=1);

namespace App\Dto\Api\Form;

class PaginatedPageTypeDto
{
    private ?int $page = null;
    private ?int $limit = null;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): PaginatedPageTypeDto
    {
        $this->page = $page;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): PaginatedPageTypeDto
    {
        $this->limit = $limit;

        return $this;
    }
}
