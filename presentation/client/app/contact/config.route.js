(function() {
    //'use strict';

    angular
        .module('app.newcontact')
        .run(appRun);

    /* @ngInject */
    appRun.$inject = ['routehelper'];
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return [
            {
                url: '/new-contact',
                config: {
                    templateUrl: 'app/contact/new-contact.html',
                    controller: 'newContact',
                    controllerAs: 'vm',
                    title: 'New Contact',
                    settings: {
                        nav: 3,
                        content: '<i class="fa fa-user-plus"></i> New Contact'
                    }
                }
            },
            {
                url: '/edit-contact/:id',
                config: {
                    templateUrl: 'app/contact/edit-contact.html',
                    controller: 'editContact',
                    controllerAs: 'vm',
                    title: 'Edit Contact',
                }
            }
        ];
    }
})();
