(function () {
    //'use strict';

    angular
        .module ( 'app.editcontact' )
        .controller ( 'editContact', editContact );

    /* @ngInject */
    editContact.$inject = [ 'logger', 'dataservice', '$routeParams', "$location" ];
    function editContact ( logger, dataservice, $routeParams, $location ) {
        /*jshint validthis: true */
        var vm         = this;
        vm.contact     = {
            fname : null,
            lname : null,
            email : null,
            phone : null,
            id    : null
        };
        vm.id = $routeParams.id;
        vm.title  = 'Agenda: Edit Contact';
        vm.dataservice = dataservice;
        vm.updateForm  = updateForm;

        activate ();

        function activate () {
            vm.contact = dataservice.getData(vm.id);
            logger.info ( 'Activated Agenda contact editing form' );
        }

        function updateForm ( editContactForm ) {
            if ( editContactForm ) {
                if (!dataservice.updateData(vm.contact)){
                    logger.error('update cannot be performed!');
                } else {
                    $location.path('/agenda');
                    logger.info('contact updated successfully');
                }
            } else {
                logger.warning ( 'The edit form is not completed!' );
            }
        }

    }
}) ();
