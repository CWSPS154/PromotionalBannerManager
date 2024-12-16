<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\PromotionalBannerManager\Model;

use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContent;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;

class ImagePreProcessor
{
    /**
     * @param ImageUploader $imageUploader
     * @param Filesystem $filesystem
     * @param ImageProcessorInterface $imageProcessor
     * @param ImageContentInterfaceFactory $imageContentFactory
     */
    public function __construct(
        private ImageUploader                $imageUploader,
        private Filesystem                   $filesystem,
        private ImageProcessorInterface      $imageProcessor,
        private ImageContentInterfaceFactory $imageContentFactory,
    ) {
    }

    /**
     * Save Base64 Image
     *
     * @param AbstractModel $model
     * @param string $imgData
     * @param string $nameField
     * @return string
     * @throws InputException
     * @throws LocalizedException
     */
    public function saveBase64Image(AbstractModel $model, string $imgData, string $nameField): string
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $decodedImage = base64_decode($imgData);
        $fileName = preg_replace("/[^A-Za-z0-9]/", '', str_replace(
            ' ',
            '-',
            strtolower($model->getData($nameField))
        )) . '_' . uniqid() . '.jpg';
        $imageProperties = getimagesizefromstring($decodedImage);
        if (!$imageProperties) {
            throw new LocalizedException(__('Unable to get properties from image.'));
        }

        /* @var ImageContent $imageContent */
        $imageContent = $this->imageContentFactory->create();
        $imageContent->setBase64EncodedData($imgData);
        $imageContent->setName($fileName);
        $imageContent->setType($imageProperties['mime']);
        $file = $this->imageProcessor->processImageContent(
            $this->imageUploader->getBasePath(),
            $imageContent
        );
        return $this->imageUploader->getBasePath() . $file;
    }

    /**
     * Upload Image
     *
     * @param array $data
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function uploadImage(array $data): string
    {
        if (!isset($data[0]['tmp_name'])) {
            return $this->handleMediaGalleryImage($data);
        }
        return $this->processUploadedImage($data);
    }

    /**
     * Handle existing image from the media gallery.
     *
     * @param array $data
     * @return string
     * @throws FileSystemException
     */
    private function handleMediaGalleryImage(array $data): string
    {
        if (!$this->fileResidesOutsideCurrentImageDir($data)) {
            $uniqueImageName = $this->checkUniqueImageName($data[0]['name']);
            return $this->imageUploader->getBasePath() . DIRECTORY_SEPARATOR . $uniqueImageName;
        }
        return $this->sanitizeMediaPath($data[0]['url']);
    }

    /**
     * Process uploaded image by moving it to the correct location and ensuring unique naming.
     *
     * @param array $imageData
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function processUploadedImage(array $imageData): string
    {
        if (isset($imageData[0]['name'])) {
//            $uniqueImageName = $this->checkUniqueImageName($imageData[0]['name']);
            return $this->imageUploader->moveFileFromTmp($imageData[0]['name'], true);
        }

        return '';
    }

    /**
     * Sanitize the media path from a given URL.
     *
     * @param string $url
     * @return string
     */
    private function sanitizeMediaPath(string $url): string
    {
        $mediaPath = DIRECTORY_SEPARATOR . DirectoryList::MEDIA;
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $urlPath = parse_url($url, PHP_URL_PATH);
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return str_replace($mediaPath, '', $urlPath);
    }

    /**
     * Ensure the image name is unique within the media directory.
     *
     * @param string $imageName
     * @return string
     * @throws FileSystemException
     */
    private function checkUniqueImageName(string $imageName): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $imageAbsolutePath = $mediaDirectory->getAbsolutePath(
            $this->imageUploader->getBasePath() . DIRECTORY_SEPARATOR . $imageName
        );

        return call_user_func([Uploader::class, 'getNewFilename'], $imageAbsolutePath);
    }

    /**
     * Check file it resides outside current image dir
     *
     * @param array|null $value
     * @return bool
     */
    private function fileResidesOutsideCurrentImageDir(?array $value): bool
    {
        if (!is_array($value) || !isset($value[0]['url'])) {
            return false;
        }

        $fileUrl = ltrim($value[0]['url'], '/');
        $baseMediaDir = $this->filesystem->getUri(DirectoryList::MEDIA);

        if (!$baseMediaDir) {
            return false;
        }

        return str_contains($fileUrl, $baseMediaDir);
    }
}
