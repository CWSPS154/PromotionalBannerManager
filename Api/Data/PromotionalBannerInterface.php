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
     * Set ID
     *
     * @param mixed $id
     * @return PromotionalBannerInterface
     */
    public function setId(mixed $id);

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set Title
     *
     * @param string $title
     * @return PromotionalBannerInterface
     */
    public function setTitle(string $title): PromotionalBannerInterface;

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Set Description
     *
     * @param string $description
     * @return PromotionalBannerInterface
     */
    public function setDescription(string $description): PromotionalBannerInterface;

    /**
     * Get Image
     *
     * @return string|array|null
     */
    public function getImage(): string|array|null;

    /**
     * Set Image
     *
     * @param array|string $image
     * @return PromotionalBannerInterface
     */
    public function setImage(array|string $image): PromotionalBannerInterface;

    /**
     * Get Priority
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Set Priority
     *
     * @param int $priority
     * @return PromotionalBannerInterface
     */
    public function setPriority(int $priority): PromotionalBannerInterface;

    /**
     * Get Start Date
     *
     * @return string
     */
    public function getStartDate(): string;

    /**
     * Set Start Date
     *
     * @param string $startDate
     * @return PromotionalBannerInterface
     */
    public function setStartDate(string $startDate): PromotionalBannerInterface;

    /**
     * Get End Date
     *
     * @return string
     */
    public function getEndDate(): string;

    /**
     * Set End Date
     *
     * @param string $endDate
     * @return PromotionalBannerInterface
     */
    public function setEndDate(string $endDate): PromotionalBannerInterface;

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus(): int;

    /**
     * Set Status
     *
     * @param int $status
     * @return PromotionalBannerInterface
     */
    public function setStatus(int $status): PromotionalBannerInterface;
}
