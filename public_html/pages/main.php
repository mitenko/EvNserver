<html lang="en" ng-app="evnApp">
<head>
<title>Events Nanaimo Manager</title>
<?php
require __PAGES__ . 'inc/HeaderRequirements.php';
?>
<script src="/javascript/main.js"></script>
</head>
<body>

<div class="col-md-10 col-md-offset-1">
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#" class="h3">Events Nanaimo Manager</a></li>
    </ul><br>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#events-panel" data-toggle="tab" class="h3" >Events</a></li>
        <li><a href="#destination-panel" data-toggle="tab" class="h3" >Destinations</a></li>
    </ul>

    <div class="tab-content" ng-controller="RootCtrl">

        <!-- The Event Table -->
        <div id="events-panel" class="tab-pane fade in active" ng-controller="EvntTblCtrl">
            <div class="panel panel-default">
                <table class="table table-bordered">
                    <tr>
                        <th>Priority <i class="btn pull-right glyphicon glyphicon-sort"></i></th>
                        <th>Start Time<i class="btn pull-right glyphicon glyphicon-sort"></i></th>
                        <th>Name <i class="btn pull-right glyphicon glyphicon-sort-by-alphabet"></i></th>
                        <th>Short Description <i class="btn pull-right glyphicon glyphicon-sort-by-alphabet"></i></th>
                        <th>&nbsp;</th>
                    </tr>

                    <tr ng-repeat="event in events">
                        <td><a class="btn" style="width:100%;height:100%;"
                               ng-class="getPriorityClass(event.priority);" href>{{event.readablePriority}}</a></td>
                        <td>{{event.readableStartTime}}</td>
                        <td>{{event.detail.name}}</td>
                        <td class='hideOverflow'>{{event.detail.shortDesc}}</td>
                        <td>
                            <a href="#edit-event-panel" data-toggle="tab" ng-click="editEvent(event);">
                                <i class="btn pull-left glyphicon glyphicon-pencil"></i>
                            </a>
                            <i class="btn pull-left glyphicon glyphicon-trash"></i>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <!-- The Edit Event Form -->
        <div id="edit-event-panel" class="tab-pane fade" ng-controller="EditEvntCtrl">
            <pre class="alert alert-info">{{event.detail.imageURL}}</pre>
            <br>
            <div class="panel panel-default col-md-6 col-md-offset-3"><br>
                <form>
                    <div class="row">
                        <span class="col-md-9 form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control input-lg" id="name" ng-model='event.detail.name'>
                        </span>

                        <span class="col-md-3 form-group">
                            <div class="dropdown">
                                <label for="priority">Priority</label>
                                <select class="form-control input-lg" id="priority"
                                        ng-class="priorityCssClass"
                                        ng-model='event.priority'
                                        ng-change="updateClass()"
                                        ng-options="pd.value as pd.text for pd in priorityData">
                                </select>
                            </div>
                        </span>
                    </div>

                    <!-- Image Upload -->
                    <div class="form-group">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail" data-trigger="fileinput">
                                <img src="{{event.detail.imageURL}}" alt="..."></div>
                            <div class="text-center">
                                <span class="btn btn-primary btn-file">
                                    <span class="fileinput-new">Upload Image</span>
                                    <span class="fileinput-exists">Change</span><input type="file" name="..."></span>
                                <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                            </div>
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div class="form-group">
                        <label for="shortDesc">Short Description</label>
                        <textarea class="form-control" rows='2' id="shortDesc" ng-model='event.detail.shortDesc'></textarea>
                    </div>

                    <!-- Long Description -->
                    <div class="form-group">
                        <label for="longDesc">Long Description</label>
                        <textarea class="form-control" rows='5' id="longDesc" ng-model='event.detail.longDesc'></textarea>
                    </div>

                    <!-- Calendar Start Date -->
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="startDate">Start Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="startDate"
                                       uib-datepicker-popup ng-model="startDate" ng-change="startDateChange()"
                                       ng-required="true" is-open="state.startCalOpen"/>
                                <span class="input-group-addon" style="cursor: pointer;">
                                    <i class="glyphicon glyphicon-calendar text-muted" ng-click="openStartCal();"></i>
                                </span>
                            </div>
                        </div>

                        <span class="col-md-6 form-group">
                            <div uib-timepicker ng-model="startDate" ng-change="startTimeChange()"
                                 hour-step="1" minute-step="5"></div>

                        </span>
                    </div>

                    <!-- Calendar End Date -->
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="endDate">End Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="startDate"
                                       uib-datepicker-popup ng-model="endDate" ng-change="endDateChange()"
                                       ng-required="true" is-open="state.endCalOpen"/>
                                <span class="input-group-addon" style="cursor: pointer;">
                                    <i class="glyphicon glyphicon-calendar text-muted" ng-click="openEndCal();"></i>
                                </span>
                            </div>
                        </div>

                        <span class="col-md-6 form-group">
                            <div uib-timepicker ng-model="startDate" ng-change="startTimeChange()"
                                 hour-step="1" minute-step="5"></div>

                        </span>
                    </div>

                    <!-- Destination Section -->
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="endDate">Destinations</label>
                        </div>
                    </div>

                    <!-- Save and Cancel Buttons -->
                    <div class="form-group text-center">
                        <a class="btn btn-success" data-toggle="tab" href="#events-panel">
                            <span class="glyphicon glyphicon-cloud-upload"></span> Save</a>
                        &nbsp;
                        <a class="btn btn-danger" data-toggle="tab" href="#events-panel">
                            <span class="glyphicon glyphicon-remove"></span> Cancel</a>
                    </div>
                </form>
                <br>
            </div>
        </div>

        <div id="destination-panel" class="tab-pane fade">
            <h3>Destinations</h3>
            <p>Some content in menu 1.</p>
        </div>
    </div>
</div>
</body>
</html>