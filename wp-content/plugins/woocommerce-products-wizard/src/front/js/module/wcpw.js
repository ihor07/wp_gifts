/* WooCommerce Products Wizard app
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.ru
 */

class WCPW {
    // <editor-fold desc="Core">
    constructor(element, options = {}) {
        const defaults = {
            documentNode: document,
            windowNode: window,
            rootSelector: 'html, body',
            searchStringMinLength: 3,
            ajaxActions: {
                submit: 'wcpwSubmit',
                addToMainCart: 'wcpwAddToMainCart',
                getStep: 'wcpwGetStep',
                skipStep: 'wcpwSkipStep',
                skipAll: 'wcpwSkipAll',
                submitAndSkipAll: 'wcpwSubmitAndSkipAll',
                reset: 'wcpwReset',
                addCartProduct: 'wcpwAddCartProduct',
                removeCartProduct: 'wcpwRemoveCartProduct',
                updateCartProduct: 'wcpwUpdateCartProduct',
                addCartStepData: 'wcpwAddCartStepData',
                removeCartStepData: 'wcpwRemoveCartStepData',
                search: 'wcpwSearch'
            }
        };

        this.element = element;
        this.customOptions = options;
        this.options = Object.assign({}, defaults, options);
    }

    /**
     * Init the instance
     * @returns {this} self instance
     */
    init() {
        this.hasError = false;
        this.preventAjaxRequest = false;
        this.productsWithError = [];
        this.ajaxRequestsQueue = [];
        this.eventListeners = [];

        return this.initEventListeners().triggerEvent('launched.wcpw', {instance: this});
    }

    /**
     * Makes an ajax-request
     * @param {FormData | Object} requestData - request data to pass
     * @param {Object} options - request options
     * @returns {Promise} ajax request
     */
    ajaxRequest(requestData, options = {}) {
        this.preventAjaxRequest = false;

        const instance = this; // eslint-disable-line
        const formData = requestData instanceof FormData ? requestData : new FormData();
        const defaultOptions = {
            updateQueryArgs: true,
            queryArgs: {},
            currentQueryArgs: this.getQueryArgs(),
            cache: 'default',
            method: 'post',
            scrollingTopOnUpdate: Boolean(this.options.scrollingTopOnUpdate),
            scrollingGap: Number(this.options.scrollingUpGap),
            scrollingBehavior: 'smooth',
            scrollingToElement: null,
            passProducts: false,
            passStepData: false,
            errorsAlerting: true,
            errorsLogging: true,
            lazy: false
        };

        options = this.extendObject(defaultOptions, options);

        // pass extra args
        if (!(requestData instanceof FormData)) {
            for (let key in requestData) {
                if (requestData.hasOwnProperty(key)) {
                    formData.append(key, typeof requestData[key] !== 'string' ? JSON.stringify(requestData[key]) : requestData[key]);
                }
            }
        }

        // remove stepsData fields to pass them right from the form as binary
        delete requestData.stepsData;

        // pass step data
        if (options.passStepData) {
            const form = this.element.querySelector('[data-component~="wcpw-form"]')
                || this.options.documentNode.querySelector('[data-component~="wcpw-form"]');

            const formElementData = new FormData(form);
            const stepDataToAddKey = formData.get('stepDataToAddKey');

            for (let pair of formElementData.entries()) {
                if (pair[0].includes('stepsData')
                    && (typeof pair[1] === 'object' && pair[1].name || typeof pair[1] === 'string')
                ) {
                    // add only specific step data key
                    if (stepDataToAddKey && pair[0] !== stepDataToAddKey) {
                        continue;
                    }

                    formData.append(pair[0], pair[1]);
                }
            }
        }

        // don't pass products if needed
        if (!options.passProducts) {
            formData.delete('productsToAdd');
            formData.delete('productsToAddChecked');
        }

        // save extra parameters
        for (let key in this.customOptions) {
            if (this.customOptions.hasOwnProperty(key)) {
                formData.append(
                    key,
                    typeof this.customOptions[key] !== 'string'
                        ? JSON.stringify(this.customOptions[key])
                        : this.customOptions[key]
                );
            }
        }

        // delete "add-to-cart" to not pass the attached product to the cart via AJAX
        formData.delete('add-to-cart');

        // add current query args to request
        if (options.currentQueryArgs.get) {
            // add extra query from "get" parameter
            if (options.currentQueryArgs.has('wcpwFilter')) {
                formData.append('wcpwFilter', options.currentQueryArgs.get('wcpwFilter'));
            }

            if (options.currentQueryArgs.has('wcpwPage')) {
                formData.append('wcpwPage', options.currentQueryArgs.get('wcpwPage'));
            }

            if (options.currentQueryArgs.has('wcpwProductsPerPage')) {
                formData.append('wcpwProductsPerPage', options.currentQueryArgs.get('wcpwProductsPerPage'));
            }

            if (options.currentQueryArgs.has('wcpwOrderBy')) {
                formData.append('wcpwOrderBy', options.currentQueryArgs.get('wcpwOrderBy'));
            }
        }

        // save passed query args to request
        if (options.queryArgs) {
            for (let key in options.queryArgs) {
                if (options.queryArgs.hasOwnProperty(key)) {
                    formData.append(key, options.queryArgs[key]);
                }
            }
        }

        this.triggerEvent('ajaxRequest.wcpw', {instance, formData, options});

        if (this.preventAjaxRequest) {
            this.triggerEvent('ajaxPrevent.wcpw', {instance, formData, options});

            return Promise.resolve();
        }

        this.element.classList.add(options.lazy ? 'is-lazy-loading' : 'is-loading');
        this.element.setAttribute('aria-live', 'polite');
        this.element.setAttribute('aria-busy', 'true');

        const request = this.options.windowNode
            .fetch(
                this.options.ajaxUrl,
                {
                    method: options.method,
                    cache: options.cache,
                    body: formData
                }
            )
            .then((response) => response.json())
            .then((response) => {
                this.triggerEvent('ajaxSuccess.wcpw', {instance, response, formData, options});

                const requestIndex = this.ajaxRequestsQueue.indexOf(request);

                if (requestIndex > -1) {
                    this.ajaxRequestsQueue.splice(requestIndex, 1);
                }

                if (options.lazy && this.ajaxRequestsQueue.length > 0) {
                    return response;
                }

                this.element.classList.remove('is-lazy-loading', 'is-loading');
                this.element.setAttribute('aria-busy', 'false');

                if (response.content) {
                    this.element.innerHTML = response.content;
                }

                // scroll navs
                for (const nav of this.element.querySelectorAll('[data-component~="wcpw-nav"]')) {
                    const navList = nav.querySelector('[data-component~="wcpw-nav-list"]');

                    if (navList) {
                        navList.scrollLeft = nav.querySelector('.active').offsetLeft;
                    }
                }

                // scroll window
                if (options.scrollingTopOnUpdate) {
                    const element = this.element
                        .querySelector(options.scrollingToElement || '[data-component~="wcpw-form-step"].is-active');

                    if (element && !this.isScrolledIntoView(element)) {
                        this.scrollToElement(element, Number(options.scrollingGap), options.scrollingBehavior);
                    }
                }

                if (options.updateQueryArgs && options.queryArgs) {
                    if (response.hasOwnProperty('stateHash')) {
                        options.queryArgs.wcpwStateHash = response.stateHash;
                    }

                    if (response.hasOwnProperty('saveStateToURL') && Boolean(response.saveStateToURL)) {
                        if (response.hasOwnProperty('id')) {
                            options.queryArgs.wcpwId = response.id;
                        }

                        if (response.hasOwnProperty('stepId')) {
                            options.queryArgs.wcpwStep = response.stepId;
                        }

                        if (response.hasOwnProperty('cart')) {
                            options.queryArgs.wcpwCart = JSON.stringify(response.cart);
                        }
                    } else {
                        options.queryArgs.wcpwStep = false;
                    }

                    if (Object.keys(options.queryArgs)
                        .filter((item) => options.queryArgs[item] !== false).length !== 0
                    ) {
                        this.setQueryArg(options.queryArgs);
                    }
                }

                this.triggerEvent('ajaxCompleted.wcpw', {instance, response, formData, options});

                return response;
            })
            .catch((error) => {
                this.triggerEvent('ajaxError.wcpw', {instance, error, formData, options});

                const requestIndex = this.ajaxRequestsQueue.indexOf(request);

                if (requestIndex > -1) {
                    this.ajaxRequestsQueue.splice(requestIndex, 1);
                }

                this.element.classList.remove('is-lazy-loading', 'is-loading');
                this.element.setAttribute('aria-busy', 'false');

                if (options.errorsLogging) {
                    this.options.windowNode.console.error(error);
                }

                if (options.errorsAlerting) {
                    this.options.windowNode.alert(`Unexpected error occurred: ${error}`);
                }

                return error;
            });

        this.ajaxRequestsQueue.push(request);

        return request;
    }

    /**
     * Send search request
     * @param {String} query - search text query
     * @param {String} targetSelector - results list selector
     * @param {Object} filterArgs - filter form request object
     * @returns {Promise} ajax query
     */
    search(query, targetSelector, filterArgs) {
        if (!query || query.length < this.options.searchStringMinLength) {
            return Promise.resolve();
        }

        const target = this.element.querySelector(targetSelector);
        const template = target.getAttribute('data-item-template');
        const defaultData = {
            action: this.options.ajaxActions.search,
            id: this.options.id,
            query
        };

        return this.options.windowNode
            .fetch(this.options.ajaxUrl + '?' + this.objectToQueryParam(this.extendObject(defaultData, filterArgs)))
            .then((response) => response.json())
            .then((response) => {
                const html = [];

                if (response.items.length > 0) {
                    for (let key in response.items) {
                        if (!response.items.hasOwnProperty(key)) {
                            continue;
                        }

                        let element = template;

                        for (let property in response.items[key]) {
                            if (response.items[key].hasOwnProperty(property)) {
                                element = element.replace(
                                    new RegExp('\\${' + property + '}', 'g'),
                                    response.items[key][property]
                                );
                            }
                        }

                        html.push(element);
                    }
                }

                target.innerHTML = html.join('');

                return response;
            });
    }

    /**
     * Delegate an event listener to a target
     * @param {String} action - event action name
     * @param {String} selector - target element selector
     * @param {Function} callback - function to fire
     * @param {Object} options - listener options
     * @returns {this} self instance
     */
    delegateEventListener(action, selector, callback, options = {capture: false}) {
        const actionName = action.split('.')[0];
        const handler = function (event) {
            let target = event.target;

            while (target && !target.matches(selector) && target !== this) {
                target = target.parentElement;
            }

            if (target && target.matches(selector)) {
                callback.call(target, event);
            }

            return this;
        };

        this.eventListeners.push({
            action,
            actionName,
            selector,
            handler
        });

        this.element.addEventListener(actionName, handler, options);

        return this;
    }

    /**
     * Un-delegate an event listener from the target
     * @param {String} action - event action name
     * @param {String} selector - target element selector
     * @param {Object} options - listener options
     * @returns {this} self instance
     */
    unDelegateEventListener(action, selector = '', options = {capture: false}) {
        const events = this.eventListeners
            .filter((item) => item.action === action && (!selector || item.selector === selector));

        if (events[0]) {
            this.element.removeEventListener(events[0].actionName, events[0].handler, options);
        }

        return this;
    }

    /**
     * Add required event listeners
     * @returns {this} self instance
     */
    initEventListeners() {
        const _this = this;

        // browser history handlers
        this.options.windowNode.addEventListener('popstate', (event) => this.popState(event), false);

        // prevent thumbnail link redirect on click
        this.delegateEventListener(
            'click.thumbnail.product.wcpw',
            '[data-component~="wcpw-product-thumbnail-link"]',
            (event) => event.preventDefault()
        );

        // change the active form item
        this.delegateEventListener('click.product.wcpw', '[data-component~="wcpw-product"]', function () {
            const input = this.querySelector('[data-component~="wcpw-product-choose"][type="radio"]');

            if (input && !input.checked && !input.disabled) {
                input.checked = true;
                input.dispatchEvent(new Event('change'));
            }
        });

        // add product to the cart
        this.delegateEventListener(
            'click.add.product.cart.wcpw',
            '[data-component~="wcpw-add-cart-product"]',
            function (event) {
                if (this.classList.contains('disabled')) {
                    return event.preventDefault();
                }

                const otherInputs = [];
                const product = this.closest('[data-component~="wcpw-product"]');
                const inputs = _this.element
                    .querySelectorAll('input:not(:disabled),select:not(:disabled),textarea:not(:disabled)');

                for (const input of inputs) {
                    if (!product.contains(input)) {
                        otherInputs.push(input);
                    }
                }

                if (!_this.options.documentNode.querySelector('#' + this.getAttribute('form')).checkValidity()) {
                    for (const input of otherInputs) {
                        input.disabled = false;
                    }

                    return this;
                }

                for (const input of otherInputs) {
                    input.disabled = false;
                }

                this.classList.add('is-loading');
                this.setAttribute('aria-busy', 'true');

                event.preventDefault();

                return _this
                    .addCartProduct(
                        {productToAddKey: this.value},
                        JSON.parse(this.getAttribute('data-add-cart-product-options') || '{}')
                    )
                    .finally(() => {
                        this.classList.remove('is-loading');
                        this.setAttribute('aria-busy', 'false');
                    });
            }
        );

        // update product in the cart
        this.delegateEventListener(
            'click.update.product.cart.wcpw',
            '[data-component~="wcpw-update-cart-product"]',
            function (event) {
                if (this.classList.contains('disabled')) {
                    return event.preventDefault();
                }

                const otherInputs = [];
                const product = this.closest('[data-component~="wcpw-product"]');
                const inputs = _this.element
                    .querySelectorAll('input:not(:disabled),select:not(:disabled),textarea:not(:disabled)');

                for (const input of inputs) {
                    if (!product.contains(input)) {
                        otherInputs.push(input);
                    }
                }

                for (const input of otherInputs) {
                    input.disabled = true;
                }

                for (const input of otherInputs) {
                    input.disabled = true;
                }

                if (!_this.options.documentNode.querySelector('#' + this.getAttribute('form')).checkValidity()) {
                    for (const input of otherInputs) {
                        input.disabled = false;
                    }

                    return this;
                }

                for (const input of otherInputs) {
                    input.disabled = false;
                }

                this.classList.add('is-loading');
                this.setAttribute('aria-busy', 'true');

                event.preventDefault();

                return _this
                    .updateCartProduct(
                        {productCartKey: this.value},
                        JSON.parse(this.getAttribute('data-update-cart-product-options') || '{}')
                    )
                    .finally(() => {
                        this.classList.remove('is-loading');
                        this.setAttribute('aria-busy', 'false');
                    });
            }
        );

        // remove product from the cart
        this.delegateEventListener(
            'click.remove.product.cart.wcpw',
            '[data-component~="wcpw-remove-cart-product"]',
            function (event) {
                event.preventDefault();

                this.classList.add('is-loading');
                this.setAttribute('aria-busy', 'true');

                return _this
                    .removeCartProduct(
                        {productCartKey: this.value},
                        JSON.parse(this.getAttribute('data-remove-cart-product-options') || '{}')
                    )
                    .finally(() => {
                        this.classList.remove('is-loading');
                        this.setAttribute('aria-busy', 'false');
                    });
            }
        );

        // add step data to the cart
        this.delegateEventListener(
            'change.add.stepData.cart.wcpw',
            '[data-component~="wcpw-add-cart-step-data"]',
            function (event) {
                if (this.classList.contains('disabled')) {
                    return event.preventDefault();
                }

                event.preventDefault();

                this.classList.add('is-loading');
                this.setAttribute('aria-busy', 'true');

                return _this
                    .addCartStepData(
                        {stepDataToAddKey: this.getAttribute('name')},
                        JSON.parse(this.getAttribute('data-add-cart-step-data-options') || '{}')
                    )
                    .finally(() => {
                        this.classList.remove('is-loading');
                        this.setAttribute('aria-busy', 'false');
                    });
            }
        );

        // remove step data from the cart
        this.delegateEventListener(
            'change.remove.stepData.cart.wcpw',
            '[data-component~="wcpw-remove-cart-step-data"]',
            function (event) {
                event.preventDefault();

                this.classList.add('is-loading');
                this.setAttribute('aria-busy', 'true');

                return _this
                    .removeCartStepData(
                        {
                            stepDataKey: this.getAttribute('data-name'),
                            stepId: this.getAttribute('data-step-id')
                        },
                        JSON.parse(this.getAttribute('data-remove-cart-step-data-options') || '{}')
                    )
                    .finally(() => {
                        this.classList.remove('is-loading');
                        this.setAttribute('aria-busy', 'false');
                    });
            }
        );

        // nav item click
        this.delegateEventListener('click.nav.wcpw', '[data-component~="wcpw-nav-item"]', function (event) {
            const action = this.getAttribute('data-nav-action');
            const data = {action};

            if (!_this.options.documentNode.querySelector('#' + this.getAttribute('form')).checkValidity()
                && ['submit', 'add-to-main-cart', 'add-to-main-cart-repeat'].indexOf(action) !== -1
            ) {
                return this;
            }

            event.preventDefault();

            if (this.getAttribute('data-nav-id')) {
                data.stepId = this.getAttribute('data-nav-id');
            }

            return _this.navRouter(data);
        });

        // share button click
        this.delegateEventListener('click.share.wcpw', '[data-component~="wcpw-share"]', function (event) {
            if (_this.setClipboard(this.getAttribute('href'))) {
                event.preventDefault();

                this.querySelector('*').innerText = this.getAttribute('data-share-success-message');
            }

            return this;
        });

        // filter submit
        this.delegateEventListener('submit.filter.wcpw', '[data-component~="wcpw-filter"]', function (event) {
            event.preventDefault();

            const queryArgs = _this.getQueryArgs();
            const filterData = _this.serializeObject(this).wcpwFilter;
            const stepId = this.getAttribute('data-step-id');
            let pages = {};
            let filters = {};

            if (queryArgs.get) {
                // change filter query
                if (queryArgs.has('wcpwFilter') && queryArgs.get('wcpwFilter')) {
                    filters = _this.queryStringToObject(queryArgs.get('wcpwFilter'));
                }

                // reset page query
                if (queryArgs.has('wcpwPage') && queryArgs.get('wcpwPage')) {
                    pages = _this.queryStringToObject(queryArgs.get('wcpwPage'));
                    pages[stepId] = 1;
                }
            }

            filters = _this.extendObject(filters, filterData);

            return _this.getStep(
                {stepId},
                {
                    queryArgs: {
                        wcpwFilter: _this.objectToQueryParam(filters),
                        wcpwPage: _this.objectToQueryParam(pages)
                    }
                }
            );
        });

        // filter reset
        this.delegateEventListener('reset.filter.wcpw', '[data-component~="wcpw-filter"]', function (event) {
            event.preventDefault();

            const queryArgs = _this.getQueryArgs();
            const filterData = _this.serializeObject(this).wcpwFilter;
            const stepId = this.getAttribute('data-step-id');
            let filters = {};

            if (queryArgs.get && queryArgs.has('wcpwFilter')) {
                filters = _this.queryStringToObject(queryArgs.get('wcpwFilter'));
            }

            filters = _this.extendObject(filters, {[Object.keys(filterData)[0]]: {}});

            return _this.getStep({stepId}, {queryArgs: {wcpwFilter: _this.objectToQueryParam(filters)}});
        });

        // auto-submit form on change
        this.delegateEventListener('change.autoSubmit.wcpw', '[data-component~="wcpw-submit-on-change"]', function () {
            this.dispatchEvent(new Event('submit', {bubbles: true}));
        });

        // search form input change
        this.delegateEventListener('input.search.wcpw', '[data-component~="wcpw-search-form-input"]', function () {
            const filterData = _this.serializeObject(this.closest('[data-component~="wcpw-filter"]'));

            // clear previous timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            // abort the current request
            if (this.searchPromise && this.searchPromise.abort) {
                this.searchPromise.abort();
            }

            // add extra step-id argument
            filterData.stepId = this.getAttribute('data-step-id');

            // attach a new request in a second
            this.searchTimeout = setTimeout(
                () => {
                    this.classList.add('is-loading');
                    this.setAttribute('aria-busy', 'true');
                    this.searchPromise = _this
                        .search(
                            this.value,
                            this.getAttribute('data-target'),
                            filterData
                        )
                        .finally(() => {
                            this.classList.remove('is-loading');
                            this.setAttribute('aria-busy', 'false');
                        });
                },
                1000
            );

            return this.searchTimeout;
        });

        // search form results item click
        this.delegateEventListener(
            'click.result.search.wcpw',
            '[data-component~="wcpw-search-form-results"] [data-value]',
            function (event) {
                event.preventDefault();

                const target = this.closest('[data-component~="wcpw-search-form-results"]').getAttribute('data-target');

                if (target) {
                    _this.element.querySelector(target).value = this.getAttribute('data-value');
                }

                return this;
            }
        );

        // pagination link click
        this.delegateEventListener(
            'click.pagination.wcpw',
            '[data-component~="wcpw-form-pagination-link"]',
            function (event) {
                event.preventDefault();

                const queryArgs = _this.getQueryArgs();
                const stepId = this.getAttribute('data-step-id');
                const page = this.getAttribute('data-page');
                let pages = {};

                // change page query
            if (queryArgs.get && queryArgs.has('wcpwPage') && queryArgs.get('wcpwPage')) {
                    pages = _this.queryStringToObject(queryArgs.get('wcpwPage'));
                }

                pages[stepId] = page;

                return _this.getStep(
                    {
                        stepId,
                        page
                    },
                    {
                        queryArgs: {wcpwPage: _this.objectToQueryParam(pages)},
                        scrollingTopOnUpdate: true,
                        scrollingToElement: `[data-component~="wcpw-form-step"][data-id="${stepId}"]`
                    }
                );
            }
        );

        // products per page submit
        this.delegateEventListener(
            'submit.productsPerPage.wcpw',
            '[data-component~="wcpw-form-products-per-page"]',
            function (event) {
                event.preventDefault();

                const queryArgs = _this.getQueryArgs();
                const value = _this.serializeObject(this);
                const stepId = this.getAttribute('data-step-id');
                let pages = {};
                let productsPerPage = {};

                if (queryArgs.get) {
                if (queryArgs.has('wcpwProductsPerPage')) {
                    productsPerPage = _this.queryStringToObject(queryArgs.get('wcpwProductsPerPage'));
                }

                // reset page query
                if (queryArgs.has('wcpwPage') && queryArgs.get('wcpwPage')) {
                    pages = _this.queryStringToObject(queryArgs.get('wcpwPage'));
                    pages[stepId] = 1;
                }
                }

                productsPerPage[stepId] = value.wcpwProductsPerPage[stepId];

                return _this.getStep(
                    {stepId},
                    {
                        queryArgs: {
                            wcpwProductsPerPage: _this.objectToQueryParam(productsPerPage),
                            wcpwPage: _this.objectToQueryParam(pages)
                        }
                    }
                );
            }
        );

        // products order-by submit
        this.delegateEventListener('submit.orderBy.wcpw', '[data-component~="wcpw-form-order-by"]', function (event) {
            event.preventDefault();

            const queryArgs = _this.getQueryArgs();
            const value = _this.serializeObject(this);
            const stepId = this.getAttribute('data-step-id');
            let orderBy = {};

            if (queryArgs.get && queryArgs.has('wcpwOrderBy')) {
                orderBy = _this.queryStringToObject(queryArgs.get('wcpwOrderBy'));
            }

            orderBy[stepId] = value.wcpwOrderBy[stepId];

            return _this.getStep({stepId}, {queryArgs: {wcpwOrderBy: _this.objectToQueryParam(orderBy)}});
        });

        // toggle element
        this.delegateEventListener('click.toggle.wcpw', '[data-component~="wcpw-toggle"]', function (event) {
            event.preventDefault();

            const targetSelector = this.getAttribute('data-target') || this.getAttribute('href');
            const target = _this.element.querySelector(targetSelector);
            const isClosed = target.getAttribute('aria-expanded') === 'false';

            this.setAttribute('aria-expanded', isClosed ? 'true' : 'false');
            target.setAttribute('aria-expanded', isClosed ? 'true' : 'false');

            _this.options.documentNode.cookie = `${targetSelector}-expanded=${String(isClosed)}; path=/`;
            _this.triggerEvent('toggle.wcpw', {instance: _this, target});

            return this;
        });
        
        return this;
    }

    /**
     * Dispatch an event
     * @param {String} name - event name
     * @param {Object} options - object of arguments
     * @returns {this} self instance
     */
    triggerEvent(name, options = {}) {
        this.element.dispatchEvent(new CustomEvent(name, {bubbles: true, detail: options}));

        return this;
    }

    /**
     * Get a step by the previous state
     * @param {PopStateEvent} event - window history pop event
     * @returns {Promise} ajax request
     */
    popState(event) {
        const requestArgs = {action: this.options.ajaxActions.getStep};
        const openingPath = event && event.state && event.state.path;

        if (!openingPath) {
            return Promise.resolve();
        }

        const queryArgs = new URL(openingPath).searchParams;

        if (queryArgs.has('wcpwStep')) {
            requestArgs.stepId = queryArgs.get('wcpwStep');
        }

        if (queryArgs.has('wcpwFilters')) {
            requestArgs.filters = this.queryStringToObject(queryArgs.get('wcpwFilters'))[requestArgs.stepId];
        }

        if (queryArgs.has('wcpwPages')) {
            requestArgs.page = this.queryStringToObject(queryArgs.get('wcpwPages'))[requestArgs.stepId];
        }

        if (queryArgs.has('wcpwOrderBy')) {
            requestArgs.orderby = this.queryStringToObject(queryArgs.get('wcpwOrderBy'))[requestArgs.stepId];
        }

        return this.ajaxRequest(requestArgs, {updateQueryArgs: false});
    };
    // </editor-fold>

    // <editor-fold desc="Product actions">
    /**
     * Add form product to the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    addCartProduct(data = {}, options = {}) {
        const defaultOptions = {
            behavior: 'default',
            passProducts: true,
            passStepData: false
        };

        data = this.extendObject({action: this.options.ajaxActions.addCartProduct}, data);
        options = this.extendObject(defaultOptions, options);

        // change the action to submit
        switch (options.behavior) {
            default:
            case 'default':
                return this.submit(data, this.extendObject({scrollingTopOnUpdate: false}, options));

            case 'submit':
                return this.submit(data, options);

            case 'add-to-main-cart':
                return this.addToMainCart(data, options);
        }
    }

    /**
     * Update form product in the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    updateCartProduct(data = {}, options = {}) {
        const defaultOptions = {
            behavior: 'default',
            passProducts: true,
            passStepData: false
        };

        data = this.extendObject({action: this.options.ajaxActions.updateCartProduct}, data);
        options = this.extendObject(defaultOptions, options);

        // change the action to submit
        switch (options.behavior) {
            default:
            case 'default':
                return this.submit(data, this.extendObject({scrollingTopOnUpdate: false}, options));

            case 'submit':
                return this.submit(data, options);

            case 'add-to-main-cart':
                return this.addToMainCart(data, options);
        }
    }

    /**
     * Remove form product from the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    removeCartProduct(data = {}, options = {}) {
        const defaultOptions = {
            scrollingTopOnUpdate: false,
            passProducts: true
        };

        data = this.extendObject({action: this.options.ajaxActions.removeCartProduct}, data);
        options = this.extendObject(defaultOptions, options);

        // make custom request instead of the form submit
        return this.ajaxRequest(data, options);
    }
    // </editor-fold>

    // <editor-fold desc="Step data actions">
    /**
     * Add form step data to the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    addCartStepData(data = {}, options = {}) {
        const defaultOptions = {
            behavior: 'default',
            passProducts: false,
            passStepData: true
        };

        options = this.extendObject(defaultOptions, options);

        // change the action to submit
        switch (options.behavior) {
            default:
            case 'default':
                return this.submit(
                    this.extendObject({action: this.options.ajaxActions.addCartStepData}, data),
                    this.extendObject({scrollingTopOnUpdate: false}, options)
                );

            case 'submit':
                return this.submit(data, options);

            case 'add-to-main-cart':
                return this.addToMainCart(data, options);
        }
    }

    /**
     * Remove form step data from the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    removeCartStepData(data = {}, options = {}) {
        data = this.extendObject({action: this.options.ajaxActions.removeCartStepData}, data);
        options = this.extendObject({scrollingTopOnUpdate: false}, options);

        // make custom request instead of the form submit
        return this.ajaxRequest(data, options);
    }
    // </editor-fold>

    // <editor-fold desc="Main actions">
    /**
     * Add selected products to the main cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    addToMainCart(data = {}, options = {}) {
        const instance = this; // eslint-disable-line
        const defaultOptions = {
            preventRedirect: false,
            passProducts: true,
            passStepData: true
        };

        data = this.extendObject({action: this.options.ajaxActions.addToMainCart}, data);
        options = this.extendObject(defaultOptions, options);

        const result = this.submit(data, options);

        this.triggerEvent('addToMainCart.wcpw', {instance, data, result});

        if (!result) {
            return Promise.resolve();
        }

        return result.then((response) => {
            // has some product errors
            if (response.hasError || this.hasError) {
                this.triggerEvent('addToMainCartError.wcpw', {instance, data, response});

                return response;
            }

            if (!options.preventRedirect && !response.preventRedirect && response.finalRedirectUrl) {
                this.triggerEvent('addToMainCartRedirect.wcpw', {instance, data, response});

                this.options.documentNode.location = response.finalRedirectUrl;
            }

            return response;
        });
    }

    /**
     * Add selected products to the main cart and repeat the workflow
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    addToMainCartRepeat(data = {}, options = {}) {
        data.getContent = true;
        options.preventRedirect = true;

        return this.addToMainCart(data, options);
    }

    /**
     * Send custom products from the active step to the wizard cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request or false
     */
    submit(data = {}, options = {}) {
        // reset error state
        this.hasError = false;
        this.productsWithError = [];

        const instance = this;
        const form = this.element.querySelector('[data-component~="wcpw-form"]')
            || this.options.documentNode.querySelector('[data-component~="wcpw-form"]');

        const formData = this.serializeObject(form);
        const defaultData = {
            action: this.options.ajaxActions.submit,
            productToAddKey: null,
            productsToAdd: [],
            productsToAddChecked: []
        };

        const defaultOptions = {
            passProducts: true,
            passStepData: true
        };

        data = this.extendObject(defaultData, data, formData);
        options = this.extendObject(defaultOptions, options);

        if (data.productToAddKey) {
            // keep only one product by id
            for (let key in data.productsToAdd) {
                if (data.productsToAdd.hasOwnProperty(key)) {
                    let product = data.productsToAdd[key];

                    if (`${product.step_id}-${product.product_id}` !== data.productToAddKey) {
                        delete data.productsToAdd[key];
                    } else {
                        data.productsToAddChecked = {[product.step_id]: [product.product_id]};
                    }
                }
            }
        } else {
            delete data.productToAddKey;
        }

        this.triggerEvent('submit.wcpw', {instance, data});

        // has some errors
        if (this.hasError) {
            this.triggerEvent('submitError.wcpw', {instance, data});

            return Promise.resolve();
        }

        // send ajax
        return this.ajaxRequest(data, options);
    }

    /**
     * Route to the required navigation event
     * @param {Object} args - object of arguments
     * @param {Object} options - object of method options
     * @returns {Object} nav function
     */
    navRouter(args = {}, options = {}) {
        const action = args.action;

        // action will be added by a method
        delete args.action;

        switch (action) {
            case 'skip-step':
                return this.skipStep(args, options);

            case 'skip-all':
                return this.skipAll(args, options);

            case 'submit-and-skip-all':
                return this.submitAndSkipAll(args, options);

            case 'submit':
                return this.submit(args, options);

            case 'add-to-main-cart':
                return this.addToMainCart(args, options);

            case 'add-to-main-cart-repeat':
                return this.addToMainCartRepeat(args, options);

            case 'reset':
                return this.reset(args, options);

            case 'none':
                return null;

            case 'get-step':
            default:
                return this.getStep(args, options);
        }
    }

    /**
     * Skip form to the next step without adding products to the wizard cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    skipStep(data = {}, options = {}) {
        data = this.extendObject({action: this.options.ajaxActions.skipStep}, data);

        return this.ajaxRequest(data, options);
    }

    /**
     * Submit and skip form to the last step
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    submitAndSkipAll(data = {}, options = {}) {
        data = this.extendObject({action: this.options.ajaxActions.submitAndSkipAll}, data);

        return this.submit(data, options);
    }

    /**
     * Skip form to the last step without adding products to the wizard cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    skipAll(data = {}, options = {}) {
        data = this.extendObject({action: this.options.ajaxActions.skipAll}, data);

        return this.ajaxRequest(data, options);
    }

    /**
     * Get step content by the id
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    getStep(data = {}, options = {}) {
        data = this.extendObject({action: this.options.ajaxActions.getStep}, data);

        return this.ajaxRequest(data, options);
    }

    /**
     * Reset form to the initial state
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    reset(data = {}, options = {}) {
        data = this.extendObject({action: this.options.ajaxActions.reset}, data);

        return this.ajaxRequest(data, options);
    }
    // </editor-fold>

    // <editor-fold desc="Utils">
    /**
     * Parse any query data to an object
     * https://github.com/cobicarmel/jquery-serialize-object/
     * @param {Object} dataContainer - target
     * @param {Object} key - prop key
     * @param {Object} value - prop value
     * @returns {Object} recursive or null
     */
    parseObject(dataContainer, key, value) {
        const isArrayKey = (/^[^\[\]]+\[]/).test(key);
        const isObjectKey = (/^[^\[\]]+\[[^\[\]]+]/).test(key);
        const keyName = key.replace(/\[.*/, '');

        if (isArrayKey) {
            if (!dataContainer[keyName]) {
                dataContainer[keyName] = [];
            }
        } else {
            if (!isObjectKey) {
                if (dataContainer.push) {
                    dataContainer.push(value);
                } else {
                    dataContainer[keyName] = value;
                }

                return null;
            }

            if (!dataContainer[keyName]) {
                dataContainer[keyName] = {};
            }
        }

        const nextKeys = key.match(/\[[^\[\]]*]/g);

        nextKeys[0] = nextKeys[0].replace(/\[|]/g, '');

        return this.parseObject(dataContainer[keyName], nextKeys.join(''), value);
    }

    /**
     * Get FormData as a recursive object
     * https://github.com/cobicarmel/jquery-serialize-object/
     * @param {HTMLFormElement} form - DOM element
     * @returns {Object} form data object
     */
    serializeObject(form) {
        const formData = new FormData(form);
        const data = {};

        for (let pair of formData.entries()) {
            this.parseObject(data, pair[0], pair[1]);
        }

        return data;
    }

    /**
     * Get current URL search params
     * @param {String} search - GET string to parse
     * @returns {Object} URLSearchParams
     */
    getQueryArgs(search = this.options.windowNode.location.search) {
        if (typeof URLSearchParams === 'undefined') {
            return {};
        }

        return new URLSearchParams(search);
    }

    /**
     * Set URL request parameter value
     * @param {Object} args - key pair of params
     * @returns {this} self instance
     */
    setQueryArg(args) {
        if (!this.options.windowNode.history || !this.options.windowNode.history.pushState) {
            return this;
        }

        const queryArgs = this.getQueryArgs();

        if (!queryArgs.get) {
            return this;
        }

        for (let key in args) {
            if (args.hasOwnProperty(key)) {
                if (typeof args[key] === 'boolean' && !args[key]) {
                    queryArgs.delete(key);
                } else {
                    queryArgs.set(key, args[key]);
                }
            }
        }

        const path = this.options.windowNode.location.protocol + '//' + this.options.windowNode.location.host
            + this.options.windowNode.location.pathname + '?' + queryArgs.toString();

        this.options.windowNode.history.pushState({path}, '', path);

        return this;
    }

    /**
     * Parse query string to an object
     * @param {String} string - string to parse
     * @returns {Object} parsed output
     */
    queryStringToObject(string) {
        const output = {};

        if (!string) {
            return output;
        }

        /* eslint-disable */
        const data = JSON.parse('{"' + decodeURI(string)
            .replace(/"/g, '\\"')
            .replace(/&/g, '","')
            .replace(/=/g,'":"') + '"}');
        /* eslint-enable */

        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                this.parseObject(output, key, data[key]);
            }
        }

        return output;
    }

    /**
     * Send vibration signal
     * @param {Array} args - vibration pattern as duration, pause, duration,..
     * @returns {this} self instance
     */
    vibrate(args = [200]) {
        if ('vibrate' in this.options.windowNode.navigator) {
            this.options.windowNode.navigator.vibrate(args);
        } else if ('oVibrate' in this.options.windowNode.navigator) {
            this.options.windowNode.navigator.oVibrate(args);
        } else if ('mozVibrate' in this.options.windowNode.navigator) {
            this.options.windowNode.navigator.mozVibrate(args);
        } else if ('webkitVibrate' in this.options.windowNode.navigator) {
            this.options.windowNode.navigator.webkitVibrate(args);
        }

        return this;
    }

    /**
     * Set clipboard content
     * @param {String} text - string to add into clipboard
     * @returns {Boolean} function result
     */
    setClipboard(text) {
        if (this.options.windowNode.clipboardData && this.options.windowNode.clipboardData.setData) {
            this.options.windowNode.clipboardData.setData('Text', text);

            return true;
        } else if (this.options.documentNode.queryCommandSupported
            && this.options.documentNode.queryCommandSupported('copy')
        ) {
            const textarea = this.options.documentNode.createElement('textarea');

            textarea.textContent = text;
            textarea.style.position = 'fixed';
            this.options.documentNode.body.appendChild(textarea);
            textarea.select();

            try {
                return this.options.documentNode.execCommand('copy');
            } catch (ex) {
                return false;
            } finally {
                this.options.documentNode.body.removeChild(textarea);
            }
        }

        return false;
    }

    /**
     * Is element on the screen
     * @param {HTMLElement} element - element to check
     * @param {Boolean} strict - check element bottom position also
     * @returns {Boolean} function result
     */
    isScrolledIntoView(element, strict = false) {
        const rect = element.getBoundingClientRect();

        return !strict && rect.top >= 0 && rect.top <= this.options.windowNode.innerHeight
            || strict && rect.top >= 0 && rect.bottom <= this.options.windowNode.innerHeight;
    }

    /**
     * Scroll window screen to element
     * @param {HTMLElement} element - scroll to element
     * @param {Number} gap - top space gap
     * @param {String} behavior - animation behavior
     * @returns {this} self instance
     */
    scrollToElement(element, gap = 0, behavior = 'smooth') {
        const reduceMotion = this.options.windowNode.matchMedia('(prefers-reduced-motion: reduce)') === true
            || this.options.windowNode.matchMedia('(prefers-reduced-motion: reduce)').matches === true;

        this.options.windowNode.scrollTo({
            top: element.getBoundingClientRect().top - Number(gap),
            behavior: reduceMotion ? 'instant' : behavior
        });

        return this;
    }

    /**
     * Serialize any object
     * https://github.com/knowledgecode/jquery-param
     * @param {Object} input - any object to serialize
     * @returns {String} a serialized string
     */
    objectToQueryParam(input) {
        /* eslint-disable */
        const output = [];
        const add = function (key, value) {
            value = typeof value === 'function' ? value() : value;
            value = value === null ? '' : value === undefined ? '' : value;
            output[output.length] = encodeURIComponent(key) + '=' + encodeURIComponent(value);
        };

        const buildParams = function (prefix, obj) {
            let i, len, key;

            if (prefix) {
                if (Array.isArray(obj)) {
                    for (i = 0, len = obj.length; i < len; i++) {
                        buildParams(
                            prefix + '[' + (typeof obj[i] === 'object' && obj[i] ? i : '') + ']',
                            obj[i]
                        );
                    }
                } else if (Object.prototype.toString.call(obj) === '[object Object]') {
                    for (key in obj) {
                        buildParams(prefix + '[' + key + ']', obj[key]);
                    }
                } else {
                    add(prefix, obj);
                }
            } else if (Array.isArray(obj)) {
                for (i = 0, len = obj.length; i < len; i++) {
                    add(obj[i].name, obj[i].value);
                }
            } else {
                for (key in obj) {
                    buildParams(key, obj[key]);
                }
            }

            return output;
        };
        /* eslint-enable */

        return buildParams('', input).join('&');
    }

    /**
     * Extend object properties by other objects
     * @param {Object} args - object to extend
     * @returns {Object} new extended object
     */
    extendObject(...args) {
        return Object.assign({}, ...args);
    };
    // </editor-fold>
}

export default WCPW;
