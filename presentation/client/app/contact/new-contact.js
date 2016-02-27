(function () {
    //'use strict';

    angular
        .module ( 'app.newcontact' )
        .controller ( 'newContact', newContact );

    /* @ngInject */
    newContact.$inject = [ 'logger', 'dataservice' ];
    function newContact ( logger, dataservice ) {
        /*jshint validthis: true */
        var vm         = this;
        vm.contact     = {
            fname : null,
            lname : null,
            email : null,
            phone : null,
            id    : null
        };
        vm.title       = 'Agenda: New Contact';
        vm.dataservice = dataservice;
        vm.submitForm  = submitForm;

        activate ();

        function activate () {
            logger.info ( 'Activated Agenda add contact' );
        }

        function submitForm ( newContactForm ) {
            if ( newContactForm ) {
                if ( !dataservice.saveData ( vm.contact ) ) {
                    logger.error ( 'the new contact cannot be saved!' );
                } else {
                    logger.success ( 'contact saved successfully' );

                    // now we reset and reinitialize the form :)
                    vm.contact = null;
                    newContactForm.$setPristine ();
                    newContactForm.$setUntouched ();
                }
            } else {
                logger.warning ( 'The form is not completed!' );
            }
        }

    }
}) ();
