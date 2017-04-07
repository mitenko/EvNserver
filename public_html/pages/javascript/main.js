/**
 * Created by David on 2017-03-25.
 */
var evnApp = angular.module('evnApp',
    ['ngResource','ui.bootstrap','file-model', 'ngMap']);

/**
 * Custom Filter
 */
evnApp.filter('notInArray', function($filter){
    return function(list, arrayFilter, element){
        if(arrayFilter){
            return $filter("filter")(list, function(listItem){
                return arrayFilter.indexOf(listItem[element]) == -1;
            });
        }
    };
});

/**
 * Root Controller
 */
evnApp.controller('RootCtrl', function RootCtrl($scope, $http) {
    /**
     * Initializations
     */
    $scope.destinations = [];
    $scope.events = [];
    $scope.categories = [];

    var priorityData = new Array();
    priorityData[0] = {value: 0, text: 'Ultra', cssClass: 'btn btn-danger'};
    priorityData[1] = {value: 1, text: 'High', cssClass: 'btn btn-warning'};
    priorityData[2] = {value: 2, text: 'Medium', cssClass: 'btn btn-success'};
    priorityData[3] = {value: 3, text: 'Low', cssClass: 'btn btn-primary'};
    $scope.priorityData = priorityData;

    var costData = new Array();
    costData[0] = {value: 0, text: 'Free'};
    costData[1] = {value: 1, text: '$'};
    costData[2] = {value: 2, text: '$$'};
    costData[3] = {value: 3, text: '$$$'};
    costData[4] = {value: 3, text: '$$$$'};
    $scope.costData = costData;

    /**
     * Root Functions
     */
    /**
     * Returns an empty detail
     * @returns {*}
     */
    $scope.buildEmptyDetail = function () {
        var emptyDetail = {
            id: '',
            name: '',
            shortDesc: '',
            longDesc: '',
            imageURL: '',
            phone: '',
            website: '',
            cost: '',
            activities: [],
        };
        return emptyDetail;
    };

    /**
     * Returns an empty address
     * @returns {*}
     */
    $scope.buildEmptyAddress= function () {
        var emptyAddress = {
            id: '',
            lineOne: '',
            lineTwo: '',
            postalCode: '',
            city: ''
        };
        return emptyAddress;
    };

    /**
     * Returns an empty event
     * @param $eventPriority
     * @returns {*}
     */
    $scope.buildEmptyEvent = function () {
        var emptyDetail = $scope.buildEmptyDetail();
        var emptyEvent = {
            id: '',
            detail: emptyDetail,
            unixStartTime: Math.floor(Date.now() / 1000),
            readableStartTime: '',
            unixEndTime: Math.floor(Date.now() / 1000),
            readableEndTime: '',
            priority: 3,
            destinations: []
        };
        return emptyEvent;
    };

    /**
     * Returns an empty event
     * @param $eventPriority
     * @returns {*}
     */
    $scope.buildEmptyDestination = function () {
        var emptyDetail = $scope.buildEmptyDetail();
        var emptyAddress = $scope.buildEmptyAddress();
        var emptyDestination = {
            id: '',
            detail: emptyDetail,
            address: emptyAddress,
            longitude: '',
            latitude: '',
        };
        return emptyDestination;
    };

    /**
     * Returns the css class for the event priority
     * @param $eventPriority
     * @returns {*}
     */
    $scope.getPriorityClass = function ($eventPriority) {
        if ($scope.priorityData.length > $eventPriority && $eventPriority >= 0) {
            return $scope.priorityData[$eventPriority].cssClass;
        }
        return '';
    };

    /**
     * Returns the css class for the event priority
     * @param $eventPriority
     * @returns {*}
     */
    $scope.getPriorityName = function ($eventPriority) {
        if ($scope.priorityData.length > $eventPriority && $eventPriority >= 0) {
            return $scope.priorityData[$eventPriority].text;
        }
        return '';
    };

    /**
     * Returns the cost as readable test
     * @param $eventPriority
     * @returns {*}
     */
    $scope.getCostName = function (cost) {
        if ($scope.costData.length > cost && cost >= 0) {
            return $scope.costData[cost].text;
        }
        return '';
    };

    /**
     * Returns the destination name given the destination id
     * @param $id
     * @returns {string}
     */
    $scope.getDestinationName = function (id) {
        for (var i = 0; i < $scope.destinations.length; i++) {
            if ($scope.destinations[i].id == id) {
                return $scope.destinations[i].detail.name;
            }
        }
        return 'Unknown Destination (' + id + ')';
    };

    /**
     * Uploads the image file for the given detail id
     * @returns {string}
     */
    $scope.uploadImageToServer = function (id, imageData) {
        var formData = new FormData();
        console.log('detail:' + id);
        formData.append('detailId', id);
        formData.append('uploadImage', imageData);
        $http.post('/adminApi/updateImage', formData, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).then(function (response) {
            // REturns the detail ID
            // now post to /adminApi/updateImage
            console.log(response);
        });
    };

    $scope.getAddressComponent = function (addressComponents, componentType) {
        for (var i = 0; i < addressComponents.length; i++) {
            var component = addressComponents[i];
            for (var j = 0; j < component['types'].length; j++) {
                if (component['types'][j]==componentType) {
                    return component.long_name;
                }
            }
        }
    }

    /**
     * HTTP calls
     */
    /**
     * Returns the list of events
     */
    $scope.getEvents = function (sorton, sortdir) {
        $http.get('/adminApi/getEvents',
            {params: {'sorton': sorton, 'sortdir': sortdir}})
            .then(function (response) {
                $scope.events = response.data.data;
            });
    };

    /**
     * Sets the list of destinations
     */
    $scope.getDestinations = function (sorton, sortdir) {
        $http.get('/adminApi/getDestinations',
            {params: {'sorton': sorton, 'sortdir': sortdir}})
            .then(function (response) {
                console.log(response);
                $scope.destinations = response.data.data;
            });
    };

    /**
     * Gets the category / activity data
     */
    $scope.getCategoryData = function () {
        $http.get('/adminApi/getCategoryData')
            .then(function (response) {
                $scope.categories = response.data.data;
            });
    };

    $scope.getEvents('priority', 'ASC');
    $scope.getDestinations('name', 'ASC');
    $scope.getCategoryData();
});
