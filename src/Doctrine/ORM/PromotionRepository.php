<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Doctrine\ORM;

use DateTimeInterface;
use Safe\DateTime;
use Setono\SyliusCatalogPromotionPlugin\Repository\PromotionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class PromotionRepository extends EntityRepository implements PromotionRepositoryInterface
{
    use HasAnyBeenUpdatedSinceTrait;

    public function findEnabledWithChannel(array $orderBy = null, DateTimeInterface $dateTime = null): array
    {
        if(null === $dateTime) {
            $dateTime = new DateTime();
        }

        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.enabled = true')
            ->andWhere('SIZE(o.channels) > 0')
            ->andWhere('o.startsAt is null OR o.startsAt <= :date')
            ->andWhere('o.endsAt is null OR o.endsAt >= :date')
//            ->addOrderBy('o.exclusive', 'ASC')
//            ->addOrderBy('o.priority', 'ASC')
            ->setParameter('date', $dateTime)
        ;

        $this->applySorting($qb, $orderBy);

        return $qb->getQuery()->getResult();
    }
}
