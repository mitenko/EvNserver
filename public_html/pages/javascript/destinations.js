/**
 * Created by David on 2017-04-07.
 */

/**
 * Destination Table Controller
 */
evnApp.controller('DestTblCtrl', function DestTblCtrl($scope, $http) {
    $scope.deleteDestination = $scope.$parent.buildEmptyDestination();
    $scope.sortState = {
        field: 'name',
        direction: 'ASC'
    };

    /**
     * Opens up the Edit Event panel
     * @param event
     */
    $scope.editDestination = function(destination) {
        $scope.$parent.$broadcast('destinationSelect', destination);
        // Make the Events tab active for return navigation
        $(".nav-tabs").find("li").removeClass("active");
    };

    /**
     * Opens up the confirm delete modal
     */
    $scope.confirmDeleteDest = function(destination) {
        $scope.deleteDestination = destination;
    };

    /**
     * Deletes the destination on confirmation
     */
    $scope.onConfirmDeleteDest = function(destId) {
        console.log('Deleting ' + destId);
        $http.post('/adminApi/deleteDest', {'destId': destId})
            .then(function(response) {
                console.log(response);
                $scope.$parent.getDestinations(
                    $scope.sortState.field, $scope.sortState.direction);
            });
    };

    /**
     * Tracks the sorting state of the table
     */
    $scope.sortDestTable = function(field) {
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

        $scope.$parent.getDestinations(
            $scope.sortState.field, $scope.sortState.direction);
    };
});

/**
 * Edit Destination Controller
 */
evnApp.controller('EditDestCtrl', function EditDestCtrl(
    $scope, $http, NgMap, $timeout) {
    /**
     * Initializations
     */
    $scope.googleMapsUrl = "https://maps.google.com/maps/api/js?key=AIzaSyCDL0vv7gI6sH4Upl8xkrcow6jygDa0aK";
    $scope.defaultCenter = new google.maps.LatLng(
        49.201996, -123.958657);
    $scope.marker = new google.maps.Marker({});
    $scope.defaultZoom = 10;
    $scope.zoomWithLocation = 15;
    $scope.uploadImage = '';
    $scope.dest = $scope.$parent.buildEmptyDestination();
    $scope.state = {
        hasImage: false,
    };
    $scope.selectedCategory = {};
    $scope.selectedActivity = {};

    /**
     * Called when the Destination is set
     */
    $scope.$on('destinationSelect', function(event, selectedDest) {
        $scope.dest = selectedDest;
        $scope.backupDest = jQuery.extend(true, {}, selectedDest);
        $scope.state.hasImage = ($scope.dest.detail.imageURL);
        if (!$scope.state.hasImage) {
            $('.fileinput').fileinput('clear');
        }

        // Resize map
        // See https://github.com/allenhwkim/angularjs-google-maps/issues/471
        $timeout(function() {
            NgMap.getMap().then(function(map) {
                var center;
                var zoom = $scope.defaultZoom;
                if ($scope.dest.latitude && $scope.dest.longitude) {
                    center = new google.maps.LatLng(
                        $scope.dest.latitude, $scope.dest.longitude);
                    zoom = $scope.zoomWithLocation;

                    $scope.marker = new google.maps.Marker({
                        title: $scope.dest.detail.name,
                    });
                    $scope.marker.setPosition(center);
                    $scope.marker.setMap(map);

                } else {
                    center = $scope.defaultCenter;
                    $scope.marker.setMap(null);
                }
                google.maps.event.trigger(map, 'resize');
                map.setCenter(center);
                map.setZoom(zoom);
            });
        }, 500);
    });

    /**
     * Button Events
     */
    /**
     * Restore with the backup
     */
    $scope.onCancelDest = function() {
        // Find the destination
        index = -1;
        for (var i = 0; i < $scope.$parent.destinations.length; i++) {
            if ($scope.backupDest.id == $scope.$parent.destinations[i].id) {
                index = i;
                break;
            }
        }
        if (index > -1) {
            $scope.dest = $scope.backupDest;
            $scope.$parent.destinations[index] = $scope.backupDest;
        }
    };

    /**
     * Send to the server!
     */
    $scope.onSaveDest = function() {
        if (!$scope.eventDestForm.$valid
            || $scope.dest.detail.activities.length == 0
            || !($scope.uploadImage || $scope.dest.detail.imageURL)) {
            $('#incompleteDestModal').modal('show');
            return;
        }
        $('.nav-tabs a[href="#destination-panel"]').tab('show');

        console.log('Saving Destination');
        console.log($scope.dest);
        var detailId = $scope.dest.detail.id;
        console.log($scope.uploadImage);

        // Update the event if we have a detailId
        if (detailId) {
            $http.post('/adminApi/updateDest',
                {'dest': $scope.dest}).then(function(response) {console.log(response);});

            if ($scope.uploadImage) {
                $scope.$parent.uploadImageToServer(detailId, $scope.uploadImage);
            }
        } else {
            // Add a new event
            $http.post('/adminApi/addDest',
                {'dest': $scope.dest})
                .then(function(response) {
                    detailId = response.data.detailId;
                    $scope.dest.detail.id = detailId;
                    $scope.dest.address.id = response.data.addressId;
                    $scope.dest.id = response.data.destId;

                    // Don't upload the image until we have a detailId
                    if ($scope.uploadImage) {
                        $scope.$parent.uploadImageToServer(detailId, $scope.uploadImage);
                    }

                    $scope.$parent.getDestinations('name', 'ASC');
                });
        }
    };

    /**
     * Image Events
     */
    /**
     * Validate the Image Dimensions
     */
    $('.fileinput').fileinput().on('change.bs.fileinput', function(event, file) {
        var image = new Image();
        image.onload = function() {
            if (this.width > 1024) {
                $('#invalidDestImageModal').modal('show');
                $('.fileinput').fileinput('reset');
            }
        };
        image.src = file.result;
    });

    /**
     * Destination Activity Methods
     */
    /**
     * Removes the Destination
     */
    $scope.removeActivityFromDest = function(id) {
        var index = -1;
        for(var i = 0; i < $scope.dest.detail.activities.length; i++) {
            if ($scope.dest.detail.activities[i].id == id) {
                index = i;
                break;
            }
        }
        if (index > -1) {
            $scope.dest.detail.activities.splice(index,1);
        }
    };

    /**
     * Adds an Activity
     */
    $scope.addActivityToDest = function() {
        var selectedId = $scope.selectedActivity.id;
        var index = -1;
        for(var i = 0; i < $scope.dest.detail.activities.length; i++) {
            if ($scope.dest.detail.activities[i].id == selectedId) {
                index = i;
                break;
            }
        }
        if (index == -1) {
            $scope.dest.detail.activities.push($scope.selectedActivity);
        }
    };

    /**
     * Determine the Destination lat / lng from address
     */
    $scope.getLocationFromAddress = function() {
        var fullAddress =
            $scope.dest.address.lineOne
            + ' ' + $scope.dest.address.lineTwo
            + ' ' + $scope.dest.address.city
            + ' ' + $scope.dest.address.postalCode;

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode(
            { 'address': fullAddress},
            function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    console.log(results);
                    // Update the Address itself
                    var addressComponents = results[0].address_components;
                    $scope.dest.address.lineOne =
                        $scope.$parent.getAddressComponent(
                            addressComponents, 'street_number')
                        + ' ' +
                        $scope.$parent.getAddressComponent(
                            addressComponents, 'route');

                    $scope.dest.address.postalCode =
                        $scope.$parent.getAddressComponent(
                            addressComponents, 'postal_code');
                    $scope.dest.address.city =
                        $scope.$parent.getAddressComponent(
                            addressComponents, 'locality');

                    // Update the lat / lng
                    var geometry = results[0].geometry;
                    $scope.dest.latitude = geometry.location.lat();
                    $scope.dest.longitude = geometry.location.lng();

                    // Update the marker
                    $scope.marker = new google.maps.Marker({
                        title: $scope.dest.detail.name,
                    });
                    $scope.marker.setPosition(geometry.location);
                    NgMap.getMap().then(function(map) {
                        map.setCenter(results[0].geometry.location);
                        $scope.marker.setMap(map);
                        map.setZoom($scope.zoomWithLocation);
                    });
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
    };
});