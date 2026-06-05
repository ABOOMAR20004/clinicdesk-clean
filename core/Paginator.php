<?php

declare(strict_types=1);

final class Paginator
{
    public function __construct(
        private int $totalItems,
        private int $perPage,
        private int $currentPage
    ) {
        $this->currentPage = max(1, $this->currentPage);
        $this->perPage = max(1, $this->perPage);
    }

    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function totalPages(): int
    {
        return max(1, (int) ceil($this->totalItems / $this->perPage));
    }

    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages();
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }
}
