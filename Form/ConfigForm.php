<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\FloatType;
use ScoutUnitsList\Form\Field\IntegerType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Validator\ConfigValidator;

/**
 * Configuration form
 */
class ConfigForm extends Form
{
    /**
     * Set fields
     *
     * @param array $settings settings
     */
    protected function setFields(array $settings)
    {
        unset($settings);

        $this
            ->addField('cacheTtl', IntegerType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Cache TTL in seconds', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('orderCategoryIds', SelectType::class, [
                'attr' => [
                    'multiple' => true,
                    'style' => 'width:25em',
                ],
                'label' => __('Order categories', 'scout-units-list'),
                'options' => $this->getOrderCategories(),
            ])
            ->addField('orderNoFormat', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Order number format for "pattern" attribute', 'scout-units-list'),
            ])
            ->addField('orderNoPlaceholder', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Order number placeholder', 'scout-units-list'),
            ])
            ->addField('shortcodeTemplatesPath', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                    'data-path-type' => json_encode([
                        0 => __('"scout-units-list" dir in active theme dir (default)', 'scout-units-list'),
                        1 => __('Specified path (from plugin\'s dir):', 'scout-units-list'),
                    ]),
                ],
                'label' => __('Shortcode templates path', 'scout-units-list'),
            ])
            ->addField('mapKey', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Map key', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('mapDefaultLat', FloatType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Map default latitude', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('mapDefaultLng', FloatType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Map default longitude', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('mapDefaultZoom', FloatType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Map default zoom', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('userImageSource', SelectType::class, [
                'attr' => [
                    'style' => 'width:25em',
                ],
                'label' => __('User image source', 'scout-units-list'),
                'options' => [
                    Config::IMAGE_SOURCE_AVATAR => __('Wordpress avatar', 'scout-units-list'),
                    Config::IMAGE_SOURCE_PHOTO => __('custom photo', 'scout-units-list'),
                ],
                'required' => true,
            ])
            ->addField('userPhotoSize', SelectType::class, [
                'attr' => [
                    'style' => 'width:25em',
                ],
                'label' => __('User photo size', 'scout-units-list'),
                'options' => $this->getUserPhotoSizes(),
                'required' => true,
            ])
            ->addField('externalStructureUrl', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                    'pattern' => '^https?://.+',
                ],
                'label' => __('External structure URL', 'scout-units-list'),
            ])
            ->addField('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button button-primary',
                ],
                'label' => __('Save', 'scout-units-list'),
            ])
        ;
    }

    /**
     * Get order categories
     *
     * @return array
     */
    private function getOrderCategories()
    {
        $orderCategories = [];

        $categories = get_categories([
            'taxonomy' => null,
        ]);
        foreach ($categories as $category) {
            $orderCategories[$category->cat_ID] = $category->cat_name . ' (' . $category->taxonomy . ')';
        }

        return $orderCategories;
    }

    /**
     * Get user photo sizes
     *
     * @return array
     */
    private function getUserPhotoSizes()
    {
        $userPhotoSizes = [];
        foreach (\get_intermediate_image_sizes() as $size) {
            $userPhotoSizes[$size] = $size;
        }

        return $userPhotoSizes;
    }

    /**
     * Get validator class
     *
     * @return string
     */
    protected function getValidatorClass()
    {
        return ConfigValidator::class;
    }
}
