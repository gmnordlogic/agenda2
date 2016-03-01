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
            var deferred = $q.defer ();
            if ( !service.isDataLoaded || forceReload ) {
                $http.get ( GET_AGENDA_URL ).then ( successCallback, errorCallback );
            } else {
                deferred.resolve ( service.ag );
            }

            function successCallback ( response ) {
                angular.copy ( response.data, service.ag );
                service.hasError = false;
                if ( service.ag.length == 0 ) {
                    logger.warning ( 'Agenda is empty. Please try to add a new contact.' );
                }
                deferred.resolve ( service.ag );
            }

            function errorCallback () {
                service.hasError = true;
                logger.warning ( 'Problem with connection. No response from API!' );
                deferred.resolve ( [] );
            }

            return deferred.promise;
        }

        function getAgendaCount () {
            var deferred = $q.defer ();
            if ( !service.isDataLoaded || forceReload ) {
                $http.get ( GET_AGENDA_COUNT_URL ).then ( successCallback, errorCallback );
            } else {
                deferred.resolve ( service.count );
            }

            function successCallback ( response ) {
                angular.copy ( response.data, service.count );
                service.hasError = false;
                deferred.resolve ( service.count );
            }

            function errorCallback () {
                service.hasError = true;
                logger.warning ( 'Problem with connection. No response from API!' );
                deferred.resolve ( [] );
            }

            return deferred.promise;
        }

        function saveData ( person ) {
            var deferred = $q.defer ();
            $http.post ( POST_CONTACT_URL, person ).then ( successCallback, errorCallback );

            function successCallback ( response ) {
                if ( response.data.error ) {
                    service.hasError = true;
                    service.errors   = response.data.errors;
                    logger.error ( 'Error: ' + response.data.errors );
                    deferred.resolve ( service.hasError );
                } else {
                    service.hasError = false;
                    service.errors   = [];
                    deferred.resolve ( service.hasError );
                }
            }

            function errorCallback () {
                service.hasError = true;
                logger.error ( 'Error: no save no game, connection problem!' );
                deferred.resolve ( [] );
            }

            return deferred.promise;
        }

        function getData ( id ) {
            var deferred = $q.defer ();
            if ( !service.isDataLoaded || forceReload ) {
                $http.get ( GET_CONTACT_URL + '/' + id ).then ( successCallback, errorCallback );
            } else {
                deferred.resolve ( service.person );
            }

            function successCallback ( response ) {
                angular.copy ( response.data, service.person );
                service.hasError = false;
                deferred.resolve ( service.person );
            }

            function errorCallback () {
                service.hasError = true;
                logger.error ( 'Error: cannot read a person from database. Connection problem' );
                deferred.resolve ( [] );
            }

            return deferred.promise;
        }

        function deleteData ( id ) {
            var deferred = $q.defer ();
            if ( !service.isDataLoaded || forceReload ) {
                $http.delete ( CONTACT_URL + '/' + id ).then ( successCallback, errorCallback );
            } else {
                deferred.resolve ( service.person );
            }

            function successCallback ( response ) {
                angular.copy ( response.data, service.person );
                service.hasError = false;
                deferred.resolve ( service.person );
            }

            function errorCallback () {
                service.hasError = true;
                logger.error ( 'Error: couldnt delete the person. Connection problem' );
                deferred.resolve ( [] );
            }

            return deferred.promise;
        }

        function updateData ( person ) {
            saveData ( person );
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
