<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Controller\Adminhtml\Index;

use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'CWSPS154_PromotionalBannerManager::create_banner';
    public const ADMIN_RESOURCE_EDIT = 'CWSPS154_PromotionalBannerManager::edit_banner';

    /**
     * @param Context $context
     * @param PromotionalBannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        Context                                               $context,
        private readonly PromotionalBannerRepositoryInterface $bannerRepository,
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface|Page
     */
    public function execute(): ResultInterface|ResponseInterface|Page
    {
        $entity_id = $this->getRequest()->getParam('entity_id');
        if ($entity_id) {
            try {
                $title = $this->bannerRepository->getById($entity_id)->getTitle();
            } catch (NoSuchEntityException|LocalizedException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                return $this->_redirect('*/*');
            }
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Backend::content_elements');
        $resultPage->addBreadcrumb(__('Promotional Banner Manager'), __('Promotional Banner Manager'));
        $resultPage->addBreadcrumb(__('Banners'), __('Banners'));
        $resultPage->addBreadcrumb(
            $entity_id ? __('Edit '. $title) : __('Add New'),
            $entity_id ? __('Edit '. $title) : __('Add New')
        );
        $resultPage->getConfig()->getTitle()->prepend(
            $entity_id ? __('Edit '. $title) : __('Add New')
        );

        return $resultPage;
    }

    /**
     * Determines whether current user is allowed to access the action
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        if ($this->getRequest()->getParam('entity_id')) {
            return $this->_authorization->isAllowed(self::ADMIN_RESOURCE_EDIT);
        }
        return parent::_isAllowed();
    }
}
