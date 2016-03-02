(function () {
    //'use strict';

    angular
        .module ( 'app.core' )
        .factory ( 'dataservice', dataservice );

    /* @ngInject */
    dataservice.$inject = [ "$http", "$location", "$q", "exception", "logger" ];
    function dataservice ( $http, $location, $q, exception, logger ) {
        var isPrimed = false;
        var primePromise;

        var GET_AGENDA_URL       = '/api/all';
        var GET_AGENDA_PAGED_URL = '/api/page/';
        var CONTACT_URL          = '/api/contact';
        var GET_AGENDA_COUNT_URL = '/api/count';

        var service = {
            getAgendaList  : getAgendaList,
            getAgendaCount : getAgendaCount,
            ready          : ready,
            saveData       : saveData,
            getData        : getData,
            deleteData     : deleteData,
            updateData     : updateData,
            getAgendaPaged : getAgendaPaged,
            ag             : [],
            responseData   : {},
            errors         : [],
            hasError       : false,
            isDataLoaded   : false,
            count          : [],
            person         : []
        };

        return service;

        function getAgendaList () {
            return $http.get ( GET_AGENDA_URL ).then ( successCallback ).catch ( errorCallback );

            function successCallback ( response ) {
                service.hasError = false;
                //console.log ( response );
                if ( response.data.length == 0 ) {
                    logger.warning ( 'Agenda is empty. Please try to add a new contact.' );
                }
                return response.data;
            }

            function errorCallback (error) {
                service.hasError = true;
                logger.warning ( 'Problem with connection. No response from API! ' + error.data );
                return false;
            }
        }

        function getAgendaPaged (page) {
            return $http.get ( GET_AGENDA_PAGED_URL + page).then ( successCallback ).catch ( errorCallback );

            function successCallback ( response ) {
                service.hasError = false;
                //console.log ( response );
                if ( response.data.Count == 0 ) {
                    logger.warning ( 'Agenda is empty. Please try to add a new contact.' );
                }
                return response.data;
            }

            function errorCallback (error) {
                service.hasError = true;
                logger.warning ( 'Problem with connection. No response from API! ' + error.data );
                return false;
            }
        }

        function getAgendaCount () {
            return $http.get ( GET_AGENDA_COUNT_URL ).then ( successCallback ).catch ( errorCallback );

            function successCallback ( response ) {
                service.hasError = false;
                //console.log( response);
                return response.data;
            }

            function errorCallback ( error ) {
                service.hasError = true;
                logger.warning ( 'Problem with connection. No response from API! ' + error.data );
                return false;
            }
        }

        function saveData ( person ) {
            return $http.post ( CONTACT_URL, person ).then ( successCallback ).catch ( errorCallback );

            function successCallback ( response ) {
                service.hasError = true;
                return true;
            }

            function errorCallback (error) {
                service.hasError = true;
                logger.error ( 'Error: no save no game, connection problem! ' + error.data);
                return false;
            }
        }

        function getData ( id ) {
            return $http.get ( CONTACT_URL + '/' + id ).then ( successCallback ).catch( errorCallback );

            function successCallback ( response ) {
                service.hasError = false;
                //console.log(response);
                return response.data;
            }

            function errorCallback (error) {
                service.hasError = true;
                logger.error ( 'Error: cannot read a person from database. Connection problem! ' + error.data );
            }
        }

        function deleteData ( id ) {
             return $http.delete ( CONTACT_URL + '/' + id ).then ( successCallback ).catch (errorCallback );

             function successCallback ( response ) {
                service.hasError = false;
                 return true;
             }

             function errorCallback (error) {
                service.hasError = true;
                logger.error ( 'Error: couldnt delete the person. Connection problem! ' + error.data );
             }
        }

        function updateData ( person ) {
            return $http.put ( CONTACT_URL + '/' + person.id, person ).then ( successCallback ).catch ( errorCallback );

            function successCallback ( response ) {
                service.hasError = true;
                return true;
            }

            function errorCallback (error) {
                service.hasError = true;
                logger.error ( 'Error: no update, connection problem! ' + error.data);
                return false;
            }
        }

        function prime () {
            // This function can only be called once.
            if ( primePromise ) {
                return primePromise;
            }

            primePromise = $q.when ( true ).then ( success );
            return primePromise;

            function success () {
                isPrimed = true;
                logger.info ( 'Primed data' );
            }
        }

        function ready ( nextPromises ) {
            var readyPromise = primePromise || prime ();

            return readyPromise
                .then ( function () {
                    return $q.all ( nextPromises );
                } )
                .catch ( exception.catcher ( '"ready" function failed' ) );
        }
    }
}) ();
