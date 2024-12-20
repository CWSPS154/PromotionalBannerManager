<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
interface PromotionalBannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterface[]
     */
    public function getItems();

    /**
     * @param \CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
