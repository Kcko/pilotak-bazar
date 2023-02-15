require.config({
    shim: {
    },
    deps: [
    ],
    packages: [
        { name: 'nette-base-module', main: 'bootstrap' },
        { name: 'nette-back-module', main: 'bootstrap' },
        { name: 'nette-front-module', main: 'bootstrap-back' },
        { name: 'nette-catalogue-module', main: 'bootstrap-back' },
        { name: 'nette-product-module', main: 'bootstrap-back' },
    ],
    paths: {
        'nette-base-module': 'nette-base-module/assets/js',
        'nette-back-module': 'nette-back-module/assets/js',
        'nette-front-module': 'nette-front-module/assets/js',
        'nette-catalogue-module': 'nette-catalogue-module/assets/js',
        'nette-product-module': 'nette-product-module/assets/js',
        'jquery': 'jquery/dist/jquery.min',
        'presenters': '/admin/js/presenters'
    },
});

require([
    'nette-base-module', // nette ajax, form validation, history ajax implementation
    'nette-back-module',
    'nette-front-module',
    'nette-catalogue-module',
    'nette-product-module',
], function (init) {
    init.init();
});