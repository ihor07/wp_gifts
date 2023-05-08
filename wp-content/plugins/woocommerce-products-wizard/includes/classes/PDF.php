<?php
namespace WCProductsWizard;

/**
 * PDF Class
 *
 * @class PDF
 * @version 1.3.1
 */
class PDF
{
    /**
     * Current working wizard results PDF total pages
     * @var int
     */
    public $currentTotalPages = null;
    
    /** Class Constructor */
    public function __construct()
    {
        add_action('woocommerce_after_register_post_type', [$this, 'wcProductRegisteredAction'], 1);
    }
    
    /** Handles then woocommerce product is registered */
    public function wcProductRegisteredAction()
    {
        if (isset($_GET['wcpw-result-pdf']) && $_GET['wcpw-result-pdf']) {
            $id = (int) $_GET['wcpw-result-pdf'];

            self::outputCart([
                'id' => $id,
                'name' => Settings::getPost($id, 'pdf_file_name', 'post', str_replace(' ', '-', get_bloginfo('name')))
            ]);
        }
    }
    
    /**
     * Generate and return URL and path for cart content PDF
     *
     * @param array $args
     *
     * @return null|object
     */
    public function getCartInstance($args)
    {
        do_action('wcpw_before_get_cart_pdf', $args);

        if (!class_exists('\Dompdf\Dompdf')) {
            require_once(__DIR__ . '/../vendor/dompdf/autoload.inc.php');
        }

        $defaults = [
            'id' => null,
            'formData' => []
        ];

        $args = array_replace($defaults, $args);

        foreach (Cart::get($args['id']) as $item) {
            if (!isset($item['key'], $item['display_value'])) {
                continue;
            }

            $args['formData']["[{$item['key']}]"] = (string) $item['display_value'];
        }

        do_action('wcpw_before_output', $args);

        $options = new \Dompdf\Options();
        do_action('wcpw_dompdf_options', $options, $args);

        $dompdf = new \Dompdf\Dompdf($options);
        do_action('wcpw_dompdf_instance', $dompdf, $args);

        $dompdf->loadHtml(Template::html('result-pdf', $args, ['echo' => false]));
        $dompdf->render();

        $args['pageTotal'] = $this->currentTotalPages = (int) $dompdf->getCanvas()->get_page_count();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(Template::html('result-pdf', $args, ['echo' => false]));
        $dompdf->render();

        do_action('wcpw_after_output', $args);

        return apply_filters('wcpw_result_pdf_instance', $dompdf, $args);
    }

    /**
     * Save cart PDF to a file and return its URL and path
     *
     * @param array $args
     *
     * @return null|array
     */
    public function saveCart($args)
    {
        $defaults = ['name' => str_replace(' ', '-', get_bloginfo('name'))];
        $args = array_replace($defaults, $args);
        $dompdf = $this->getCartInstance($args);

        if (!$dompdf) {
            return null;
        }

        $name = apply_filters('wcpw_result_pdf_file_name', $args['name'], $args);
        $path = WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'pdf' . DIRECTORY_SEPARATOR . "$name.pdf";
        $url = WC_PRODUCTS_WIZARD_UPLOADS_URL . 'pdf' . DIRECTORY_SEPARATOR . "$name.pdf";

        // create uploads folder if not exists
        if (!file_exists(WC_PRODUCTS_WIZARD_UPLOADS_PATH)) {
            mkdir(WC_PRODUCTS_WIZARD_UPLOADS_PATH, 0777, true);
        }

        // create pdf folder if not exists
        if (!file_exists(WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'pdf')) {
            mkdir(WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'pdf', 0777, true);
        }

        file_put_contents($path, $dompdf->output());

        $output = [
            'url' => $url,
            'path' => $path,
            'pdf' => $dompdf
        ];

        return apply_filters('wcpw_result_pdf_file', $output, $args);
    }

    /**
     * Output cart PDF
     *
     * @param array $args
     */
    public function outputCart($args)
    {
        $defaults = ['name' => str_replace(' ', '-', get_bloginfo('name'))];
        $args = array_replace($defaults, $args);
        $dompdf = $this->getCartInstance($args);

        if (!$dompdf) {
            return;
        }

        $name = apply_filters('wcpw_result_pdf_file_name', $args['name'], $args);
        $dompdf->stream($name, ['Attachment' => false]);

        exit();
    }

    /**
     * Prepare HTML content for PDF using
     *
     * @param string $content
     * @param array $replacements - key => value to replace in content
     *
     * @return string
     */
    public static function prepareContent($content, $replacements = [])
    {
        if (!empty($replacements)) {
            $content = str_replace(array_keys($replacements), $replacements, $content);
        }

        return Utils::replaceImagesToBase64InHtml(apply_filters('the_content', $content));
    }
}
