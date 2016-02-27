(function() {
    //'use strict';

    angular
        .module('app.agenda')
        .controller('Agenda', Agenda);

    /* @ngInject */
    Agenda.$inject = ["dataservice", "logger"];
    function Agenda(dataservice, logger) {
        /*jshint validthis: true */
        var vm = this;
        vm.agenda = [];
        vm.title = 'Agenda';
        vm.delete = deleteData;

        activate();
        getAgenda();

        function activate() {
            logger.info('Activated Agenda View');
        }

        function getAgenda() {
            vm.agenda = dataservice.getAgendaList();
            return vm.agenda;
        }

        function deleteData(item){
            var index = vm.agenda.indexOf(item);
            var name = vm.agenda[index ].fname + ' ' + vm.agenda[index ].lname;
            var id = vm.agenda[index ].id;
            vm.agenda.splice(index, 1);
            dataservice.deleteData(id);
            logger.info('Contact deleted: ' + name);
        }
    }
})();
