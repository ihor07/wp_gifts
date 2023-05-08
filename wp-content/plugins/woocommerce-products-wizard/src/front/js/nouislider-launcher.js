/* WooCommerce Products Wizard noUiSlider initialization
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.ru
 */

(function (root, factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {
        define(['wNumb', 'noUiSlider'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined' && typeof require === 'function') {
        module.exports = factory(require('wNumb'), require('noUiSlider'));
    } else {
        factory(root.wNumb, root.noUiSlider);
    }
})(this, function (wNumb, noUiSlider) {
    'use strict';

    const defaultOptions = {
        cssPrefix: 'wcpw-noUi-',
        direction: document.documentElement.getAttribute('dir') || 'ltr',
        range: {
            max: 100,
            min: 0
        },
        start: 50
    };

    function initSlider(element) {
        const bindItems = [];
        const options = [defaultOptions, JSON.parse(element.getAttribute('data-options') || '{}')]
            .reduce((output, object) => {
                Object.keys(object).forEach((key) => {
                    output[key] = object[key];
                });

                return output;
            }, {});

        // return if there is no range
        if (options.hasOwnProperty('range') && options.range.min === options.range.max) {
            return this;
        }

        element.setAttribute('data-launched', 'true');

        // handle output format
        if (options.hasOwnProperty('format') && typeof wNumb !== 'undefined') {
            options.format = wNumb(options.format);
        }

        noUiSlider.create(element, options);

        // get binding items
        if (options.hasOwnProperty('binding')) {
            if (typeof options.binding === 'string') {
                bindItems.push(options.binding);
            } else if (options.binding instanceof Array) {
                bindItems.push(...options.binding);
            }
        }

        if (bindItems.length > 0) {
            // handle items change
            bindItems.map((bindItem, index) => {
                let selector = bindItem;

                if (Array.isArray(bindItem)) {
                    const selectors = [];

                    bindItem.map((bindItemPart) => selectors.push(bindItemPart));
                    selector = selectors.join(',');
                }

                bindItems[index] = document.querySelectorAll(selector);

                // bind inputs change
                for (let bindElement of bindItems[index]) {
                    if (['input', 'select', 'textarea'].includes(bindElement.tagName.toLowerCase())) {
                        bindElement.addEventListener('change', function () {
                            const values = [];

                            for (let i = 0; i < index; i++) {
                                values.push(null);
                            }

                            values.push(this.value);

                            // apply value to the slide
                            element.noUiSlider.set(values);
                        });
                    }
                }

                return null;
            });
        }

        // on update action
        element.noUiSlider.on('update', (values, handle) => {
            // update bind items
            for (let bindElement of bindItems[handle]) {
                if (['input', 'select', 'textarea'].includes(bindElement.tagName.toLowerCase())) {
                    bindElement.value = values[handle];
                } else {
                    bindElement.innerText = values[handle];
                }
            }
        });

        return this;
    }

    function initSliders() {
        for (
            let element
            of document.querySelectorAll('[data-component~="wcpw-no-ui-slider"]:not([data-launched="true"])')
        ) {
            initSlider(element);
        }
    }

    document.addEventListener('DOMContentLoaded', initSliders);
    document.addEventListener('init.nouislider.wcpw', initSliders);
});
