<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Api;

use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterface;
use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface PromotionalBannerRepositoryInterface
{
    /**
     * Save PromotionalBannerInterface
     *
     * @param PromotionalBannerInterface $promotionalBanner
     * @return PromotionalBannerInterface
     * @throws LocalizedException
     * @throws CouldNotSaveException
     */
    public function save(PromotionalBannerInterface $promotionalBanner);

    /**
     * Get PromotionalBannerInterface by ID
     *
     * @param int $entityId
     * @return PromotionalBannerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId);

    /**
     * Get PromotionalBannerInterface List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return PromotionalBannerSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete PromotionalBannerInterface
     *
     * @param PromotionalBannerInterface $promotionalBanner
     * @return bool
     * @throws LocalizedException
     * @throws CouldNotDeleteException
     */
    public function delete(PromotionalBannerInterface $promotionalBanner);

    /**
     * Delete PromotionalBannerInterface by ID
     *
     * @param int $entityId
     * @return bool
     * @throws LocalizedException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $entityId);

    /**
     * Validate that no other banner has the same priority and overlapping schedule
     *
     * @param int $priority
     * @param string $startDate
     * @param string $endDate
     * @param int|null $bannerId
     * @return void
     * @throws LocalizedException
     */
    public function validateUniquePrioritySchedule(int $priority, string $startDate, string $endDate, ?int $bannerId);
}
