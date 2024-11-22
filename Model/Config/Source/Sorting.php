<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Model\Config\Source;

class Sorting implements \Magento\Framework\Option\ArrayInterface
{
    public const DESC = 0;
    public const ASCE = 1;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ASCE, 'label' => __('Ascending By Priority')],
            ['value' => self::DESC, 'label' => __('Descending By Priority')]
        ];
    }
}
