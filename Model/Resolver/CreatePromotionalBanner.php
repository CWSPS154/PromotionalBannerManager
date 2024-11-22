<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Model\Resolver;

use CWSPS154\PromotionalBannerManager\Api\Data\PromotionalBannerInterfaceFactory;
use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CreatePromotionalBanner implements ResolverInterface
{
    public function __construct(
        private readonly PromotionalBannerInterfaceFactory    $bannerInterfaceFactory,
        private readonly PromotionalBannerRepositoryInterface $bannerRepository,
    )
    {
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|Value
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            $input = $args['input'];

            $banner = $this->bannerInterfaceFactory->create();
            if (!empty($data['entity_id'])) {
                $banner->setId($data['entity_id']);
            }
            $banner->setTitle($input['title']);
            $banner->setDescription($input['description']);
            $banner->setImage($input['image']);
            $banner->setPriority($input['priority']);
            $banner->setStartDate($input['start_date']);
            $banner->setEndDate($input['end_date']);
            $banner->setStatus($input['status']);

            return $this->bannerRepository->save($banner);
        } catch (CouldNotSaveException $e) {
            return $e;
        }
    }
}
