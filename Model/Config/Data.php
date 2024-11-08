<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data
{
    public const CONFIG_BASE_PATH = 'cms/promotional_banner_settings';

    /**
     * @param ScopeConfigInterface $storeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        public readonly ScopeConfigInterface  $storeConfig,
        public readonly StoreManagerInterface $storeManager
    )
    {
    }

    /**
     * get this feature is enable or not
     *
     * @param int|null $storeId
     * @return bool|null
     * @throws NoSuchEntityException
     */
    public function isEnable(int $storeId = null): ?bool
    {
        return (bool)$this->storeConfig->getValue(
            self::CONFIG_BASE_PATH . '/enable',
            ScopeInterface::SCOPE_STORE,
            $storeId ?? $this->storeManager->getStore()->getId()
        );
    }

    /**
     * get the max_banners count
     *
     * @param int|null $storeId
     * @return int|null
     * @throws NoSuchEntityException
     */
    public function maxBanners(int $storeId = null): ?int
    {
        return (int)$this->storeConfig->getValue(
            self::CONFIG_BASE_PATH . '/max_banners',
            ScopeInterface::SCOPE_STORE,
            $storeId ?? $this->storeManager->getStore()->getId()
        );
    }

    /**
     * get the sorting_options
     *
     * @param int|null $storeId
     * @return int|null
     * @throws NoSuchEntityException
     */
    public function sortingOption(int $storeId = null): ?int
    {
        return (int)$this->storeConfig->getValue(
            self::CONFIG_BASE_PATH . '/sorting_option',
            ScopeInterface::SCOPE_STORE,
            $storeId ?? $this->storeManager->getStore()->getId()
        );
    }

}
