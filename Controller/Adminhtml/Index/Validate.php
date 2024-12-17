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
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Validate extends Action implements HttpGetActionInterface
{
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
     * @return ResultInterface|ResponseInterface
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        $entityId = $this->getRequest()->getParam('entity_id');
        $priority = (int)$this->getRequest()->getParam('priority');
        $startDate = $this->getRequest()->getParam('start_date');
        $endDate = $this->getRequest()->getParam('end_date');
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $this->bannerRepository->validateUniquePrioritySchedule($priority, $startDate, $endDate, $entityId);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultJson->setData(['status' => false, 'message' => $e->getMessage()]);
        }
        return $resultJson->setData(['status' => true, 'message' => __('Valid')]);
    }
}
