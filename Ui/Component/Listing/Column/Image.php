<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Ui\Component\Listing\Column;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Image extends Column
{
    public const NAME = 'image';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface                       $context,
        UiComponentFactory                     $uiComponentFactory,
        private readonly StoreManagerInterface $storeManager,
        array                                  $components = [],
        array                                  $data = [],
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName . '_src'] = $this->getMediaUrl($item[self::NAME]);
                $item[$fieldName . '_alt'] = $item['title'];
                $item[$fieldName . '_link'] = $this->context->getUrl(
                    Action::PROMOTIONAL_BANNER_EDIT_PATH,
                    ['entity_id' => $item['entity_id']]
                );
                $item[$fieldName . '_orig_src'] = $this->getMediaUrl($item[self::NAME]);
            }
        }

        return $dataSource;
    }

    /**
     * Get the media url
     *
     * @param string|null $path
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl(?string $path): string
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore();
        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
    }
}
