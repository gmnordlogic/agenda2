(function() {
//    'use strict';

    var core = angular.module('app.core');

    core.config(toastrConfig);

    /* @ngInject */
    toastrConfig.$inject = ['toastr'];

    function toastrConfig(toastr) {
        toastr.options.timeOut = 4000;
        toastr.options.positionClass = 'toast-bottom-right';
        toastr.options.progressBar = true;
    }

    var configs = {
        appErrorPrefix: '[NG-Agenda2 Error] ', //Configure the exceptionHandler decorator
        appTitle: 'AngularUI Agenda 2',
        version: '2.0.0'
    };

    core.value('config', configs);

    core.config(configure);

    /* @ngInject */
    configure.$inject = ['$logProvider', '$routeProvider', 'routehelperConfigProvider', 'exceptionHandlerProvider'];
    function configure ($logProvider, $routeProvider, routehelperConfigProvider, exceptionHandlerProvider) {
        // turn debugging off/on (no info or warn)
        if ($logProvider.debugEnabled) {
            $logProvider.debugEnabled(true);
        }

        /*ready.$inject = ['dataservice'];
        function ready(dataservice) {
            return dataservice.ready();
        }*/

        // Configure the common route provider
        routehelperConfigProvider.config.$routeProvider = $routeProvider;
        routehelperConfigProvider.config.docTitle = 'NG-Agenda2: ';
        var resolveAlways = { /* @ngInject */
            /*ready: function(dataservice) {
                return dataservice.ready();
            }*/
            ready: ['dataservice', function (dataservice) {
                return dataservice.ready();
            }]
//            ready: ready()
        };
        routehelperConfigProvider.config.resolveAlways = resolveAlways;

        // Configure the common exception handler
        exceptionHandlerProvider.configure(configs.appErrorPrefix);
    }
})();
