<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\PromotionalBannerManager\Model\Resolver;

use CWSPS154\PromotionalBannerManager\Api\PromotionalBannerRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class DeletePromotionalBanner implements ResolverInterface
{
    /**
     * @param PromotionalBannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        private PromotionalBannerRepositoryInterface $bannerRepository,
    ) {
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|Value
     * @throws \Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['entity_id'])) {
            throw new GraphQlInputException(__('Banner with ID "%1" does not exist.', $args['entity_id']));
        }

        try {
            $banner = $this->bannerRepository->deleteById($args['id']);
        } catch (NoSuchEntityException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()), $e);
        }
        return $banner;
    }
}
