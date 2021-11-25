<?php

namespace App\Repository;

use App\Entity\SupportRequest;
use App\FilterOptionCollection\FilterOptionCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class SupportRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupportRequest::class);
    }

    public function findById(int $id): ?SupportRequest
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @return SupportRequest[]
     */
    public function getSupportRequestListByFilterOptionCollection(FilterOptionCollection $optionCollection): array
    {
        $queryBuilder = $this->createQueryBuilder('supportRequest')
        ->orderBy('supportRequest.createAt', Criteria::DESC);


        if ($optionCollection->getLimit()) {
            $queryBuilder->setMaxResults($optionCollection->getLimit());
        }

        if ($optionCollection->getLimit() && $optionCollection->getPage()) {
            $queryBuilder->setFirstResult($optionCollection->getOffset());
        }

        return $queryBuilder->getQuery()->getResult();
    }
}