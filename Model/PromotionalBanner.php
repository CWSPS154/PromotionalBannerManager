<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Model;

use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterface;
use CWSPS154\PromotionalBannerManager\Model\ResourceModel\PromotionalBanner as ResourceModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class PromotionalBanner extends AbstractModel implements PromotionalBannerInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'promotional_banner_model';

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return PromotionalBannerInterface
     */
    public function setTitle(string $title): PromotionalBannerInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set Description
     *
     * @param string $description
     * @return PromotionalBannerInterface
     */
    public function setDescription(string $description): PromotionalBannerInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get Image
     *
     * @return string|array|null
     */
    public function getImage(): string|array|null
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Set Image
     *
     * @param string|array $image
     * @return PromotionalBannerInterface
     * @throws LocalizedException
     */
    public function setImage(string|array $image): PromotionalBannerInterface
    {
        /** @var ImagePreProcessor $imagePreProcessor */
        $imagePreProcessor = $this->_data['imagePreProcessor'] ?? null;
        if (is_array($image) && $imagePreProcessor) {
            $image = $imagePreProcessor->uploadImage($image);
            return $this->setData(self::IMAGE, $image);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
        } elseif (base64_decode($image) !== false) {
            $imgData = str_replace(' ', '+', $image);
            $imgData = substr($imgData, strpos($imgData, ",") + 1);
            $image = $imagePreProcessor->saveBase64Image($this, $imgData, self::TITLE);
            return $this->setData(self::IMAGE, $image);
        }
        return $this;
    }

    /**
     * Get Priority
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * Set Priority
     *
     * @param int $priority
     * @return PromotionalBannerInterface
     */
    public function setPriority(int $priority): PromotionalBannerInterface
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Get Start Date
     *
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->getData(self::START_DATE);
    }

    /**
     * Set Start Date
     *
     * @param string $startDate
     * @return PromotionalBannerInterface
     */
    public function setStartDate(string $startDate): PromotionalBannerInterface
    {
        return $this->setData(self::START_DATE, $startDate);
    }

    /**
     * Get End Date
     *
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->getData(self::END_DATE);
    }

    /**
     * Set End Date
     *
     * @param string $endDate
     * @return PromotionalBannerInterface
     */
    public function setEndDate(string $endDate): PromotionalBannerInterface
    {
        return $this->setData(self::END_DATE, $endDate);
    }

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return PromotionalBannerInterface
     */
    public function setStatus(int $status): PromotionalBannerInterface
    {
        return $this->setData(self::STATUS, $status);
    }
}
