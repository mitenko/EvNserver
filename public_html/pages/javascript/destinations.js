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

        // Resize map
        // See https://github.com/allenhwkim/angularjs-google-maps/issues/471
        $timeout(function() {
            NgMap.getMap().then(function(map) {
                var center = map.getCenter();
                google.maps.event.trigger(map, 'resize');
                map.setCenter(center);
            });
        }, 500);
    });

    /**
     * Button Events
     */
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

                    var geometry = results[0].geometry;
                    NgMap.getMap().then(function(map) {
                        map.setCenter(results[0].geometry.location);
                    });
                    $scope.dest.latitude = geometry.location.lat();
                    $scope.dest.longitude = geometry.location.lng();
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
    };
});