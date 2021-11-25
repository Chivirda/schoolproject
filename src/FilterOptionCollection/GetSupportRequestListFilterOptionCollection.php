<?php

namespace App\FilterOptionCollection;

use Symfony\Component\HttpFoundation\Request;

class GetSupportRequestListFilterOptionCollection extends FilterOptionCollection
{
    public static function buildFromRequest(Request $request): FilterOptionCollection
    {
        $limit = $request->request->get(self::FILTER_LIMIT_KEY);
        $page = $request->request->get(self::FILTER_PAGE_KEY);

        $optionCollection = new self();

        if ($limit) {
            $optionCollection->setLimit($limit);
        }

        if ($page) {
            $optionCollection->setPage($page);
        }

        return $optionCollection;
    }
}