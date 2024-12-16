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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'CWSPS154_PromotionalBannerManager::delete_banner';

    /**
     * @param Context $context
     * @param PromotionalBannerRepositoryInterface $promotionalBannerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                                               $context,
        private readonly PromotionalBannerRepositoryInterface $promotionalBannerRepository,
        private readonly LoggerInterface                      $logger
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $entity_id = $this->getRequest()->getParam('entity_id');
        if ($entity_id) {
            try {
                $this->promotionalBannerRepository->deleteById($entity_id);
                $this->messageManager->addSuccessMessage(__('Banner Data is Deleted Successfully.'));
            } catch (CouldNotDeleteException|LocalizedException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find the banner data to delete.'));
        }

        return $this->_redirect('*/*');
    }
}
