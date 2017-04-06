/**
 * Created by David on 2017-03-25.
 */
var evnApp = angular.module('evnApp', ['ngResource','ui.bootstrap','file-model']);

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
     * Returns an empty event
     * @param $eventPriority
     * @returns {*}
     */
    $scope.buildEmptyEvent = function () {
        var emptyDetail = $scope.buildEmptyDetail()
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
                console.log(response);
                $scope.events = response.data.data;
            });
    };

    /**
     * Sets the list of destinations
     */
    $scope.getDestinations = function () {
        $http.get('/adminApi/getDestinations')
            .then(function (response) {
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
    $scope.getDestinations();
    $scope.getCategoryData();
});

/**
 * Event Table Controller
 */
evnApp.controller('EvntTblCtrl', function EvntTblCtrl($scope, $http) {
    $scope.deleteEvent = $scope.$parent.buildEmptyEvent();
    $scope.sortState = {
        field: 'priority',
        direction: 'ASC'
    };

    /**
     * Opens up the Edit Event panel
     * @param event
     */
    $scope.editEvent = function(event) {
        $scope.$parent.$broadcast('eventSelect', event);
        // Make the Events tab active for return navigation
        $(".nav-tabs").find("li").removeClass("active");
    };

    /**
     * Opens up the confirm delete modal
     */
    $scope.confirmDelete = function(event) {
        $scope.deleteEvent = event;
    };

    /**
     * Opens up the confirm delete modal
     */
    $scope.onConfirmDeleteEvent = function(eventId) {
        console.log('Deleting ' + eventId);
        $http.post('/adminApi/deleteEvent', {'eventId': eventId})
            .then(function(response) {
                console.log(response);
                $scope.$parent.getEvents();
            });

        $scope.$parent.getEvents(
            $scope.sortState.field, $scope.sortState.direction);
    };

    /**
     * Tracks the sorting state of the table
     */
    $scope.sortEventTable = function(field) {
        // First update the sort state
        if (field == $scope.sortState.field) {
            // Toggle the sort direction
            if ($scope.sortState.direction == 'ASC') {
                $scope.sortState.direction = 'DESC';
            } else {
                $scope.sortState.direction = 'ASC';
            }
        } else {
            $scope.sortState.field = field;
            $scope.sortState.direction = 'ASC';
        }

        $scope.$parent.getEvents(
            $scope.sortState.field, $scope.sortState.direction);
    };
});

/**
 * Edit Event Controller
 */
evnApp.controller('EditEvntCtrl', function EvntEvntCtrl(
        $scope, $http, $filter) {
    /**
     * Initializations
     */
    $scope.uploadImage = '';
    $scope.event = $scope.$parent.buildEmptyEvent();
    $scope.state = {
        startCalOpen: false,
        endCalOpen: false,
        hasImage: false,
    };
    $scope.dateOptions = {
        timezone: 'pst'
    };
    $scope.pickerDateFormat = 'MMMM dd, yyyy';
    $scope.selectedCategory = {};
    $scope.selectedActivity = {};
    $scope.priorityCssClass = 'btn btn-primary';
    $scope.readableDateFormat = 'MMM d, yyyy h:mm a';

    /**
     * Called when the Event is set
     */
    $scope.$on('eventSelect', function(event, selectedEvent) {
        $scope.event = selectedEvent;
        $scope.backupEvent = jQuery.extend(true, {}, selectedEvent);
        $scope.startDate = new Date(selectedEvent.unixStartTime * 1000);
        $scope.endDate = new Date(selectedEvent.unixEndTime * 1000);
        $scope.priorityCssClass = $scope.$parent.getPriorityClass(selectedEvent.priority);
        $scope.state.hasImage = ($scope.event.detail.imageURL);
    });

    /**
     * Button Events
     */
    /**
     * Restore with the backup
     */
    $scope.onCancel = function() {
        // Find the event
        index = -1;
        for (var i = 0; i < $scope.$parent.events; i++) {
            if ($scope.backupEvent.id == $scope.$parent.events[i].id) {
                index = i;
                break;
            }
        }
        if (index > -1) {
            $scope.event = $scope.backupEvent;
            $scope.$parent.events[index] = $scope.backupEvent;
        }
    };

    /**
     * Send to the server!
     */
    $scope.onSave = function() {
        if (!$scope.eventEditForm.$valid
                || $scope.event.detail.activities.length == 0
                || !($scope.uploadImage || $scope.event.detail.imageURL)) {
            $('#incompleteEventModal').modal('show');
            return;
        }
        $('.nav-tabs a[href="#events-panel"]').tab('show');

        console.log('Saving Event');
        console.log($scope.event);
        $scope.event.readableStartTime =
            $filter('date')(new Date($scope.startDate), $scope.readableDateFormat);
        var detailId = $scope.event.detail.id;

        // Update the event if we have a detailId
        if (detailId) {
            $http.post('/adminApi/updateEvent',
                {'event': $scope.event});

            if ($scope.uploadImage) {
                $scope.$parent.uploadImageToServer(detailId, $scope.uploadImage);
            }
        } else {
            // Add a new event
            $http.post('/adminApi/addEvent',
                {'event': $scope.event})
            .then(function(response) {
                detailId = response.data.detailId;
                $scope.event.detail.id = detailId;
                $scope.event.id = response.data.eventId;

                $scope.$parent.getEvents();
                // Don't upload the image until we have a detailId
                if ($scope.uploadImage) {
                    $scope.$parent.uploadImageToServer(detailId, $scope.uploadImage);
                }
            });
        }
    };

    /**
     * Calendar Picker
     */
    /**
     * Click event to open the start date calendar popup
     */
    $scope.openStartCal = function() {
        $scope.state.startCalOpen = true;
    };
    $scope.openEndCal = function() {
        $scope.state.endCalOpen = true;
    };

    /**
     * Called when the Start Date has changed
     */
    $scope.startDateChange = function() {
        $scope.event.unixStartTime = new Date($scope.startDate).getTime()/1000;
    };
    $scope.endDateChange = function() {
        $scope.event.unixEndTime = new Date($scope.endDate).getTime()/1000;
    };

    /**
     * Called when the Start Time has changed
     */
    $scope.startTimeChange = function() {
        $scope.event.unixStartTime = new Date($scope.startDate).getTime()/1000;
    };
    $scope.endTimeChange = function() {
        $scope.event.unixEndTime = new Date($scope.endDate).getTime()/1000;
    };

    /**
     * Called to update the priority display class
     */
    $scope.updateClass = function() {
        $scope.priorityCssClass = $scope.$parent.getPriorityClass($scope.event.priority);
    };

    /**
     * Event Destination Methods
     */
    /**
     * Removes the Destination
     */
    $scope.removeDestFromEvent = function(id) {
        var index = $scope.event.destinations.indexOf(id);
        if (index > -1) {
            $scope.event.destinations.splice(index,1);
        }
    };

    /**
     * Adds the Destination
     */
    $scope.addDestToEvent = function(id) {
        var index = $scope.event.destinations.indexOf(id);
        if (index == -1) {
            $scope.event.destinations.push(id);
        }
    };

    /**
     * Adds the Destination
     */
    $scope.getUnselectedDestinations = function() {
        var selectedDestinations = $scope.event.destinations;
        var allDestinations = $scope.destinations;
        var difference = [];

        jQuery.grep(allDestinations, function(el) {
            if (jQuery.inArray(el, selectedDestinations) == -1) difference.push(el);
        });
        return difference;
    };

    /**
     * Event Activity Methods
     */
    /**
     * Removes the Destination
     */
    $scope.removeActivityFromEvent = function(id) {
        var index = -1;
        for(var i = 0; i < $scope.event.detail.activities.length; i++) {
            if ($scope.event.detail.activities[i].id == id) {
                index = i;
                break;
            }
        }
        if (index > -1) {
            $scope.event.detail.activities.splice(index,1);
        }
    };

    /**
     * Adds an Activity
     */
    $scope.addActivityToEvent = function() {
        var selectedId = $scope.selectedActivity.id;
        var index = -1;
        for(var i = 0; i < $scope.event.detail.activities.length; i++) {
            if ($scope.event.detail.activities[i].id == selectedId) {
                index = i;
                break;
            }
        }
        if (index == -1) {
            $scope.event.detail.activities.push($scope.selectedActivity);
        }
    };
});