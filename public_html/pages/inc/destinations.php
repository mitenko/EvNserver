<div id="destination-panel" class="tab-pane fade" ng-controller="DestTblCtrl">
    <div class="panel panel-default">
        <table class="table table-bordered">
            <tr>
                <th>Name <i class="btn pull-right glyphicon glyphicon-sort-by-alphabet"
                            ng-click="sortEventTable('name');"></i></th>
                <th>Short Description <i class="btn pull-right glyphicon glyphicon-sort-by-alphabet"
                                         ng-click="sortEventTable('short_desc');"></i></th>
                <th>&nbsp
                    <a class="btn btn-success" href="#edit-event-panel"
                       data-toggle="tab" ng-click="editEvent(buildEmptyEvent());">
                        <span class="glyphicon glyphicon-plus"></span> Add Destination</a></th>
            </tr>

            <tr ng-repeat="destination in destinations">
                <td>{{destination.detail.name}}</td>
                <td class='hideOverflow'>{{destination.detail.shortDesc}}</td>
                <td>
                            <span class="btn-toolbar">
                                <a class="btn btn-primary" href="#edit-event-panel" data-toggle="tab"
                                   ng-click="editDestination(destination);">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                                <a class="btn btn-warning" data-toggle="modal"
                                   data-target="#confirmDeleteDestination"
                                   ng-click="confirmDeleteDestination(destination);">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                            </span>
                </td>
            </tr>
        </table>

        <!-- Confirm Delete Modal -->
        <div id="confirmDeleteEvent" class="modal fade" role="dialog">
            <div class="modal-sm centered" role="document">
                <div class="modal-content">
                    <div class="modal-header form-group">
                        Confirm Delete
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete the event <b>{{deleteEvent.detail.name}}</b>?
                    </div>
                    <div class="modal-footer btn-toolbar">
                        <a type="button" class="btn btn-danger"
                           data-dismiss="modal" ng-click="onCosdfasdnfirmDeleteEvent(deleteEvent.id);">
                            <span class="glyphicon glyphicon-trash"></span> Delete</a>
                        <a type="button" class="btn btn-warning" data-dismiss="modal">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>