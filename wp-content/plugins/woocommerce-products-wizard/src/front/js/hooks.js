/* WooCommerce Products Wizard global instance and main event handlers
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.ru
 */

(function (root, factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {
        define(['jquery', 'Masonry'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined' && typeof require === 'function') {
        module.exports = factory(require('jquery'), require('Masonry'));
    } else {
        factory(root.jQuery);
    }
})(this, function ($) {
    'use strict';
    
    const wcpw = {
        windowNode: window,
        documentNode: document,
        $window: $(window),
        $document: $(document),
        $body: $(document.body),
        stickyObserverElements: null,

        init: function () {
            if (typeof $.fn.wcpw === 'undefined') {
                this.windowNode.console.error('jQuery.fn.wcpw is not exist');

                return this;
            }

            this.$document.find('[data-component~="wcpw"]').each(function () {
                const $element = $(this);

                return $element.wcpw($element.data('options') || {});
            });

            return this;
        },

        initVariationForm: function ($elements) {
            $elements = $elements || this.$document.find('[data-component~="wcpw-product-variations"]');
            $elements.each(function () {
                const $element = $(this);

                return $element.wcpwVariationForm($element.data('options') || {});
            });

            return this;
        },

        onInit: function (event, instance) {
            // init variation forms
            this.initVariationForm(instance.$element.find('[data-component~="wcpw-product-variations"]'));

            // check product variations form for attached wizard
            if (instance.options && instance.options.attachedProduct) {
                this.$document.find('#' + instance.options.formId).trigger('check_variations');
            }

            // prettyPhoto init
            if (typeof $.fn.prettyPhoto !== 'undefined') {
                instance.$element
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
                this.windowNode.avadaLightBox.activate_lightbox(instance.$element);
            }

            // sticky elements init
            if (typeof $.fn.stick_in_parent !== 'undefined') {
                instance.$element.find('[data-component~="wcpw-sticky"]').each(function () {
                    const $element = $(this);

                    return $element.stick_in_parent($.extend(
                        // older versions support
                        {
                            parent: $element.data('sticky-parent'),
                            offset_top: Number($element.data('sticky-top-offset'))
                        },
                        $element.data('sticky-options')
                    ));
                });
            }

            // EPO plugin init
            this.reInitExtraProductOptions(instance);

            // noUi slider init
            this.documentNode.dispatchEvent(new CustomEvent('init.nouislider.wcpw'));

            // init step filter
            this.$document.trigger('init.wcsf');

            // masonry layout init
            if (typeof Masonry !== 'undefined') {
                instance.$element.find('[data-component~="wcpw-masonry"]').each(function () {
                    const $element = $(this);
                    const masonryInstance = new Masonry(
                        this,
                        $.extend(
                            {
                                itemSelector: '.col',
                                percentPosition: true,
                                columnWidth: this.querySelector('[data-component="wcpw-masonry-sizer"]')
                            },
                            $element.data('options')
                        )
                    );

                    $element.data('masonry-instance', masonryInstance);
                });
            }
        },

        reInitExtraProductOptions: function (instance) {
            if (typeof $.tcepo === 'undefined' || typeof $.tcepo.tm_init_epo === 'undefined') {
                return this;
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
            instance.$element.find('.tmcp-upload-hidden').remove();

            // unique container where the options are embedded. this is usually the parent tag of the cart form
            return instance.$element.find('[data-component~="wcpw-product"]').each(function () {
                const $product = $(this);
                const $options = $product.find('.tc-extra-product-options');
                const productId = $options.attr('data-product-id');
                const epoId = $options.attr('data-epo-id');

                if ($options.length <= 0 || !productId || !epoId) {
                    return;
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

                if ($product.data('type') === 'variable') {
                    $product.find('[data-component~="wcpw-product-variations"]').trigger('wc_variation_form.cpf');
                }
            });
        },

        saveExtraProductOptions: function (productToAdd) {
            if (typeof $.tcepo === 'undefined') {
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
            $.each(request, (key, value) => {
                if (Array.isArray(value)) {
                    value = value.filter((el) => el !== '');

                    if (value.length === 0) {
                        request[key] = '';
                    }
                }
            });

            productToAdd.request = $.extend(request, data);

            return true;
        },

        saveExtraProductOptionsAttachments: function (instance, data) {
            if (typeof $.tcepo === 'undefined') {
                return;
            }

            instance.$element.find('.tc-extra-product-options input[type="file"]').each(function () {
                if (!this.files[0] || !this.files[0].size) {
                    return this;
                }

                return data.append(this.name, this.files[0]);
            });
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
            this.$body.off('click', 'a.showcoupon');
            this.$body.off('click', '.woocommerce-remove-coupon');
            this.$body.off('click', 'a.showlogin');
            this.$body.off('click', 'a.woocommerce-terms-and-conditions-link');

            clone.src = src;
            clone.id = id;

            // append and init script
            this.documentNode.body.appendChild(clone);

            // init county select
            this.$body.trigger('country_to_state_changed');

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

    if (typeof document.wcpw === 'undefined') {
        document.wcpw = wcpw;
    }

    // main actions
    wcpw.$window.on('load', () => {
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

    wcpw.$document.ready(() => {
        wcpw.init();

        // contact form 7 hooks
        if (typeof wpcf7 !== 'undefined') {
            wcpw.$document.on('ajaxCompleted.wcpw', (event, instance) => {
                const form = instance.element.querySelector('.wpcf7-form');

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
        if (typeof $.tcepo !== 'undefined') {
            wcpw.$document.on('submit.wcpw', (event, instance, data) => {
                if (instance && data) {
                    // pass data to the request
                    $.each(data.productsToAdd, (key, product) => {
                        if (typeof data.productsToAddChecked[product.step_id] !== 'undefined'
                            && data.productsToAddChecked[product.step_id].indexOf(product.product_id) !== -1
                            && !wcpw.saveExtraProductOptions(product)
                        ) {
                            instance.hasError = true;
                            instance.productsWithError.push(product);
                        }
                    });
                }
            });

            wcpw.$document.on('ajaxRequest.wcpw', (event, instance, data) => {
                // save attachments
                wcpw.saveExtraProductOptionsAttachments(instance, data);
            });
        }
    });

    wcpw.$document.on('init.wcpw', () => wcpw.init());

    wcpw.$document.on('init.variationForm.wcpw', () => wcpw.initVariationForm());

    wcpw.$document.on('launched.wcpw', (event, instance) => {
        wcpw.onInit(event, instance);

        // off default WC form scripts
        setTimeout(() => {
            instance.$element.find('[data-component~="wcpw-product-variations"]').off('.wc-variation-form');
        }, 100);
    });

    // ajax actions
    wcpw.$document.on('ajaxCompleted.wcpw', (event, instance, response) => {
        wcpw.onInit(event, instance);

        // re-init sticky observer elements
        wcpw.stickyObserverElements = instance.element.querySelectorAll('[data-component~="wcpw-sticky-observer"]');
        wcpw.updateStickyObserverElementsState();

        // refresh WC mini-cart if wizard reflects the cart
        if (instance.options && instance.options.reflectInMainCart) {
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

    wcpw.$document.on('submitError.wcpw', (event, instance) => {
        if (!instance || instance.productsWithError.length <= 0) {
            return this;
        }

        const $product = instance.$element
            .find(
                `[data-component~="wcpw-product"][data-id="${instance.productsWithError[0].product_id}"]`
                + `[data-step-id="${instance.productsWithError[0].step_id}"]`
            );

        if ($product.length <= 0) {
            return this;
        }

        // scroll window to the product
        if (!instance.isScrolledIntoView($product.get(0))) {
            instance.scrollToElement($product.get(0), instance.options.scrollingUpGap);
        }

        if (typeof $.fn.modal !== 'undefined') {
            // open product modal with EPO errors
            const $modal = $product.find('[data-component~="wcpw-product-modal"] .tc-extra-product-options');

            if ($modal.length > 0) {
                $modal.closest('[data-component~="wcpw-product-modal"]').modal('show');
            }
        }

        // vibration signal
        instance.vibrate();

        return this;
    });

    wcpw.$document.on('ajaxRequest.wcpw', (event, instance) => {
        if (typeof $.fn.modal !== 'undefined') {
            // close products modals
            instance.$element.find('[data-component~="wcpw-product-modal"].show').modal('hide');
        }
    });

    // toggle element
    wcpw.$document.on('toggle.wcpw', () => wcpw.$body.trigger('sticky_kit:recalc'));

    // disable/enable add-to-cart button for attached wizards
    wcpw.$document.on('hide_variation', '.variations_form', function () {
        const $form = $(this);
        const $addToCartBtn = $form.closest('.product')
            .find(`[data-component~="wcpw-add-to-cart"][form="${$form.attr('id')}"]`);

        if ($addToCartBtn.length > 0) {
            $addToCartBtn.addClass('disabled').attr('disabled', true);
        }
    });

    wcpw.$document.on('show_variation', '.variations_form', function () {
        const $form = $(this);
        const $addToCartBtn = $form.closest('.product')
            .find(`[data-component~="wcpw-add-to-cart"][form="${$form.attr('id')}"]`);

        if ($addToCartBtn.length > 0) {
            $addToCartBtn.removeClass('disabled').removeAttr('disabled');
        }
    });

    // save epo data for further validations
    wcpw.$window.on('tm-from-submit', (event, data) => {
        if (data.epo) {
            $(data.epo.form.eq(0)).data('wcpw-epo-data', data);
        }
    });

    // support deprecated actions
    // @since 9.2.0
    const actions = {
        'launched.wcProductsWizard': 'launched.wcpw',
        'ajaxRequest.wcProductsWizard': 'ajaxRequest.wcpw',
        'ajaxCompleted.wcProductsWizard': 'ajaxCompleted.wcpw',
        'addToMainCart.wcProductsWizard': 'addToMainCart.wcpw',
        'addToMainCart.error.wcProductsWizard': 'addToMainCartError.wcpw',
        'addToMainCart.error.wcpw': 'addToMainCartError.wcpw',
        'addToMainCartRedirect.wcProductsWizard': 'addToMainCartRedirect.wcpw',
        'submit.wcProductsWizard': 'submit.wcpw',
        'submit.error.wcProductsWizard': 'submitError.wcpw',
        'submit.error.wcpw': 'submitError.wcpw'
    };

    $.each(actions, (oldAction, newAction) => {
        wcpw.$document.on(newAction, (event, ...args) => {
            wcpw.$document.trigger(oldAction, args);
        });
    });
});
