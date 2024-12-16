<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PromotionalBanner extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'promotional_banner_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('promotional_banner', 'entity_id');
        $this->_useIsObjectNew = true;
    }

    /**
     * Check if there is any banner with the same priority and overlapping schedule
     *
     * @param int $priority
     * @param string $startDate
     * @param string $endDate
     * @param int|null $bannerId
     * @return bool
     * @throws LocalizedException
     */
    public function checkForConflict(int $priority, string $startDate, string $endDate, ?int $bannerId = null): bool
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), ['entity_id'])
            ->where('priority = ?', $priority)
            ->where('start_date <= ?', date('Y-m-d', strtotime($endDate)))
            ->where('end_date >= ?', date('Y-m-d', strtotime($startDate)));

        if ($bannerId) {
            $select->where('entity_id != ?', $bannerId);
        }
        $result = $connection->fetchOne($select);

        return (bool)$result;
    }
}
