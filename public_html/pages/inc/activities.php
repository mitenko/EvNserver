<!-- The Activity Table -->
<div id="activity-panel" class="tab-pane fade" ng-controller="ActvTblCtrl">
    <div class="panel panel-default">
        <table class="table table-bordered">
            <tr>
                <th>Category</th>
                <th>Activity&nbsp;
                    <a class="btn btn-success pull-right" href="#edit-event-panel"
                       data-toggle="modal"
                       data-target="#editActivityModal"
                       ng-click="editActivity(buildEmptyActivity());">
                        <span class="glyphicon glyphicon-plus"></span> Add Activity</a></th>
            </tr>

            <tr ng-repeat="category in categories">
                <td class="col-md-2">
                    <a class="btn btn-success">{{category.name}}</a>
                </td>
                <td class="col-md-10">
                    <span class="btn-toolbar">
                        <a class="btn btn-primary flow-btn"
                           ng-repeat="activity in category.activities"
                           data-toggle="modal"
                           data-target="#editActivityModal"
                           ng-click="editActivity(activity);">
                            {{activity.name}}
                        </a>
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Edit Activity Modal -->
    <div id="editActivityModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-vertical-center">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{selectedActivity.name ? 'Edit Activity' : 'Add New Activity'}}</h4>
                </div>
                <div class="modal-body">
                    <div class="row col-md-12">
                        <form name="editActivityForm">
                            <span class="form-group">
                                <label for="name">Activity Name</label>
                                <input type="text" class="form-control input-lg" ng-required="true"
                                       id="name" ng-model='selectedActivity.name' placeholder="New Activity Name">
                            </span>
                        </form>
                    </div>

                    <div class="row col-md-12">
                        <span class="form-group">
                            <label for="categories">Associated Categories</label>
                            <div id="categories" class="btn-toolbar">
                                <a class="btn btn-success flow-btn"
                                   ng-repeat="category in getAssociatedCategories(selectedActivity)">
                                    {{category.name}}
                                    <span class="glyphicon glyphicon-remove"
                                          ng-click="removeCategory(category, selectedActivity);"></span></a>
                            </div>
                        </span>
                    </div>

                    <div class="row col-md-12">
                        <span class="form-group">
                            <label for="categories">Unassociated Categories</label>
                            <div id="categories" class="btn-toolbar">
                                <a class="btn btn-warning flow-btn"
                                   ng-repeat="category in getUnassociatedCategories(selectedActivity)">
                                    {{category.name}}
                                    <span class="glyphicon glyphicon-plus"
                                          ng-click="addCategory(category, selectedActivity);"></span></a>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="modal-footer btn-toolbar">
                    <a class="btn btn-success" ng-click="onSaveActivity();">
                        <span class="glyphicon glyphicon-cloud-upload"></span> Save</a>
                    <a class="btn btn-warning"
                       data-toggle="modal"
                       data-target="#editActivityModal"
                       ng-click="onCancelEditActivity();">
                        <span class="glyphicon glyphicon-remove"></span> Cancel</a>
                    <a class="btn btn-danger"
                       data-toggle="modal"
                       data-target="#confirmDeleteActivity"
                       ng-click="confirmDeleteActivity();">
                        <span class="glyphicon glyphicon-trash"></span> Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Incomplete Fields Modal -->
    <div id="incompleteActivityModal" class="modal fade" role="dialog">
        <div class="modal-sm centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    Missing required fields!
                </div>
                <div class="modal-body">
                    An activity requires a <b>name and at least one associated category.</b>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-primary" data-dismiss="modal">Close</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div id="confirmDeleteActivity" class="modal fade" role="dialog">
        <div class="modal-sm centered" role="document">
            <div class="modal-content">
                <div class="modal-header form-group">
                    Confirm Delete
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the activity <b>{{deleteActivity.name}}</b>?
                </div>
                <div class="modal-footer btn-toolbar">
                    <a type="button" class="btn btn-danger"
                       data-dismiss="modal" ng-click="onConfirmDeleteActivity();">
                        <span class="glyphicon glyphicon-trash"></span> Delete</a>
                    <a type="button" class="btn btn-warning" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>