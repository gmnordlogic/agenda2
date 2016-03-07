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
        vm.totalContacts = 0;
        vm.contactsPerPage = 10;
        vm.delete = deleteData;
        vm.pageChanged = pageChanged;
        vm.sort = sort;
        vm.sortKey = null;
        vm.reverse = false;

        activate();

        function pageChanged(newPage) {
            getPages(newPage);
        }

        function activate() {
            logger.info('Activated Agenda View');
            //getAgenda();
            getPages(1);
        }

        function getAgenda(){
            return dataservice.getAgendaList().then(function(data){
                vm.agenda = data;
                //console.log(vm.agenda);
                return vm.agenda;
            });
        }

        function getPages(page){
            //return dataservice.getAgendaPaged(page).then(function(data){
            return dataservice.getAgendaPagedFull(page, '', vm.sortKey, vm.reverse).then(function(data){
                vm.agenda = data.Items;
                vm.totalContacts = data.Count;
                //console.log(vm.agenda);
                return vm.agenda;
            });
        }

        function deleteData(item){
            var index = vm.agenda.indexOf(item);
            var name = vm.agenda[index ].fname + ' ' + vm.agenda[index ].lname;
            var id = vm.agenda[index ].id;
            vm.agenda.splice(index, 1);
            //console.log(id);
            dataservice.deleteData(id);
            logger.info('Contact deleted: ' + name);
        }

        function sort(keyname, page){
            vm.sortKey = keyname;
            vm.reverse = !vm.reverse;
            pageChanged(page);
        }
    }
})();
