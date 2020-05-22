<?php
declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Resolver;

use DateTimeInterface;

interface ResourcesUpdatedResolverInterface
{
    /**
     * Returns true if any relevant resources have been updated since the given date
     */
    public function resolve(DateTimeInterface $dateTime): bool;
}
