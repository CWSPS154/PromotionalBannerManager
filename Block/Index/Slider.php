<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Block\Index;

use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterface;
use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerSearchResultsInterface;
use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use CWSPS154\PromotionalBannerManager\Model\Config\Data;
use CWSPS154\PromotionalBannerManager\Model\Config\Source\Sorting;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Slider extends Template
{
    /**
     * @param Context $context
     * @param PromotionalBannerRepositoryInterface $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrder $sortOrder
     * @param Data $configData
     * @param array $data
     */
    public function __construct(
        Template\Context                             $context,
        private PromotionalBannerRepositoryInterface $bannerRepository,
        private SearchCriteriaBuilder                $searchCriteriaBuilder,
        private SortOrder                            $sortOrder,
        private Data                                 $configData,
        array                                        $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get Banner details
     *
     * @return PromotionalBannerSearchResultsInterface
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws LocalizedException
     */
    public function getBanners()
    {
        $maxBanners = $this->configData->maxBanners();
        $sortingOption = $this->configData->sortingOption();
        if ($sortingOption == Sorting::ASCE) {
            $direction = SortOrder::SORT_ASC;
        } else {
            $direction = SortOrder::SORT_DESC;
        }
        $this->sortOrder->setField(PromotionalBannerInterface::PRIORITY);
        $this->sortOrder->setDirection($direction);

        $searchCriteria = $this->searchCriteriaBuilder
            ->addSortOrder($this->sortOrder)
            ->setPageSize($maxBanners)
            ->setCurrentPage(1)
            ->create();

        return $this->bannerRepository->getList($searchCriteria);
    }

    /**
     * Checking is this module is enabled
     *
     * @return bool|null
     * @throws NoSuchEntityException
     */
    public function isEnabled(): ?bool
    {
        return $this->configData->isEnable();
    }
}
