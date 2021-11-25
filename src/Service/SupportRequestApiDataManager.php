<?php

namespace App\Service;

use App\FilterOptionCollection\FilterOptionCollection;
use App\Repository\SupportRequestRepository;

class SupportRequestApiDataManager
{
    private SupportRequestRepository $supportRequestRepository;

    public function __construct(SupportRequestRepository $supportRequestRepository)
    {
        $this->supportRequestRepository = $supportRequestRepository;
    }

    public function getSupportRequestDataListByFilterOptionCollection(FilterOptionCollection $optionCollection): array
    {
        $dataList = [];
        $supportRequestList = $this->supportRequestRepository->getSupportRequestListByFilterOptionCollection($optionCollection);

        foreach ($supportRequestList as $supportRequest) {
            $dataList[] = $supportRequest->getData();
        }

        return $dataList;
    }
}