<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'CWSPS154_PromotionalBannerManager::listing';

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|Page
     */
    public function execute(): ResultInterface|Page
    {
        /** @var ResultInterface $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Backend::content_elements');
        $resultPage->addBreadcrumb(__('Promotional Banner Manager'), __('Promotional Banner Manager'));
        $resultPage->addBreadcrumb(__('Banners'), __('Banners'));
        $resultPage->getConfig()->getTitle()->prepend(__('Banners Listing'));

        return $resultPage;
    }
}
