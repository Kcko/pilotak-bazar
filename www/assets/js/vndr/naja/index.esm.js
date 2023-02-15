/*
 * Naja.js
 * 2.5.0
 *
 * by Jiří Pudil <https://jiripudil.cz>
 */
// ready
const onDomReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    }
    else {
        callback();
    }
};
// assert
class AssertionError extends Error {
}
const assert = (condition, description) => {
    if (!condition) {
        const message = `Assertion failed${description !== undefined ? `: ${description}` : '.'}`;
        throw new AssertionError(message);
    }
};

class UIHandler extends EventTarget {
    constructor(naja) {
        super();
        this.naja = naja;
        this.selector = '.ajax';
        this.allowedOrigins = [window.location.origin];
        this.handler = this.handleUI.bind(this);
        naja.addEventListener('init', this.initialize.bind(this));
    }
    initialize() {
        onDomReady(() => this.bindUI(window.document.body));
        this.naja.snippetHandler.addEventListener('afterUpdate', (event) => {
            const { snippet } = event.detail;
            this.bindUI(snippet);
        });
    }
    bindUI(element) {
        const selectors = [
            `a${this.selector}`,
            `input[type="submit"]${this.selector}`,
            `input[type="image"]${this.selector}`,
            `button[type="submit"]${this.selector}`,
            `form${this.selector} input[type="submit"]`,
            `form${this.selector} input[type="image"]`,
            `form${this.selector} button[type="submit"]`,
        ].join(', ');
        const bindElement = (element) => {
            element.removeEventListener('click', this.handler);
            element.addEventListener('click', this.handler);
        };
        const elements = element.querySelectorAll(selectors);
        for (let i = 0; i < elements.length; i++) {
            bindElement(elements.item(i));
        }
        if (element.matches(selectors)) {
            bindElement(element);
        }
        const bindForm = (form) => {
            form.removeEventListener('submit', this.handler);
            form.addEventListener('submit', this.handler);
        };
        if (element.matches(`form${this.selector}`)) {
            bindForm(element);
        }
        const forms = element.querySelectorAll(`form${this.selector}`);
        for (let i = 0; i < forms.length; i++) {
            bindForm(forms.item(i));
        }
    }
    handleUI(event) {
        const mouseEvent = event;
        if (mouseEvent.altKey || mouseEvent.ctrlKey || mouseEvent.shiftKey || mouseEvent.metaKey || mouseEvent.button) {
            return;
        }
        const element = event.currentTarget;
        const options = this.naja.prepareOptions();
        const ignoreErrors = () => {
            // don't reject the promise in case of an error as developers have no way of handling the rejection
            // in this situation; errors should be handled in `naja.addEventListener('error', errorHandler)`
        };
        if (event.type === 'submit') {
            this.submitForm(element, options, event).catch(ignoreErrors);
        }
        else if (event.type === 'click') {
            this.clickElement(element, options, mouseEvent).catch(ignoreErrors);
        }
    }
    async clickElement(element, options = {}, event) {
        let method = 'GET', url = '', data;
        if (!this.dispatchEvent(new CustomEvent('interaction', { cancelable: true, detail: { element, originalEvent: event, options } }))) {
            event?.preventDefault();
            return {};
        }
        if (element.tagName === 'A') {
            assert(element instanceof HTMLAnchorElement);
            method = 'GET';
            url = element.href;
            data = null;
        }
        else if (element.tagName === 'INPUT' || element.tagName === 'BUTTON') {
            assert(element instanceof HTMLInputElement || element instanceof HTMLButtonElement);
            const { form } = element;
            // eslint-disable-next-line no-nested-ternary,no-extra-parens
            method = element.getAttribute('formmethod')?.toUpperCase() ?? form?.getAttribute('method')?.toUpperCase() ?? 'GET';
            url = element.getAttribute('formaction') ?? form?.getAttribute('action') ?? window.location.pathname + window.location.search;
            data = new FormData(form ?? undefined);
            if (element.type === 'submit' && element.name !== '') {
                data.append(element.name, element.value || '');
            }
            else if (element.type === 'image') {
                const coords = element.getBoundingClientRect();
                const prefix = element.name !== '' ? `${element.name}.` : '';
                data.append(`${prefix}x`, Math.max(0, Math.floor(event !== undefined ? event.pageX - coords.left : 0)));
                data.append(`${prefix}y`, Math.max(0, Math.floor(event !== undefined ? event.pageY - coords.top : 0)));
            }
        }
        if (!this.isUrlAllowed(url)) {
            throw new Error(`Cannot dispatch async request, URL is not allowed: ${url}`);
        }
        event?.preventDefault();
        return this.naja.makeRequest(method, url, data, options);
    }
    async submitForm(form, options = {}, event) {
        if (!this.dispatchEvent(new CustomEvent('interaction', { cancelable: true, detail: { element: form, originalEvent: event, options } }))) {
            event?.preventDefault();
            return {};
        }
        const method = form.getAttribute('method')?.toUpperCase() ?? 'GET';
        const url = form.getAttribute('action') ?? window.location.pathname + window.location.search;
        const data = new FormData(form);
        if (!this.isUrlAllowed(url)) {
            throw new Error(`Cannot dispatch async request, URL is not allowed: ${url}`);
        }
        event?.preventDefault();
        return this.naja.makeRequest(method, url, data, options);
    }
    isUrlAllowed(url) {
        const urlObject = new URL(url, location.href);
        // ignore non-URL URIs (javascript:, data:, mailto:, ...)
        if (urlObject.origin === 'null') {
            return false;
        }
        return this.allowedOrigins.includes(urlObject.origin);
    }
}

class FormsHandler {
    constructor(naja) {
        this.naja = naja;
        naja.addEventListener('init', this.initialize.bind(this));
        naja.uiHandler.addEventListener('interaction', this.processForm.bind(this));
    }
    initialize() {
        onDomReady(() => this.initForms(window.document.body));
        this.naja.snippetHandler.addEventListener('afterUpdate', (event) => {
            const { snippet } = event.detail;
            this.initForms(snippet);
        });
    }
    initForms(element) {
        const netteForms = this.netteForms || window.Nette;
        if (netteForms) {
            if (element.tagName === 'form') {
                netteForms.initForm(element);
            }
            const forms = element.querySelectorAll('form');
            for (let i = 0; i < forms.length; i++) {
                netteForms.initForm(forms.item(i));
            }
        }
    }
    processForm(event) {
        const { element, originalEvent } = event.detail;
        const inputElement = element;
        if (inputElement.form !== undefined && inputElement.form !== null) {
            inputElement.form['nette-submittedBy'] = element;
        }
        const netteForms = this.netteForms || window.Nette;
        if ((element.tagName === 'FORM' || element.form) && netteForms && !netteForms.validateForm(element)) {
            if (originalEvent) {
                originalEvent.stopImmediatePropagation();
                originalEvent.preventDefault();
            }
            event.preventDefault();
        }
    }
}

class RedirectHandler extends EventTarget {
    constructor(naja) {
        super();
        this.naja = naja;
        naja.uiHandler.addEventListener('interaction', (event) => {
            const { element, options } = event.detail;
            if (!element) {
                return;
            }
            if (element.hasAttribute('data-naja-force-redirect') || element.form?.hasAttribute('data-naja-force-redirect')) {
                const value = element.getAttribute('data-naja-force-redirect') ?? element.form?.getAttribute('data-naja-force-redirect');
                options.forceRedirect = value !== 'off';
            }
        });
        naja.addEventListener('success', (event) => {
            const { payload, options } = event.detail;
            if (payload.redirect) {
                this.makeRedirect(payload.redirect, options.forceRedirect ?? false, options);
                event.stopImmediatePropagation();
            }
        });
        this.locationAdapter = {
            assign: (url) => window.location.assign(url),
        };
    }
    makeRedirect(url, force, options = {}) {
        if (url instanceof URL) {
            url = url.href;
        }
        let isHardRedirect = force || !this.naja.uiHandler.isUrlAllowed(url);
        const canRedirect = this.dispatchEvent(new CustomEvent('redirect', {
            cancelable: true,
            detail: {
                url,
                isHardRedirect,
                setHardRedirect(value) {
                    isHardRedirect = !!value;
                },
                options,
            },
        }));
        if (!canRedirect) {
            return;
        }
        if (isHardRedirect) {
            this.locationAdapter.assign(url);
        }
        else {
            this.naja.makeRequest('GET', url, null, options);
        }
    }
}

class SnippetHandler extends EventTarget {
    constructor(naja) {
        super();
        this.naja = naja;
        this.op = {
            replace: (snippet, content) => {
                snippet.innerHTML = content;
            },
            prepend: (snippet, content) => snippet.insertAdjacentHTML('afterbegin', content),
            append: (snippet, content) => snippet.insertAdjacentHTML('beforeend', content),
        };
        naja.addEventListener('success', (event) => {
            const { options, payload } = event.detail;
            if (payload.snippets) {
                this.updateSnippets(payload.snippets, false, options);
            }
        });
    }
    static findSnippets(predicate) {
        const result = {};
        const snippets = window.document.querySelectorAll('[id^="snippet-"]');
        for (let i = 0; i < snippets.length; i++) {
            const snippet = snippets.item(i);
            if (predicate?.(snippet) ?? true) {
                result[snippet.id] = snippet.innerHTML;
            }
        }
        return result;
    }
    updateSnippets(snippets, fromCache = false, options = {}) {
        Object.keys(snippets).forEach((id) => {
            const snippet = document.getElementById(id);
            if (snippet) {
                this.updateSnippet(snippet, snippets[id], fromCache, options);
            }
        });
    }
    updateSnippet(snippet, content, fromCache, options) {
        let operation = this.op.replace;
        if ((snippet.hasAttribute('data-naja-snippet-prepend') || snippet.hasAttribute('data-ajax-prepend')) && !fromCache) {
            operation = this.op.prepend;
        }
        else if ((snippet.hasAttribute('data-naja-snippet-append') || snippet.hasAttribute('data-ajax-append')) && !fromCache) {
            operation = this.op.append;
        }
        const canUpdate = this.dispatchEvent(new CustomEvent('beforeUpdate', {
            cancelable: true,
            detail: {
                snippet,
                content,
                fromCache,
                operation,
                changeOperation(value) {
                    operation = value;
                },
                options,
            },
        }));
        if (!canUpdate) {
            return;
        }
        if (snippet.tagName.toLowerCase() === 'title') {
            document.title = content;
        }
        else {
            operation(snippet, content);
        }
        this.dispatchEvent(new CustomEvent('afterUpdate', {
            cancelable: true,
            detail: {
                snippet,
                content,
                fromCache,
                operation,
                options,
            },
        }));
    }
}

class HistoryHandler extends EventTarget {
    constructor(naja) {
        super();
        this.naja = naja;
        this.initialized = false;
        this.popStateHandler = this.handlePopState.bind(this);
        naja.addEventListener('init', this.initialize.bind(this));
        naja.addEventListener('before', this.saveUrl.bind(this));
        naja.addEventListener('before', this.replaceInitialState.bind(this));
        naja.addEventListener('success', this.pushNewState.bind(this));
        naja.uiHandler.addEventListener('interaction', this.configureMode.bind(this));
        this.historyAdapter = {
            replaceState: (state, title, url) => window.history.replaceState(state, title, url),
            pushState: (state, title, url) => window.history.pushState(state, title, url),
        };
    }
    set uiCache(value) {
        console.warn('Naja: HistoryHandler.uiCache is deprecated, use options.snippetCache instead.');
        this.naja.defaultOptions.snippetCache = value;
    }
    handlePopState(event) {
        const { state } = event;
        if (state?.source !== 'naja') {
            return;
        }
        const options = this.naja.prepareOptions();
        this.dispatchEvent(new CustomEvent('restoreState', { detail: { state, options } }));
    }
    initialize() {
        window.addEventListener('popstate', this.popStateHandler);
    }
    saveUrl(event) {
        const { url, options } = event.detail;
        options.href ??= url;
    }
    replaceInitialState(event) {
        const { options } = event.detail;
        const mode = HistoryHandler.normalizeMode(options.history);
        if (mode !== false && !this.initialized) {
            onDomReady(() => this.historyAdapter.replaceState(this.buildState(window.location.href, options), window.document.title, window.location.href));
            this.initialized = true;
        }
    }
    configureMode(event) {
        const { element, options } = event.detail;
        // propagate mode to options
        if (!element) {
            return;
        }
        if (element.hasAttribute('data-naja-history') || element.form?.hasAttribute('data-naja-history')) {
            const value = element.getAttribute('data-naja-history') ?? element.form?.getAttribute('data-naja-history');
            options.history = HistoryHandler.normalizeMode(value);
        }
    }
    static normalizeMode(mode) {
        if (mode === 'off' || mode === false) {
            return false;
        }
        else if (mode === 'replace') {
            return 'replace';
        }
        return true;
    }
    pushNewState(event) {
        const { payload, options } = event.detail;
        const mode = HistoryHandler.normalizeMode(options.history);
        if (mode === false) {
            return;
        }
        if (payload.postGet && payload.url) {
            options.href = payload.url;
        }
        const method = mode === 'replace' ? 'replaceState' : 'pushState';
        this.historyAdapter[method](this.buildState(options.href, options), window.document.title, options.href);
    }
    buildState(href, options) {
        const state = { source: 'naja', href };
        this.dispatchEvent(new CustomEvent('buildState', { detail: { state, options } }));
        return state;
    }
}

class SnippetCache extends EventTarget {
    constructor(naja) {
        super();
        this.naja = naja;
        this.storages = {
            off: new OffCacheStorage(naja),
            history: new HistoryCacheStorage(),
            session: new SessionCacheStorage(),
        };
        naja.uiHandler.addEventListener('interaction', this.configureCache.bind(this));
        naja.historyHandler.addEventListener('buildState', this.buildHistoryState.bind(this));
        naja.historyHandler.addEventListener('restoreState', this.restoreHistoryState.bind(this));
    }
    resolveStorage(option) {
        let storageType;
        if (option === true || option === undefined) {
            storageType = 'history';
        }
        else if (option === false) {
            storageType = 'off';
        }
        else {
            storageType = option;
        }
        return this.storages[storageType];
    }
    configureCache(event) {
        const { element, options } = event.detail;
        if (!element) {
            return;
        }
        if (element.hasAttribute('data-naja-snippet-cache') || element.form?.hasAttribute('data-naja-snippet-cache')
            || element.hasAttribute('data-naja-history-cache') || element.form?.hasAttribute('data-naja-history-cache')) {
            const value = element.getAttribute('data-naja-snippet-cache')
                ?? element.form?.getAttribute('data-naja-snippet-cache')
                ?? element.getAttribute('data-naja-history-cache')
                ?? element.form?.getAttribute('data-naja-history-cache');
            options.snippetCache = value;
        }
    }
    buildHistoryState(event) {
        const { state, options } = event.detail;
        if ('historyUiCache' in options) {
            console.warn('Naja: options.historyUiCache is deprecated, use options.snippetCache instead.');
            options.snippetCache = options.historyUiCache;
        }
        const snippets = SnippetHandler.findSnippets((snippet) => !snippet.hasAttribute('data-naja-history-nocache')
            && !snippet.hasAttribute('data-history-nocache')
            && (!snippet.hasAttribute('data-naja-snippet-cache')
                || snippet.getAttribute('data-naja-snippet-cache') !== 'off'));
        if (!this.dispatchEvent(new CustomEvent('store', { cancelable: true, detail: { snippets, state, options } }))) {
            return;
        }
        const storage = this.resolveStorage(options.snippetCache);
        state.snippets = {
            storage: storage.type,
            key: storage.store(snippets),
        };
    }
    restoreHistoryState(event) {
        const { state, options } = event.detail;
        if (state.snippets === undefined) {
            return;
        }
        options.snippetCache = state.snippets.storage;
        if (!this.dispatchEvent(new CustomEvent('fetch', { cancelable: true, detail: { state, options } }))) {
            return;
        }
        const storage = this.resolveStorage(options.snippetCache);
        const snippets = storage.fetch(state.snippets.key, state, options);
        if (snippets === null) {
            return;
        }
        if (!this.dispatchEvent(new CustomEvent('restore', { cancelable: true, detail: { snippets, state, options } }))) {
            return;
        }
        this.naja.snippetHandler.updateSnippets(snippets, true, options);
        this.naja.scriptLoader.loadScripts(snippets);
    }
}
class OffCacheStorage {
    constructor(naja) {
        this.naja = naja;
        this.type = 'off';
    } // eslint-disable-line no-empty-function
    store() {
        return null;
    }
    fetch(key, state, options) {
        this.naja.makeRequest('GET', state.href, null, {
            ...options,
            history: false,
            snippetCache: false,
        });
        return null;
    }
}
class HistoryCacheStorage {
    constructor() {
        this.type = 'history';
    }
    store(data) {
        return data;
    }
    fetch(key) {
        return key;
    }
}
class SessionCacheStorage {
    constructor() {
        this.type = 'session';
    }
    store(data) {
        const key = Math.random().toString(36).substring(2, 8);
        window.sessionStorage.setItem(key, JSON.stringify(data));
        return key;
    }
    fetch(key) {
        const data = window.sessionStorage.getItem(key);
        if (data === null) {
            return null;
        }
        return JSON.parse(data);
    }
}

class ScriptLoader {
    constructor(naja) {
        this.loadedScripts = new Set();
        naja.addEventListener('init', () => {
            onDomReady(() => {
                document.querySelectorAll('script[data-naja-script-id]').forEach((script) => {
                    const scriptId = script.getAttribute('data-naja-script-id');
                    if (scriptId !== null && scriptId !== '') {
                        this.loadedScripts.add(scriptId);
                    }
                });
            });
            naja.addEventListener('success', (event) => {
                const { payload } = event.detail;
                if (payload.snippets) {
                    this.loadScripts(payload.snippets);
                }
            });
        });
    }
    loadScripts(snippets) {
        Object.keys(snippets).forEach((id) => {
            const content = snippets[id];
            if (!/<script/i.test(content)) {
                return;
            }
            const el = window.document.createElement('div');
            el.innerHTML = content;
            const scripts = el.querySelectorAll('script');
            for (let i = 0; i < scripts.length; i++) {
                const script = scripts.item(i);
                const scriptId = script.getAttribute('data-naja-script-id');
                if (scriptId !== null && scriptId !== '' && this.loadedScripts.has(scriptId)) {
                    continue;
                }
                const scriptEl = window.document.createElement('script');
                scriptEl.innerHTML = script.innerHTML;
                if (script.hasAttributes()) {
                    const attrs = script.attributes;
                    for (let j = 0; j < attrs.length; j++) {
                        const attrName = attrs[j].name;
                        scriptEl.setAttribute(attrName, attrs[j].value);
                    }
                }
                window.document.head.appendChild(scriptEl)
                    .parentNode.removeChild(scriptEl);
                if (scriptId !== null && scriptId !== '') {
                    this.loadedScripts.add(scriptId);
                }
            }
        });
    }
}

class Naja extends EventTarget {
    constructor(uiHandler, redirectHandler, snippetHandler, formsHandler, historyHandler, snippetCache, scriptLoader) {
        super();
        this.VERSION = 2;
        this.initialized = false;
        this.extensions = [];
        this.defaultOptions = {};
        this.uiHandler = new (uiHandler ?? UIHandler)(this);
        this.redirectHandler = new (redirectHandler ?? RedirectHandler)(this);
        this.snippetHandler = new (snippetHandler ?? SnippetHandler)(this);
        this.formsHandler = new (formsHandler ?? FormsHandler)(this);
        this.historyHandler = new (historyHandler ?? HistoryHandler)(this);
        this.snippetCache = new (snippetCache ?? SnippetCache)(this);
        this.scriptLoader = new (scriptLoader ?? ScriptLoader)(this);
    }
    registerExtension(extension) {
        if (this.initialized) {
            extension.initialize(this);
        }
        this.extensions.push(extension);
    }
    initialize(defaultOptions = {}) {
        if (this.initialized) {
            throw new Error('Cannot initialize Naja, it is already initialized.');
        }
        this.defaultOptions = this.prepareOptions(defaultOptions);
        this.extensions.forEach((extension) => extension.initialize(this));
        this.dispatchEvent(new CustomEvent('init', { detail: { defaultOptions: this.defaultOptions } }));
        this.initialized = true;
    }
    prepareOptions(options) {
        return {
            ...this.defaultOptions,
            ...options,
            fetch: {
                ...this.defaultOptions.fetch,
                ...options?.fetch,
            },
        };
    }
    async makeRequest(method, url, data = null, options = {}) {
        // normalize url to instanceof URL
        if (typeof url === 'string') {
            url = new URL(url, location.href);
        }
        options = this.prepareOptions(options);
        const headers = new Headers(options.fetch.headers || {});
        const body = this.transformData(url, method, data);
        const abortController = new AbortController();
        const request = new Request(url.toString(), {
            credentials: 'same-origin',
            ...options.fetch,
            method,
            headers,
            body,
            signal: abortController.signal,
        });
        // impersonate XHR so that Nette can detect isAjax()
        request.headers.set('X-Requested-With', 'XMLHttpRequest');
        // hint the server that Naja expects response to be JSON
        request.headers.set('Accept', 'application/json');
        if (!this.dispatchEvent(new CustomEvent('before', { cancelable: true, detail: { request, method, url: url.toString(), data, options } }))) {
            return {};
        }
        const promise = window.fetch(request);
        this.dispatchEvent(new CustomEvent('start', { detail: { request, promise, abortController, options } }));
        let response, payload;
        try {
            response = await promise;
            if (!response.ok) {
                throw new HttpError(response);
            }
            payload = await response.json();
        }
        catch (error) {
            if (error.name === 'AbortError') {
                this.dispatchEvent(new CustomEvent('abort', { detail: { request, error, options } }));
                this.dispatchEvent(new CustomEvent('complete', { detail: { request, response, payload: undefined, error, options } }));
                return {};
            }
            this.dispatchEvent(new CustomEvent('error', { detail: { request, response, error, options } }));
            this.dispatchEvent(new CustomEvent('complete', { detail: { request, response, payload: undefined, error, options } }));
            throw error;
        }
        this.dispatchEvent(new CustomEvent('payload', { detail: { request, response, payload, options } }));
        this.dispatchEvent(new CustomEvent('success', { detail: { request, response, payload, options } }));
        this.dispatchEvent(new CustomEvent('complete', { detail: { request, response, payload, error: undefined, options } }));
        return payload;
    }
    appendToQueryString(searchParams, key, value) {
        if (value === null || value === undefined) {
            return;
        }
        if (Array.isArray(value) || Object.getPrototypeOf(value) === Object.prototype) {
            for (const [subkey, subvalue] of Object.entries(value)) {
                this.appendToQueryString(searchParams, `${key}[${subkey}]`, subvalue);
            }
        }
        else {
            searchParams.append(key, String(value));
        }
    }
    transformData(url, method, data) {
        const isGet = ['GET', 'HEAD'].includes(method.toUpperCase());
        // sending a form via GET -> serialize FormData into URL and return empty request body
        if (isGet && data instanceof FormData) {
            for (const [key, value] of data) {
                if (value !== null && value !== undefined) {
                    url.searchParams.append(key, String(value));
                }
            }
            return null;
        }
        // sending a POJO -> serialize it recursively into URLSearchParams
        const isDataPojo = data !== null && Object.getPrototypeOf(data) === Object.prototype;
        if (isDataPojo || Array.isArray(data)) {
            // for GET requests, append values to URL and return empty request body
            // otherwise build `new URLSearchParams()` to act as the request body
            const transformedData = isGet ? url.searchParams : new URLSearchParams();
            for (const [key, value] of Object.entries(data)) {
                this.appendToQueryString(transformedData, key, value);
            }
            return isGet
                ? null
                : transformedData;
        }
        return data;
    }
}
class HttpError extends Error {
    constructor(response) {
        const message = `HTTP ${response.status}: ${response.statusText}`;
        super(message);
        this.name = this.constructor.name;
        this.stack = new Error(message).stack;
        this.response = response;
    }
}

class AbortExtension {
    constructor() {
        this.abortControllers = new Set();
    }
    initialize(naja) {
        naja.uiHandler.addEventListener('interaction', this.checkAbortable.bind(this));
        naja.addEventListener('init', this.onInitialize.bind(this));
        naja.addEventListener('start', this.saveAbortController.bind(this));
        naja.addEventListener('complete', this.removeAbortController.bind(this));
    }
    onInitialize() {
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !(event.ctrlKey || event.shiftKey || event.altKey || event.metaKey)) {
                for (const controller of this.abortControllers) {
                    controller.abort();
                }
                this.abortControllers.clear();
            }
        });
    }
    checkAbortable(event) {
        const { element, options } = event.detail;
        if (element.hasAttribute('data-naja-abort') || element.form?.hasAttribute('data-naja-abort')) {
            options.abort = (element.getAttribute('data-naja-abort') ?? element.form?.getAttribute('data-naja-abort')) !== 'off';
        }
    }
    saveAbortController(event) {
        const { abortController, options } = event.detail;
        if (options.abort !== false) {
            this.abortControllers.add(abortController);
            options.clearAbortExtension = () => this.abortControllers.delete(abortController);
        }
    }
    removeAbortController(event) {
        const { options } = event.detail;
        if (options.abort !== false && !!options.clearAbortExtension) {
            options.clearAbortExtension();
        }
    }
}

class UniqueExtension {
    constructor() {
        this.abortControllers = new Map();
    }
    initialize(naja) {
        naja.uiHandler.addEventListener('interaction', this.checkUniqueness.bind(this));
        naja.addEventListener('start', this.abortPreviousRequest.bind(this));
        naja.addEventListener('complete', this.clearRequest.bind(this));
    }
    checkUniqueness(event) {
        const { element, options } = event.detail;
        const unique = element.getAttribute('data-naja-unique') ?? element.form?.getAttribute('data-naja-unique');
        options.unique = unique === 'off' ? false : unique ?? 'default';
    }
    abortPreviousRequest(event) {
        const { abortController, options } = event.detail;
        if (options.unique !== false) {
            this.abortControllers.get(options.unique ?? 'default')?.abort();
            this.abortControllers.set(options.unique ?? 'default', abortController);
        }
    }
    clearRequest(event) {
        const { request, options } = event.detail;
        if (!request.signal.aborted && options.unique !== false) {
            this.abortControllers.delete(options.unique ?? 'default');
        }
    }
}

const naja = new Naja();
naja.registerExtension(new AbortExtension());
naja.registerExtension(new UniqueExtension());

export { HttpError, Naja, naja as default };
//# sourceMappingURL=index.esm.js.map
