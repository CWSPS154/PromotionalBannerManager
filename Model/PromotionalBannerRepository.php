<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Model;

use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterface;
use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterfaceFactory;
use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerSearchResultsInterface;
use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerSearchResultsInterfaceFactory;
use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use CWSPS154\PromotionalBannerManager\Model\Config\Data;
use CWSPS154\PromotionalBannerManager\Model\ResourceModel\PromotionalBanner;
use CWSPS154\PromotionalBannerManager\Model\ResourceModel\PromotionalBanner\CollectionFactory;
use DateTime;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Util\Exception;

class PromotionalBannerRepository implements PromotionalBannerRepositoryInterface
{
    /**
     * @param PromotionalBannerInterfaceFactory $promotionalBannerInterfaceFactory
     * @param PromotionalBannerSearchResultsInterfaceFactory $searchResultsInterfaceFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param PromotionalBanner $resourceModel
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        private readonly PromotionalBannerInterfaceFactory                 $promotionalBannerInterfaceFactory,
        private readonly PromotionalBannerSearchResultsInterfaceFactory    $searchResultsInterfaceFactory,
        private readonly ResourceModel\PromotionalBanner\CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface                      $collectionProcessor,
        private readonly ResourceModel\PromotionalBanner                   $resourceModel,
        private ImageUploader                                              $imageUploader,
        private Data                                                       $configData
    )
    {
    }

    /**
     * @param PromotionalBannerInterface $promotionalBanner
     * @return PromotionalBannerInterface
     * @throws LocalizedException
     * @throws CouldNotSaveException
     */
    public function save(PromotionalBannerInterface $promotionalBanner)
    {
        if (!$this->configData->isEnable()) {
            throw new Exception(
                __('The Promotional Banner is disabled.')
            );
        }
        try {
            $startDate = $promotionalBanner->getStartDate();
            $endDate = $promotionalBanner->getEndDate();
            $priority = (int)$promotionalBanner->getPriority();
            $bannerId = (int)$promotionalBanner->getId();
            $this->validateUniquePrioritySchedule($priority, $startDate, $endDate, $bannerId);
            $this->resourceModel->save($promotionalBanner);
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('The banner data was unable to be saved. Please try again.' . $e->getMessage()),
                $e
            );
        }
        return $promotionalBanner;
    }

    /**
     * @param int $entityId
     * @return PromotionalBannerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId)
    {
        if (!$this->configData->isEnable()) {
            throw new Exception(
                __('The Promotional Banner is disabled.')
            );
        }
        $promotionalBanner = $this->promotionalBannerInterfaceFactory->create();
        $this->resourceModel->load($promotionalBanner, $entityId);
        if (!$promotionalBanner->getId()) {
            throw new NoSuchEntityException(
                __(
                    sprintf("The banner data with '%s' ID doesn't exist.", $entityId)
                )
            );
        }
        return $promotionalBanner;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return PromotionalBannerSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        if (!$this->configData->isEnable()) {
            throw new Exception(
                __('The Promotional Banner is disabled.')
            );
        }
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResult = $this->searchResultsInterfaceFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * @param PromotionalBannerInterface $promotionalBanner
     * @return bool
     * @throws LocalizedException
     * @throws CouldNotDeleteException
     */
    public function delete(PromotionalBannerInterface $promotionalBanner): bool
    {
        if (!$this->configData->isEnable()) {
            throw new Exception(
                __('The Promotional Banner is disabled.')
            );
        }
        try {
            $this->resourceModel->delete($promotionalBanner);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the banner data: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws LocalizedException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }

    /**
     * Validate that no other banner has the same priority and overlapping schedule
     *
     * @param int $priority
     * @param string $startDate
     * @param string $endDate
     * @param int|null $bannerId
     *
     * @throws LocalizedException
     */
    public function validateUniquePrioritySchedule(int $priority, $startDate, $endDate, $bannerId): void
    {
        if (!$this->isValidDate($startDate)) {
            throw new LocalizedException(
                __('Start Date is a required field. Please add a valid date')
            );
        }
        if (!$this->isValidDate($endDate)) {
            throw new LocalizedException(
                __('End Date is a required field. Please add a valid date')
            );
        }
        $isConflict = $this->resourceModel->checkForConflict($priority, $startDate, $endDate, $bannerId);

        if ($isConflict) {
            throw new LocalizedException(
                __('Another banner with the same priority exists during the specified period.')
            );
        }
    }

    /**
     * @param PromotionalBannerInterface $model
     * @param array $data
     * @return PromotionalBannerInterface
     * @throws LocalizedException
     */
    public function imageData(PromotionalBannerInterface $model, array $data): PromotionalBannerInterface
    {
        if (!isset($data['image'][0]['tmp_name'])) {
            $model->setImage($this->imageUploader->getBasePath() . $data['image'][0]['name']);
            return $model;
        }
        if ($model->getEntityId()) {
            if (isset($data['image'][0]['name'])) {
                $imageName1 = $model->getImage();
                $imageName2 = $data['image'][0]['name'];
                if ($imageName1 != $imageName2) {
                    $imageName = $data['image'][0]['name'];
                    $data['image'] = $this->imageUploader->moveFileFromTmp($imageName, true);
                } else {
                    $data['image'] = $data['image'][0]['name'];
                }
            } else {
                $data['image'] = '';
            }
        } else {
            if (isset($data['image'][0]['name'])) {
                $imageName = $data['image'][0]['name'];
                $data['image'] = $this->imageUploader->moveFileFromTmp($imageName, true);
            } else {
                $data['image'] = '';
            }
        }
        $model->setImage($data['image']);
        return $model;
    }

    /**
     * Validate if the string is a valid date
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $dateTime = DateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) === $date;
    }
}
