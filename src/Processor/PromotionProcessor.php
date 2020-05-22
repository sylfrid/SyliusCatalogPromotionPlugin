<?php
declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Processor;

use DateTimeImmutable;
use Setono\SyliusCatalogPromotionPlugin\Provider\ProcessablePromotionsProviderInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\ChannelPricingRepositoryInterface;
use Setono\SyliusCatalogPromotionPlugin\Resolver\ResourcesUpdatedResolverInterface;
use Setono\SyliusCatalogPromotionPlugin\Rule\ManuallyDiscountedProductsExcludedRule;
use Setono\SyliusCatalogPromotionPlugin\Rule\RuleInterface;

final class PromotionProcessor implements PromotionProcessorInterface
{
    /**
     * @var ProcessablePromotionsProviderInterface
     */
    private $promotionsProvider;
    /**
     * @var PromotionProcessorContextInterface
     */
    private $promotionProcessorContext;
    /**
     * @var ResourcesUpdatedResolverInterface
     */
    private $resourcesUpdatedResolver;
    /**
     * @var ChannelPricingRepositoryInterface
     */
    private $channelPricingRepository;

    public function __construct(
        ProcessablePromotionsProviderInterface $promotionsProvider,
        PromotionProcessorContextInterface $promotionProcessorContext,
        ResourcesUpdatedResolverInterface $resourcesUpdatedResolver,
        ChannelPricingRepositoryInterface $channelPricingRepository
    ) {
        $this->promotionsProvider = $promotionsProvider;
        $this->promotionProcessorContext = $promotionProcessorContext;
        $this->resourcesUpdatedResolver = $resourcesUpdatedResolver;
        $this->channelPricingRepository = $channelPricingRepository;
    }

    public function process(bool $force = false): void
    {
        if ($this->promotionProcessorContext->isRunning()) {
            return;
        }

        $startTime = new DateTimeImmutable();

        $promotions = $this->promotionsProvider->get();
        if (count($promotions) === 0) {
            return;
        }

        if (!$force
            && $this->promotionProcessorContext->hasPriorExecution()
            && !$this->promotionProcessorContext->promotionsChanged($promotions)
            && !$this->resourcesUpdatedResolver->resolve($this->promotionProcessorContext->getLastExecutionStart()) // despite being the same and same order we still need to check whether any relevant entities were updated
        ) {
            return;
        }

        $this->channelPricingRepository->resetMultiplier($startTime);

        foreach ($promotions as $promotion) {
            $qb = $this->productVariantRepository->createQueryBuilder('o');
            $qb->select('o.id');
            $qb->distinct();

            if ($promotion->isManuallyDiscountedProductsExcluded()) {
                (new ManuallyDiscountedProductsExcludedRule())->filter($qb, []);
            }

            foreach ($promotion->getRules() as $rule) {
                if ($rule->getType() === null) {
                    continue;
                }

                if (!$this->ruleRegistry->has($rule->getType())) {
                    // todo should this throw an exception or give an error somewhere?
                    continue;
                }

                /** @var RuleInterface $ruleQueryBuilder */
                $ruleQueryBuilder = $this->ruleRegistry->get($rule->getType());

                $ruleQueryBuilder->filter($qb, $rule->getConfiguration());
            }

            $bulkSize = 100;
            $qb->setMaxResults($bulkSize);
            $i = 0;

            do {
                $qb->setFirstResult($i * $bulkSize);
                $productVariantIds = $qb->getQuery()->getResult();

                $this->channelPricingRepository->updateMultiplier(
                    $promotion->getMultiplier(), $productVariantIds, $promotion->getChannelCodes(), $startTime,
                    $promotion->isExclusive()
                );

                ++$i;
            } while (count($productVariantIds) !== 0);
        }

        $this->channelPricingRepository->updatePrices($startTime);

        $this->promotionProcessorContext->saveExecution($startTime, new DateTimeImmutable(), $promotions);
    }
}
