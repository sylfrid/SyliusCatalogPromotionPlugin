<?php
declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Provider;

use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;

interface ProcessablePromotionsProviderInterface
{
    /**
     * @return PromotionInterface[]
     */
    public function get(): array;
}
