<?php

namespace App\FilterOptionCollection;

use Symfony\Component\HttpFoundation\Request;

abstract class FilterOptionCollection
{
    protected const FILTER_LIMIT_KEY = 'limit';
    protected const FILTER_PAGE_KEY  = 'page';

    protected ?int $limit = null;
    protected ?int $page = null;

    abstract public static function buildFromRequest(Request $request): self;

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    public function getOffset(): ?int
    {
        if ($this->getLimit() && $this->getPage()) {
            return $this->getPage() < 1 ? 0 : ($this->getPage() - 1) * $this->getLimit();
        }

        return null;
    }
}