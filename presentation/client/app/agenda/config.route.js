(function() {
    //'use strict';

    angular
        .module('app.agenda')
        .run(appRun);

    // appRun.$inject = ['routehelper']

    /* @ngInject */
    appRun.$inject = ['routehelper'];
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return [
            {
                url: '/agenda',
                config: {
                    templateUrl: 'app/agenda/agenda.html',
                    controller: 'Agenda',
                    controllerAs: 'vm',
                    title: 'agenda',
                    settings: {
                        nav: 2,
                        content: '<i class="fa fa-mobile-phone"></i> Agenda'
                    }
                }
            }
        ];
    }
})();
