<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Controller\Adminhtml\Index;

use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterface;
use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterfaceFactory;
use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Psr\Log\LoggerInterface;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'CWSPS154_PromotionalBannerManager::create_banner';

    /**
     * @param Context $context
     * @param PromotionalBannerInterfaceFactory $bannerInterfaceFactory
     * @param PromotionalBannerRepositoryInterface $bannerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                                               $context,
        private readonly PromotionalBannerInterfaceFactory    $bannerInterfaceFactory,
        private readonly PromotionalBannerRepositoryInterface $bannerRepository,
        private readonly LoggerInterface                      $logger
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface
    {
        /** @var Request|RequestInterface $request */
        $request = $this->getRequest();
        if (!$this->_formKeyValidator->validate($request)) {
            $this->messageManager->addErrorMessage(__("Form key is Invalidate"));
            return $this->_redirect('*/*');
        }
        $data = $request->getPostValue();
        $model = $this->bannerInterfaceFactory->create();
        if (!empty($data['entity_id'])) {
            $model->setId($data['entity_id']);
        }
        $model->setTitle($data['title']);
        $model->setDescription($data['description']);
        $model->setPriority((int)$data['priority']);
        $model->setStartDate(date('Y-m-d', strtotime($data['start_date'])));
        $model->setEndDate(date('Y-m-d', strtotime($data['end_date'])));
        $model->setImage($data['image']);
        $model->setStatus((int)$data['status']);
        try {
            $this->bannerRepository->save($model);
            $this->messageManager->addSuccessMessage(__('Banner Data is Saved Successfully.'));
            return $this->processResultRedirect($model);
        } catch (InputException|LocalizedException|CouldNotSaveException|Exception $e) {
            $this->handleException($e);
        }
        if ($model->getId()) {
            return $this->_redirect('*/*/edit', [
                'entity_id' => $model->getId(),
                '_current' => true,
            ]);
        }
        return $this->_redirect('*/*/new');
    }

    /**
     * Process result redirect based on request parameters
     *
     * @param PromotionalBannerInterface $model
     * @return ResponseInterface
     */
    private function processResultRedirect(PromotionalBannerInterface $model): ResponseInterface
    {
        $backParam = $this->getRequest()->getParam('back');
        if ($backParam === 'edit' && $model->getId()) {
            return $this->_redirect('*/*/edit', [
                'entity_id' => $model->getId(),
                '_current' => true,
            ]);
        }

        return $this->_redirect('*/*');
    }

    /**
     * Handle exceptions during data saving
     *
     * @param Exception $exception
     * @return void
     */
    private function handleException(Exception $exception): void
    {
        if (method_exists($exception, 'getErrors')) {
            $messages = array_map(function ($error) {
                return $error->getMessage();
            }, $exception->getErrors());
        } else {
            $messages = [$exception->getMessage()];
        }

        $errorMessage = implode(',', $messages);
        $this->logger->error($errorMessage, $exception->getTrace());
        $this->messageManager->addErrorMessage(__('Error(s) occurred: %1', $errorMessage));
    }
}
