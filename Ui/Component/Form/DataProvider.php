<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Ui\Component\Form;

use CWSPS154\PromotionalBannerManager\Model\ResourceModel\PromotionalBanner\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    )
    {
        $this->collection = $collectionFactory->create();
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $attribute) {
            $this->loadedData[$attribute->getId()] = $attribute->getData();
            if (isset($this->loadedData[$attribute->getId()]['image'])) {
                $this->loadedData[$attribute->getId()]['image'] = [];
                $this->loadedData[$attribute->getId()]['image'][0]['name'] = basename($attribute->getImage());
                $this->loadedData[$attribute->getId()]['image'][0]['url'] = $this->getMediaUrl() . $attribute->getImage();
                $this->loadedData[$attribute->getId()]['image'][0]['size'] = $this->getFileSize( $attribute->getImage());
                $this->loadedData[$attribute->getId()]['image'][0]['type'] = $this->getMimeType( $attribute->getImage());
            }
        }
        return $this->loadedData;
    }

    /**
     * Get base media URL
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getMediaUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get file size of the image
     *
     * @param string $filePath
     * @return int
     */
    protected function getFileSize(string $filePath): int
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath = $mediaDirectory->getAbsolutePath($filePath);

        return file_exists($absolutePath) ? filesize($absolutePath) : 0;
    }

    /**
     * Get mime type of the image
     *
     * @param string $filePath
     * @return string
     */
    protected function getMimeType(string $filePath): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath = $mediaDirectory->getAbsolutePath($filePath);

        return file_exists($absolutePath) ? mime_content_type($absolutePath) : '';
    }
}
