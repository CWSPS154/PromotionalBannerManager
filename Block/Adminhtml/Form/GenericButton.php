<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Block\Adminhtml\Form;

use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @param Context $context
     * @param PromotionalBannerRepositoryInterface $promotionalBannerRepository
     */
    public function __construct(
        private readonly Context                              $context,
        private readonly PromotionalBannerRepositoryInterface $promotionalBannerRepository
    )
    {
    }

    /**
     * @return mixed|null
     */
    public function getBannerId()
    {
        try {
            $entityId = $this->context->getRequest()->getParam('entity_id');
            if ($entityId) {
                return $this->promotionalBannerRepository->getById($entityId)->getId();
            }
        } catch (NoSuchEntityException|LocalizedException $e) {
            $this->context->getLogger()->error($e->getMessage(), $e->getTrace());
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return  string
     */
    public function getUrl(string $route = '', array $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
