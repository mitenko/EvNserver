/**
 * Created by David on 2017-03-25.
 */
var evnApp = angular.module('evnApp', ['ngResource']);

/**
 * Services
 */
evnApp.service('eventService', function() {
    var event;

    var setEvent = function(eventToSet) {
        console.log('Service Setting Event:' + eventToSet.detail.name);
        event = eventToSet;
    };

    var getEvent = function(){
        if (event != null) {
            console.log('Service returning Event:' + event.detail.name);
        }
        return event;
    };

    return {
        setEvent: setEvent,
        getEvent: getEvent
    };
});

/**
 * Event Table Controller
 */
evnApp.controller('EvntTblCtrl', function EvntTblCtrl($scope, $http, $rootScope, eventService) {
    $scope.events = [];

    $http.get('/adminApi/getEvents')
        .then(function(response) {
            $scope.events = response.data.data;
    });

    $scope.editEvent = function(event) {
        $rootScope.$broadcast('eventSelect', event);
    };
});

/**
 * Edit Event Controller
 */
evnApp.controller('EditEvntCtrl', function EvntEvntCtrl($scope, $http, eventService) {
    $scope.$on('eventSelect', function(event, selectedEvent) {
        $scope.event = selectedEvent;
    });
});