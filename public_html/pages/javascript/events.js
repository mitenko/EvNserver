
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
    $scope.confirmDeleteEvent = function(event) {
        $scope.deleteEvent = event;
    };

    /**
     * Deletes the event on confirmation
     */
    $scope.onConfirmDeleteEvent = function(eventId) {
        console.log('Deleting ' + eventId);
        $http.post('/adminApi/deleteEvent', {'eventId': eventId})
            .then(function(response) {
                console.log(response);
                $scope.$parent.getEvents(
                    $scope.sortState.field, $scope.sortState.direction);
            });
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
    $scope.uploadImage = $scope.$parent.imagePlaceholder;
    $scope.thumbnail = $scope.$parent.imagePlaceholder;
    $scope.cropper = {
        primaryImage: null,
        croppedImage: null
    };
    $scope.event = $scope.$parent.buildEmptyEvent();
    $scope.state = {
        startCalOpen: false,
        endCalOpen: false,
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

        // Image Setup
        $scope.cropper = {
            primaryImage: null,
            croppedImage: null
        };
        $scope.uploadImage = ($scope.event.detail.imageURL) ?
            $scope.event.detail.imageURL :
            $scope.$parent.imagePlaceholder;
        $scope.thumbnail = ($scope.event.detail.thumbURL) ?
            $scope.event.detail.thumbURL :
            $scope.$parent.imagePlaceholder;
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
            || $scope.uploadImage == $scope.$parent.imagePlaceholder
            || $scope.thumbnail == $scope.$parent.imagePlaceholder) {
            $('#incompleteEventModal').modal('show');
            return;
        }
        $('.nav-tabs a[href="#events-panel"]').tab('show');

        console.log('Saving Event');
        console.log($scope.uploadImage);
        $scope.event.readableStartTime =
            $filter('date')(new Date($scope.startDate), $scope.readableDateFormat);
        var detailId = $scope.event.detail.id;

        // Update the event if we have a detailId
        if (detailId) {
            $http.post('/adminApi/updateEvent',
                {'event': $scope.event})
                .then(function(response) {
                    $scope.uploadEventImages(detailId)
                });
        } else {
            // Add a new event
            $http.post('/adminApi/addEvent',
                {'event': $scope.event})
                .then(function(response) {
                    detailId = response.data.detailId;
                    $scope.event.detail.id = detailId;
                    $scope.event.id = response.data.eventId;

                    // Now that we have a detailID, we can upload
                    $scope.uploadEventImages(detailId);

                    $scope.$parent.getEvents();
                });
        }
    };

    /**
     * Uploads the Primary and Thumbnail Images
     * @param detailId
     */
    $scope.uploadEventImages = function(detailId) {
        if ($scope.uploadImage &&
            $scope.uploadImage != $scope.event.detail.imageURL) {
            $scope.$parent.uploadImagesToServer(
                detailId, $scope.uploadImage, 'PrimaryImage');
            $scope.event.detail.imageURL = $scope.uploadImage;
        }

        if ($scope.thumbnail &&
            $scope.thumbnail != $scope.event.detail.thumbURL) {
            var blob = $scope.$parent.dataURItoBlob($scope.thumbnail);
            var thumbnailFile = new File([blob], 'thumbnail.png', {type:"image/png"});
            $scope.$parent.uploadImagesToServer(
                detailId, thumbnailFile, 'Thumbnail');
            $scope.event.detail.thumbURL = $scope.thumbnail;
        }
    };

    /**
     * Image Events
     */
    /**
     * Validate the Image Dimensions
     */
    $scope.validateEventImage = function($files, $file) {
        if($scope.eventEditForm.imageInput.$error.maxWidth) {
            $scope.uploadImage = $scope.$parent.imagePlaceholder;
            $('#invalidImageModal').modal('show');
        } else {
            // Start parsing that cropper.primaryImage **now**
            var reader = new FileReader();
            reader.addEventListener("load", function () {
                $scope.cropper.primaryImage = reader.result;
            }, false);

            reader.readAsDataURL($scope.uploadImage);
        }
    };

    /**
     * Initialize the image crop
     */
    $scope.initImageCrop = function() {
        console.log($scope.uploadImage);
        if (!($scope.uploadImage instanceof File)) {
            $scope.cropper.primaryImage = $scope.uploadImage;
        }
    };

    /**
     * Initialize the image crop
     */
    $scope.onSaveImageCrop = function() {
        $scope.thumbnail = $scope.cropper.croppedImage;
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
});/**
 * Created by David on 2017-04-07.
 */
