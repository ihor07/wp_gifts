/* WooCommerce Products Wizard global instance and main event handlers
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.ru
 */

import { WCPW } from 'wcpw';
import { WCPWVariationForm } from 'wcpw-variation-form';
import { Masonry } from 'masonry-layout';
import { jQuery  as $} from 'jquery';

const wcpw = {
    windowNode: window,
    documentNode: document,
    $window: typeof $ !== 'undefined' ? $(window) : null,
    $document: typeof $ !== 'undefined' ? $(document) : null,
    $body: typeof $ !== 'undefined' ? $(document.body) : null,
    stickyObserverElements: null,

    init: function () {
        if (typeof WCPW === 'undefined') {
            this.windowNode.console.error('WCPW class is not exist');

            return this;
        }

        for (const element of this.documentNode.querySelectorAll('[data-component~="wcpw"]')) {
            element.wcpw = new WCPW(element, JSON.parse(element.getAttribute('data-options')) || {});
            element.wcpw.init();
        }

        return this;
    },

    initVariationForm: function (elements) {
        if (typeof WCPWVariationForm === 'undefined') {
            this.windowNode.console.error('WCPWVariationForm class is not exist');

            return this;
        }

        elements = elements || this.documentNode.querySelectorAll('[data-component~="wcpw-product-variations"]');

        for (const element of elements) {
            element.wcpwVariationForm = new WCPWVariationForm(element, JSON.parse(element.getAttribute('data-options')) || {});
            element.wcpwVariationForm.init();
        }

        return this;
    },

    onInit: function (event) {
        const instance = event.detail.instance;

        // init variation forms
        this.initVariationForm(instance.element.querySelectorAll('[data-component~="wcpw-product-variations"]'));

        // check product variations form for attached wizard
        if (this.$document && instance.options && instance.options.attachedProduct) {
            this.$document.find('#' + instance.options.formId).trigger('check_variations');
        }

        // prettyPhoto init
        if (typeof $ !== 'undefined' && typeof $.fn.prettyPhoto !== 'undefined') {
            $(instance.element)
                .find('a[data-rel^="prettyPhoto"]')
                .prettyPhoto({
                    hook: 'data-rel',
                    social_tools: false,
                    theme: 'pp_woocommerce',
                    horizontal_padding: 20,
                    opacity: 0.8,
                    deeplinking: false
                });
        }

        // avada lightbox init
        if (typeof this.windowNode.avadaLightBox !== 'undefined'
            && typeof this.windowNode.avadaLightBox.activate_lightbox !== 'undefined'
        ) {
            this.windowNode.avadaLightBox.activate_lightbox($(instance.element));
        }

        // sticky elements init
        if (typeof $ !== 'undefined' && typeof $.fn.stick_in_parent !== 'undefined') {
            for (let element of instance.element.querySelectorAll('[data-component~="wcpw-sticky"]')) {
                $(element).stick_in_parent(Object.assign(
                    // older versions support
                    {
                        parent: element.getAttribute('data-sticky-parent'),
                        offset_top: Number(element.getAttribute('data-sticky-top-offset'))
                    },
                    JSON.parse(element.getAttribute('data-sticky-options') || '{}')
                ));
            }
        }

        // EPO plugin init
        this.reInitExtraProductOptions(instance);

        // noUi slider init
        this.documentNode.dispatchEvent(new CustomEvent('init.nouislider.wcpw'));

        // init step filter
        this.documentNode.dispatchEvent(new CustomEvent('init.wcsf'));

        // masonry layout init
        if (typeof Masonry !== 'undefined') {
            for (const element of instance.element.querySelectorAll('[data-component~="wcpw-masonry"]')) {
                element.masonryInstance = new Masonry(
                    element,
                    Object.assign(
                        {
                            itemSelector: '.col',
                            percentPosition: true,
                            columnWidth: element.querySelector('[data-component="wcpw-masonry-sizer"]')
                        },
                        JSON.parse(element.getAttribute('data-options') || '{}')
                    )
                );
            }
        }
    },

    reInitExtraProductOptions: function (instance) {
        if (typeof $ === 'undefined' || typeof $.tcepo === 'undefined' || typeof $.tcepo.tm_init_epo === 'undefined') {
            return;
        }

        const _this = this;

        // clear fields cache
        if (typeof $.tc_api_set !== 'undefined') {
            $.tc_api_set('get_element_from_field_cache', []);
        } else if (typeof $.tcAPISet !== 'undefined') {
            $.tcAPISet('getElementFromFieldCache', []);
        }

        // remove old listeners
        this.$document.off('change.cpfurl tmredirect', '.use_url_container .tmcp-select');
        this.$document.off(
            'click.cpfurl change.cpfurl tmredirect',
            '.use_url_container .tmcp-radio, .use_url_container .tmcp-radio+label'
        );

        // remove old elements
        instance.element.querySelectorAll('.tmcp-upload-hidden').remove();

        // unique container where the options are embedded. this is usually the parent tag of the cart form
        for (let product of instance.element.querySelectorAll('[data-component~="wcpw-product"]')) {
            const $product = $(product);
            const options = product.querySelector('.tc-extra-product-options');
            const productId = options.getAttribute('data-product-id');
            const epoId = options.getAttribute('data-epo-id');

            if (!options || !productId || !epoId) {
                continue;
            }

            $.tcepo.tm_init_epo($product, true, productId, epoId);

            _this.$window.trigger('tmlazy');

            if ($.jMaskGlobals) {
                $product.find($.jMaskGlobals.maskElements).each(function () {
                    const $element = $(this);

                    if ($element.attr('data-mask')) {
                        $element.mask($element.attr('data-mask'));
                    }
                });
            }

            if (product.getAttribute('data-type') === 'variable') {
                $product.find('[data-component~="wcpw-product-variations"]').trigger('wc_variation_form.cpf');
            }
        }
    },

    saveExtraProductOptions: function (productToAdd) {
        if (typeof $ === 'undefined' || typeof $.tcepo === 'undefined') {
            return null;
        }

        const productId = productToAdd.product_id;
        const $extraOptions = this.$document.find(`.tc-extra-product-options.tm-product-id-${productId}`);

        if ($extraOptions.length !== 1) {
            return true;
        }

        const $totalsForm = this.$document.find(`.tc-totals-form.tm-product-id-${productId}`);
        const $form = $totalsForm.closest('form');
        const formPrefix = $totalsForm.find('.tc_form_prefix').val();
        const data = {
            tcajax: 1,
            tcaddtocart: productId,
            cpf_product_price: $totalsForm.find('.cpf-product-price').val()
        };

        if (formPrefix) {
            data.tc_form_prefix = formPrefix;
        }

        if ($totalsForm.tc_validate && !$form.tc_validate().form()) {
            return false;
        }

        if ($form.data('wcpw-epo-data') && $form.data('wcpw-epo-data').functions
            && !$form.data('wcpw-epo-data').functions.apply_submit_events($form.data('wcpw-epo-data').epo)
        ) {
            return false;
        }

        // save collected data into product request arg
        const request = $extraOptions.tm_aserializeObject
            ? $extraOptions.tm_aserializeObject()
            : $extraOptions.tcSerializeObject();

        // bug with files upload
        for (let key of request) {
            let value = request[key];

            if (Array.isArray(value)) {
                value = value.filter((el) => el !== '');

                if (value.length === 0) {
                    request[key] = '';
                }
            }
        }

        productToAdd.request = Object.assign(request, data);

        return true;
    },

    saveExtraProductOptionsAttachments: function (instance, data) {
        if (typeof $.tcepo === 'undefined') {
            return;
        }

        for (let element of instance.element.querySelectorAll('.tc-extra-product-options input[type="file"]')) {
            if (element.files[0] && element.files[0].size) {
                data.append(element.name, element.files[0]);
            }
        }
    },

    reInitCheckoutScript: function () {
        const script = this.documentNode.querySelector('#wc-checkout-js');

        if (!script) {
            return this;
        }

        const src = script.getAttribute('src');
        const id = script.getAttribute('id');
        const clone = this.documentNode.createElement('script');

        // clear all traces
        script.remove();

        if (this.$body) {
            this.$body.off('click', 'a.showcoupon');
            this.$body.off('click', '.woocommerce-remove-coupon');
            this.$body.off('click', 'a.showlogin');
            this.$body.off('click', 'a.woocommerce-terms-and-conditions-link');
        }

        clone.src = src;
        clone.id = id;

        // append and init script
        this.documentNode.body.appendChild(clone);

        // init county select
        if (this.$body) {
            this.$body.trigger('country_to_state_changed');
        }

        return this;
    },

    updateStickyObserverElementsState: function () {
        if (!this.stickyObserverElements || this.stickyObserverElements.length <= 0) {
            return;
        }

        for (const element of this.stickyObserverElements) {
            if (!element.parentElement) {
                continue;
            }

            const parentTop = element.parentElement.getBoundingClientRect().top;
            const top = Math.round(element.getBoundingClientRect().top - parentTop);

            element.style.position = 'static';
            element.toggleAttribute(
                'stuck',
                top !== Math.round(element.getBoundingClientRect().top - parentTop)
            );

            element.style.position = '';
            element.dataset.prevClientTop = String(top);
        }
    }
};

// main actions
wcpw.windowNode.addEventListener('load', () => {
    // init sticky observer elements
    wcpw.stickyObserverElements = wcpw.documentNode.querySelectorAll('[data-component~="wcpw-sticky-observer"]');

    if (!wcpw.stickyObserverElements || wcpw.stickyObserverElements.length <= 0) {
        return;
    }

    wcpw.updateStickyObserverElementsState();
    wcpw.windowNode.addEventListener('resize', () => setTimeout(() => wcpw.updateStickyObserverElementsState(), 0));
    wcpw.windowNode.addEventListener('scroll', () => {
        for (const element of wcpw.stickyObserverElements) {
            if (!element.parentElement) {
                continue;
            }

            const top = Math
                .round(element.getBoundingClientRect().top - element.parentElement.getBoundingClientRect().top);

            element.toggleAttribute('stuck', Number(element.dataset.prevClientTop) !== top);
            element.dataset.prevClientTop = String(top);
        }
    }, false);
});

wcpw.documentNode.addEventListener('DOMContentLoaded', () => {
    wcpw.init();

    // contact form 7 hooks
    if (typeof wpcf7 !== 'undefined') {
        wcpw.documentNode.addEventListener('ajaxCompleted.wcpw', (event) => {
            const form = event.detail.instance.element.querySelector('.wpcf7-form');

            if (form) {
                if (typeof wpcf7.initForm !== 'undefined') {
                    wpcf7.initForm($(form));
                } else if (typeof wpcf7.init !== 'undefined') {
                    wpcf7.init(form);
                }
            }
        });
    }

    // EPO hooks
    if (typeof $ !== 'undefined' && typeof $.tcepo !== 'undefined') {
        wcpw.documentNode.addEventListener('submit.wcpw', (event) => {
            const instance = event.detail.instance;
            const data = event.detail.data;

            if (instance && data) {
                // pass data to the request
                for (let key of data.productsToAdd) {
                    let product = data.productsToAdd[key];

                    if (typeof data.productsToAddChecked[product.step_id] !== 'undefined'
                        && data.productsToAddChecked[product.step_id].indexOf(product.product_id) !== -1
                        && !wcpw.saveExtraProductOptions(product)
                    ) {
                        instance.hasError = true;
                        instance.productsWithError.push(product);
                    }
                }
            }
        });

        wcpw.documentNode.addEventListener('ajaxRequest.wcpw', (event) => {
            // save attachments
            wcpw.saveExtraProductOptionsAttachments(event.detail.instance, event.detail.data);
        });
    }
});

wcpw.documentNode.addEventListener('init.wcpw', () => wcpw.init());

wcpw.documentNode.addEventListener('launched.wcpw', (event) => {
    const instance = event.detail.instance;

    wcpw.onInit(event);

    if (typeof $ !== 'undefined') {
        // off default WC form scripts
        setTimeout(() => {
            $(instance.element).find('[data-component~="wcpw-product-variations"]').off('.wc-variation-form');
        }, 100);
    }
});

// ajax actions
wcpw.documentNode.addEventListener('ajaxCompleted.wcpw', (event) => {
    const instance = event.detail.instance;
    const response = event.detail.response;

    wcpw.onInit(event);

    // re-init sticky observer elements
    wcpw.stickyObserverElements = instance.element.querySelectorAll('[data-component~="wcpw-sticky-observer"]');
    wcpw.updateStickyObserverElementsState();

    // refresh WC mini-cart if wizard reflects the cart
    if (wcpw.$body && instance.options && instance.options.reflectInMainCart) {
        wcpw.$body.trigger('wc_fragment_refresh');
    }

    if (instance.options && instance.options.enableCheckoutStep
        && response.finalRedirectUrl && response.preventRedirect
    ) {
        // re-init checkout script
        wcpw.reInitCheckoutScript();
    }

    if (response.hasError) {
        const message = instance.element.querySelector('[data-component~="wcpw-message"]');

        // scroll to the message
        if (message && !instance.isScrolledIntoView(message)) {
            instance.scrollToElement(message, instance.options.scrollingUpGap);
        }

        // vibration signal
        instance.vibrate();
    }
});

wcpw.documentNode.addEventListener('submitError.wcpw', (event) => {
    const instance = event.detail.instance;

    if (!instance || instance.productsWithError.length <= 0) {
        return;
    }

    const product = instance.element
        .querySelector(
            `[data-component~="wcpw-product"][data-id="${instance.productsWithError[0].product_id}"]`
            + `[data-step-id="${instance.productsWithError[0].step_id}"]`
        );

    if (!product) {
        return;
    }

    // scroll window to the product
    if (!instance.isScrolledIntoView(product)) {
        instance.scrollToElement(product, instance.options.scrollingUpGap);
    }

    if (typeof $.fn.modal !== 'undefined') {
        // open product modal with EPO errors
        const $modal = $(product).find('[data-component~="wcpw-product-modal"] .tc-extra-product-options');

        if ($modal.length > 0) {
            $modal.closest('[data-component~="wcpw-product-modal"]').modal('show');
        }
    }

    // vibration signal
    instance.vibrate();
});

wcpw.documentNode.addEventListener('ajaxRequest.wcpw', (event) => {
    const instance = event.detail.instance;

    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        // close products modals
        $(instance.element).find('[data-component~="wcpw-product-modal"].show').modal('hide');
    }
});

// toggle element
wcpw.documentNode.addEventListener('toggle.wcpw', () => {
    if (wcpw.$body) {
        wcpw.$body.trigger('sticky_kit:recalc');
    }
});

if (wcpw.$document) {
    // disable/enable add-to-cart button for attached wizards
    wcpw.$document.on('hide_variation', '.variations_form', function () {
        const addToCartBtn = this.closest('.product')
            .querySelector(`[data-component~="wcpw-add-to-cart"][form="${this.getAttribute('id')}"]`);

        if (addToCartBtn.length > 0) {
            addToCartBtn.classList.add('disabled');
            addToCartBtn.setAttribute('disabled', 'disabled');
        }
    });

    wcpw.$document.on('show_variation', '.variations_form', function () {
        const addToCartBtn = this.closest('.product')
            .querySelector(`[data-component~="wcpw-add-to-cart"][form="${this.getAttribute('id')}"]`);

        if (addToCartBtn.length > 0) {
            addToCartBtn.classList.remove('disabled');
            addToCartBtn.removeAttribute('disabled');
        }
    });
}

if (wcpw.$window) {
    // save epo data for further validations
    wcpw.$window.on('tm-from-submit', (event, data) => {
        if (data.epo) {
            $(data.epo.form.eq(0)).data('wcpw-epo-data', data);
        }
    });
}

export default wcpw;
