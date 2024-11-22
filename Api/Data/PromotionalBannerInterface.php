<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Api\Data;

interface PromotionalBannerInterface
{
    public const ENTITY_ID = 'entity_id';
    public const TITLE = 'title';
    public const DESCRIPTION = 'description';
    public const IMAGE = 'image';
    public const PRIORITY = 'priority';
    public const START_DATE = 'start_date';
    public const END_DATE = 'end_date';
    public const STATUS = 'status';

    public const REQUIRED_FIELD = [
        self::TITLE,
        self::PRIORITY
    ];

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * @param $id
     * @return PromotionalBannerInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string $title
     * @return PromotionalBannerInterface
     */
    public function setTitle(string $title): PromotionalBannerInterface;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     * @return PromotionalBannerInterface
     */
    public function setDescription(string $description): PromotionalBannerInterface;

    /**
     * @return string|array|null
     */
    public function getImage(): string|array|null;

    /**
     * @param array|string $image
     * @return PromotionalBannerInterface
     */
    public function setImage(array|string $image): PromotionalBannerInterface;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param int $priority
     * @return PromotionalBannerInterface
     */
    public function setPriority(int $priority): PromotionalBannerInterface;

    /**
     * @return string
     */
    public function getStartDate(): string;

    /**
     * @param string $startDate
     * @return PromotionalBannerInterface
     */
    public function setStartDate(string $startDate): PromotionalBannerInterface;

    /**
     * @return string
     */
    public function getEndDate(): string;

    /**
     * @param string $endDate
     * @return PromotionalBannerInterface
     */
    public function setEndDate(string $endDate): PromotionalBannerInterface;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     * @return PromotionalBannerInterface
     */
    public function setStatus(int $status): PromotionalBannerInterface;
}
