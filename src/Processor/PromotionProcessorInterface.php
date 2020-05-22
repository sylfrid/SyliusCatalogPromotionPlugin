<?php
declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Processor;

interface PromotionProcessorInterface
{
    public function process(bool $force = false): void;
}
