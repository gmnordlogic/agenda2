(function() {
//    'use strict';

    angular
        .module('app.dashboard')
        .controller('Dashboard', Dashboard);

    Dashboard.$inject = ['$q', 'dataservice', 'logger'];

    function Dashboard($q, dataservice, logger) {

        /*jshint validthis: true */
        var vm = this;

        vm.count = 0;
        vm.news = {
            title: 'Agenda News',
            description: 'AngularUI Agenda v2 is now in development!'
        };
        vm.counts = {
            title: 'Agenda Contacts',
            description: 'You have saved in local storage: '
        };
        vm.title = 'Dashboard';
        vm.getContactsCount = getContactsCount;

        activate();

        function activate() {
            logger.info('Activated Dashboard View');
            getContactsCount();
        }

        function getContactsCount(){
            return dataservice.getAgendaCount().then(function(data){
                vm.count = data[0].countContacts;
                return vm.count;
            });
        }
    }
})();
