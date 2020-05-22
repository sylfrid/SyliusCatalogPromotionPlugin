<?php
declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Resolver;

use DateTimeInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\HasAnyBeenUpdatedSinceRepositoryInterface;

final class ResourcesUpdatedResolver implements ResourcesUpdatedResolverInterface
{
    /**
     * @var HasAnyBeenUpdatedSinceRepositoryInterface[]
     */
    private $repositories;

    public function __construct(HasAnyBeenUpdatedSinceRepositoryInterface ...$repositories)
    {
        $this->repositories = $repositories;
    }

    public function resolve(DateTimeInterface $dateTime): bool
    {
        foreach ($this->repositories as $repository) {
            if($repository->hasAnyBeenUpdatedSince($dateTime)) {
                return true;
            }
        }

        return false;
    }
}
