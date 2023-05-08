<?php
namespace WCProductsWizard;

/**
 * Thumbnail Class
 *
 * @class Thumbnail
 * @version 1.1.0
 */
class Thumbnail
{
    /**
     * Generate thumbnail file and save it in uploads
     *
     * @param integer $wizardId
     * @param array $cart
     *
     * @return array
     */
    public static function generate($wizardId, $cart = [])
    {
        static $cache = [];

        if (isset($cache[$wizardId])) {
            return apply_filters('wcpw_generated_thumbnail', $cache[$wizardId], $wizardId, $cart);
        }

        $cart = !empty($cart) ? $cart : Cart::get($wizardId);
        $areas = Settings::getPost($wizardId, 'thumbnail_areas');
        $canvasWidth = Settings::getPost($wizardId, 'thumbnail_canvas_width');
        $canvasHeight = Settings::getPost($wizardId, 'thumbnail_canvas_height');
        $finalImage = imagecreatetruecolor($canvasWidth, $canvasHeight);
        $cartAreas = self::getCartAreas($wizardId, $cart);

        // Enable blend mode and save full alpha channel
        imagealphablending($finalImage, true);
        imagesavealpha($finalImage, true);
        imagefill($finalImage, 0, 0, 0x7fff0000);

        foreach ($areas as $key => $area) {
            if (isset($area['availability_rules'])
                && !Utils::getAvailabilityByRules($wizardId, $area['availability_rules'], "thumbnail-area-{$key}")
            ) {
                continue;
            }

            // get default area image
            $imagePath = isset($area['image']) ? get_attached_file($area['image']) : null;

            foreach ($cartAreas as $cartArea) {
                // find cart item area image
                if ($cartArea['name'] == $area['name']) {
                    $imagePath = isset($cartArea['image_path']) && $cartArea['image_path']
                        ? $cartArea['image_path']
                        : (isset($cartArea['image_id']) ? get_attached_file($cartArea['image_id']) : null);
                }
            }

            if (!$imagePath) {
                continue;
            }

            $image = null;
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $size = getimagesize($imagePath);

            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;

                case 'png':
                    $image = imagecreatefrompng($imagePath);
                    break;

                case 'gif':
                    $image = imagecreatefromgif($imagePath);
            }

            if (!$image) {
                continue;
            }

            imagecopyresized(
                $finalImage,
                $image,
                $area['x'],
                $area['y'],
                0,
                0,
                $area['width'],
                $area['height'],
                $size[0],
                $size[1]
            );
        }

        $filename = md5(serialize(['id' => (int) $wizardId] + $cartAreas)) . '.png';
        $folderUrl = WC_PRODUCTS_WIZARD_UPLOADS_URL . 'thumbnails' . DIRECTORY_SEPARATOR;
        $folderPath = WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'thumbnails' . DIRECTORY_SEPARATOR;

        // create uploads folder if not exists
        if (!file_exists(WC_PRODUCTS_WIZARD_UPLOADS_PATH)) {
            mkdir(WC_PRODUCTS_WIZARD_UPLOADS_PATH, 0777, true);
        }

        // create thumbnails folder if not exists
        if (!file_exists(WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'thumbnails')) {
            mkdir(WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'thumbnails', 0777, true);
        }

        imagepng($finalImage, $folderPath . $filename);
        imagedestroy($finalImage);

        $output = [
            'url' => $folderUrl . $filename,
            'path' => $folderPath . $filename
        ];

        $cache[$wizardId] = $output;

        return apply_filters('wcpw_generated_thumbnail_data', $output, $wizardId, $cart);
    }

    /**
     * Collect the areas data of the cart items
     *
     * @param integer $wizardId
     * @param array $cart
     *
     * @return array
     */
    public static function getCartAreas($wizardId, $cart = [])
    {
        $output = [];

        foreach ($cart as $cartItem) {
            if (isset($cartItem['name'], $cartItem['url'], $cartItem['is_image']) && $cartItem['is_image']) {
                $output[] = [
                    'name' => $cartItem['key'],
                    'image_path' => $cartItem['path']
                ];

                continue;
            } elseif (!isset($cartItem['product_id']) || !$cartItem['product_id']) {
                continue;
            }

            $areas = [];

            // category level
            foreach (Product::getTermsIds($cartItem['product_id']) as $categoryId) {
                foreach (Settings::getProductCategory($categoryId, 'thumbnail_areas') as $key => $area) {
                    if (isset($area['name'], $area['image'])
                        && Utils::getAvailabilityByRules(
                            $wizardId,
                            [$area],
                            "cart-product-category-{$categoryId}-thumbnail-area-{$key}"
                        )
                    ) {
                        $areas[] = $area;
                    }
                }
            }

            // product level
            foreach (Settings::getProduct($cartItem['product_id'], 'thumbnail_areas') as $key => $area) {
                if (isset($area['name'], $area['image'])
                    && Utils::getAvailabilityByRules(
                        $wizardId,
                        [$area],
                        "cart-product-{$cartItem['product_id']}-thumbnail-area-{$key}"
                    )
                ) {
                    $areas[] = $area;
                }
            }

            if (isset($cartItem['variation_id']) && $cartItem['variation_id']) {
                // variation attributes level
                foreach ($cartItem['variation'] as $attribute => $value) {
                    if (strpos($attribute, 'attribute_pa_') === false) {
                        continue;
                    }

                    $term = get_term_by('name', $value, str_replace('attribute_', '', $attribute));

                    if (!$term) {
                        continue;
                    }

                    foreach (Settings::getProductAttribute($term->term_id, 'thumbnail_areas') as $key => $area) {
                        if (isset($area['name'], $area['image'])
                            && Utils::getAvailabilityByRules(
                                $wizardId,
                                [$area],
                                "cart-product-attribute-{$term->term_id}-thumbnail-area-{$key}"
                            )
                        ) {
                            $areas[] = $area;
                        }
                    }
                }

                // variation level
                foreach (Settings::getProductVariation($cartItem['variation_id'], 'thumbnail_areas') as $key => $area) {
                    if (isset($area['name'], $area['image'])
                        && Utils::getAvailabilityByRules(
                            $wizardId,
                            [$area],
                            "cart-product-variation-{$cartItem['variation_id']}-thumbnail-area-{$key}"
                        )
                    ) {
                        $areas[] = $area;
                    }
                }
            }

            if (empty($areas)) {
                continue;
            }

            foreach ($areas as $area) {
                if (!$area['name'] || !$area['image']) {
                    continue;
                }

                $output[] = [
                    'name' => $area['name'],
                    'image_id' => $area['image']
                ];
            }
        }

        return apply_filters('wcpw_generated_thumbnail_cart_areas', $output, $wizardId, $cart);
    }
}
