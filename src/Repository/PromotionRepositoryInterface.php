<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Repository;

use DateTimeInterface;
use Doctrine\ORM\Query\Expr\OrderBy;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PromotionRepositoryInterface extends RepositoryInterface, HasAnyBeenUpdatedSinceRepositoryInterface
{
    /**
     * Will return enabled promotions with at least one enabled channel
     * - If the $dateTime argument is set it will return promotions that was active on this date, otherwise today will be used
     *
     * @return PromotionInterface[]
     */
    public function findEnabledWithChannel(array $orderBy = null, DateTimeInterface $dateTime = null): array;
}
