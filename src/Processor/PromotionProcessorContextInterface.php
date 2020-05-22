<?php
declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Processor;

use DateTimeInterface;

interface PromotionProcessorContextInterface
{
    /**
     * Returns true if the processor is already running
     */
    public function isRunning(): bool;

    /**
     * Returns true if the promotion processor has been run before
     */
    public function hasPriorExecution(): bool;

    /**
     * Returns true if the given promotions does not match the promotions on the last processing run
     * Matches in this context also means the order of promotions since this is important because of priority between promotions
     */
    public function promotionsChanged(array $promotions): bool;

    /**
     * Returns the start time of the last execution and null if no prior execution exists
     */
    public function getLastExecutionStart(): ?DateTimeInterface;

    public function saveExecution(DateTimeInterface $startTime, DateTimeInterface $endTime, array $promotions): void;
}
