/**
 * Destination Table Controller
 */
evnApp.controller('ActvTblCtrl', function ActvTblCtrl($scope, $http) {
    $scope.selectedActivity = $scope.$parent.buildEmptyActivity();
    /**
     * Called when the user selects an activity to edit
     * @param destination
     */
    $scope.editActivity = function(activity) {
        $scope.selectedActivity = activity;
    };

    /**
     * Called when the user wishes to cancel their changes
     */
    $scope.onSaveActivity = function() {
        var associatedCategories =
            $scope.getAssociatedCategories($scope.selectedActivity);
        if (!$scope.editActivityForm.$valid
            || associatedCategories.length == 0) {
            $('#incompleteActivityModal').modal('show');
            return;
        }

        var activityId = $scope.selectedActivity.id;
        var activityName = $scope.selectedActivity.name;
        var associatedCategoryIds = [];
        for (var i = 0; i < associatedCategories.length; i++) {
            associatedCategoryIds.push(associatedCategories[i].id);
        }

        if (activityId == -1) {
            // New Activity, Add it
        } else {
            // Update the Activity
            $http.post('/adminApi/updateActivity',
                {'id': activityId,
                'name': activityName,
                'categoryIds': associatedCategoryIds})
                .then(function(response) {
                    $scope.$parent.getCategoryData();
                });
        }
    };

    /**
     * Called when the user wishes to cancel their changes
     */
    $scope.onCancelEditActivity = function() {
        $scope.$parent.getCategoryData();
    };

    /**
     * Returns an array of categories for the
     * passed activity
     * @param destination
     */
    $scope.getAssociatedCategories = function(activity) {
        if (activity == null) {
            return;
        }
        var activityId = activity.id;
        var categories = [];
        for (var i = 0; i < $scope.$parent.categories.length; i++) {
            var activities = $scope.$parent.categories[i].activities;
            for (var j = 0; j < activities.length; j++) {
                if (activities[j].id == activityId) {
                    categories.push($scope.$parent.categories[i]);
                    break;
                }
            }
        }
        return categories;
    };

    /**
     * Returns an array of unassociated categories for the
     * passed activity
     * @param destination
     */
    $scope.getUnassociatedCategories = function(activity) {
        if (activity == null) {
            return;
        }
        var activityId = activity.id;
        var categories = [];
        for (var i = 0; i < $scope.$parent.categories.length; i++) {
            var activities = $scope.$parent.categories[i].activities;
            var hasActivity = false;
            for (var j = 0; j < activities.length; j++) {
                if (activities[j].id == activityId) {
                    hasActivity = true;
                    break;
                }
            }
            if (!hasActivity) {
                categories.push($scope.$parent.categories[i]);
            }
        }
        return categories;
    };

    /**
     * Removes the activity from the selected category
     * @param category
     * @param selectedActivity
     */
    $scope.removeCategory = function(category, selectedActivity){
        var activities = category.activities;
        var activityId = selectedActivity.id;
        var index = -1;
        for (var j = 0; j < activities.length; j++) {
            if (activities[j].id == activityId) {
                index = j;
                break;
            }
        }
        if (index > -1) {
            category.activities.splice(index,1);
        }
    }

    /**
     * Removes the activity from the selected category
     * @param category
     * @param selectedActivity
     */
    $scope.addCategory = function(category, selectedActivity){
        category.activities.push(selectedActivity);
    }
});
