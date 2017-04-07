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