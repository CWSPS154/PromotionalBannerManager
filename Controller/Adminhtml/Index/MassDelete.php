<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\PromotionalBannerManager\Controller\Adminhtml\Index;

use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use CWSPS154\PromotionalBannerManager\Model\ResourceModel\PromotionalBanner\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'CWSPS154_PromotionalBannerManager::delete_banner';

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param PromotionalBannerRepositoryInterface $promotionalBannerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                                               $context,
        private readonly Filter                               $filter,
        private readonly CollectionFactory                    $collectionFactory,
        private readonly PromotionalBannerRepositoryInterface $promotionalBannerRepository,
        private readonly LoggerInterface                      $logger
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $deleteCount = 0;
        $deletedError = 0;
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            foreach ($collection as $item) {
                $this->promotionalBannerRepository->delete($item);
                $deleteCount++;
            }
        } catch (CouldNotDeleteException|LocalizedException $e) {
            $deletedError++;
            $this->logger->error($e->getMessage(), $e->getTrace());
        }

        if ($deleteCount) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $deleteCount)
            );
        }

        if ($deletedError) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been deleted. Please see error logs for more details.',
                    $deletedError
                )
            );
        }

        /** @var ResultInterface $resultPage */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
