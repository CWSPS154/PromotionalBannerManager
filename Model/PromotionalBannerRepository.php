<?php
declare(strict_types=1);

namespace CWSPS154\PromotionalBannerManager\Model;

use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterface;
use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterfaceFactory;
use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class PromotionalBannerRepository implements PromotionalBannerRepositoryInterface
{
    /**
     * @param PromotionalBannerInterfaceFactory $promotionalBannerInterfaceFactory
     * @param SearchResultsInterfaceFactory $searchResultsInterfaceFactory
     * @param ResourceModel\PromotionalBanner\CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceModel\PromotionalBanner $resourceModel
     */
    public function __construct(
        private readonly PromotionalBannerInterfaceFactory                 $promotionalBannerInterfaceFactory,
        private readonly SearchResultsInterfaceFactory                     $searchResultsInterfaceFactory,
        private readonly ResourceModel\PromotionalBanner\CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface                      $collectionProcessor,
        private readonly ResourceModel\PromotionalBanner                   $resourceModel
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
        try {
            $startDate = $promotionalBanner->getStartDate();
            $endDate = $promotionalBanner->getEndDate();
            $priority = $promotionalBanner->getPriority();
            $bannerId = $promotionalBanner->getId();
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
     * @return SearchResultsInterfaceFactory
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
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
    public function validateUniquePrioritySchedule(int $priority, string $startDate, string $endDate, int|null $bannerId): void
    {
        $isConflict = $this->resourceModel->checkForConflict($priority, $startDate, $endDate, $bannerId);

        if ($isConflict) {
            throw new LocalizedException(
                __('Another banner with the same priority exists during the specified period.')
            );
        }
    }
}
