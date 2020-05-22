<?php
declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Provider;

use Setono\SyliusCatalogPromotionPlugin\Repository\PromotionRepositoryInterface;

final class ProcessablePromotionsProvider implements ProcessablePromotionsProviderInterface
{
    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    public function __construct(PromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function get(): array
    {
        return $this->promotionRepository->findEnabledWithChannel(['exclusive' => 'asc', 'priority' => 'asc']);
    }
}
