<!-- The Event Table -->
<div id="events-panel" class="tab-pane fade in active" ng-controller="EvntTblCtrl">
    <div class="panel panel-default">
        <table class="table table-bordered">
            <tr>
                <th>Priority <i class="btn pull-right glyphicon glyphicon-sort"
                                ng-click="sortEventTable('priority');"></i></th>
                <th>Start Time<i class="btn pull-right glyphicon glyphicon-sort"
                                 ng-click="sortEventTable('start_time');"></i></th>
                <th>Name <i class="btn pull-right glyphicon glyphicon-sort-by-alphabet"
                            ng-click="sortEventTable('name');"></i></th>
                <th>Short Description <i class="btn pull-right glyphicon glyphicon-sort-by-alphabet"
                                         ng-click="sortEventTable('short_desc');"></i></th>
                <th>Cost <i class="btn pull-right glyphicon glyphicon-sort"
                            ng-click="sortEventTable('cost');"></i></th>
                <th>&nbsp
                    <a class="btn btn-success" href="#edit-event-panel"
                       data-toggle="tab" ng-click="editEvent(buildEmptyEvent());">
                        <span class="glyphicon glyphicon-plus"></span> Add Event</a></th>
            </tr>

            <tr ng-repeat="event in events">
                <td><a class="btn" style="width:100%;height:100%;"
                       ng-class="getPriorityClass(event.priority);" href>{{getPriorityName(event.priority);}}</a></td>
                <td>{{event.readableStartTime}}</td>
                <td>{{event.detail.name}}</td>
                <td class='hideOverflow'>
                    {{event.detail.shortDesc | limitTo : 30}}{{event.detail.shortDesc.length > 30 ? '...' : ''}}</td>
                <td class="money">{{getCostName(event.detail.cost);}}</td>
                <td>
                            <span class="btn-toolbar">
                                <a class="btn btn-primary" href="#edit-event-panel" data-toggle="tab"
                                   ng-click="editEvent(event);">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                                <a class="btn btn-warning" data-toggle="modal"
                                   data-target="#confirmDeleteEvent" ng-click="confirmDeleteEvent(event);">
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
                           data-dismiss="modal" ng-click="onConfirmDeleteEvent(deleteEvent.id);">
                            <span class="glyphicon glyphicon-trash"></span> Delete</a>
                        <a type="button" class="btn btn-warning" data-dismiss="modal">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Edit Event Form -->
<div id="edit-event-panel" class="tab-pane fade" ng-controller="EditEvntCtrl">
    <br>
    <div class="panel panel-default col-md-6 col-md-offset-3"><br>
        <form name="eventEditForm">
            <div class="row">
                <span class="col-md-9 form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control input-lg" ng-required="true"
                           id="name" ng-model='event.detail.name' placeholder="Event Title">
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
                <div class="fileinput " ng-class="state.hasImage? 'fileinput-exists' : 'fileinput-new'"
                     data-provides="fileinput" ng-model="newImage">
                    <div class="fileinput-preview fileinput-exists thumbnail" data-trigger="fileinput">
                        <img src="{{event.detail.imageURL}}" alt="...">
                    </div>
                    <div class="fileinput-new thumbnail" data-trigger="fileinput">
                        <img src="https://eventsnanaimo.com/img/placeholder.png" alt="..."></div>
                    <div class="text-center">
                                <span class="btn btn-primary btn-file">
                                    <span class="fileinput-new">Add Image</span>
                                    <span class="fileinput-exists">Change</span>
                                    <input type="file" name="file"
                                           file-model="uploadImage">
                                </span>
                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                </div>
            </div>

            <!-- Short Description -->
            <div class="form-group">
                <label for="shortDesc">Short Description</label>
                <textarea class="form-control" rows='3' id="shortDesc" ng-required="true"
                          placeholder="A short summary of the event" ng-model='event.detail.shortDesc'></textarea>
            </div>

            <!-- Long Description -->
            <div class="form-group">
                <label for="longDesc">Long Description</label>
                <textarea class="form-control" rows='8' id="longDesc" ng-required="true"
                          placeholder="A more detailed description of the event. No longer than three paragraphs."
                          ng-model='event.detail.longDesc'></textarea>
            </div>

            <!-- Website and Phone -->
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="website">Website</label>
                    <input type="text" class="form-control"
                           id="website" ng-model='event.detail.website' placeholder="Website URL">
                </div>

                <span class="col-md-6 form-group">
                            <label for="phone">Contact Phone</label>
                            <input type="text" class="form-control"
                                   id="phone" ng-model='event.detail.phone' placeholder="Contact Phone Number">
                        </span>
            </div>

            <!-- Cost -->
            <div class="row">
                <div class="form-group col-md-4 col-md-offset-4">
                    <div class="dropdown">
                        <label for="cost">Cost</label>
                        <select class="form-control money" id="cost"
                                ng-model='event.detail.cost'
                                ng-options="cost.value as cost.text for cost in costData">
                        </select>
                    </div>
                </div>
            </div>

            <!-- Calendar Start Date -->
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="startDate">Start Date</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="startDate"
                               uib-datepicker-popup="{{pickerDateFormat}}"
                               ng-model="startDate"
                               ng-change="startDateChange()"
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
                        <input type="text" class="form-control"
                               id="endDate" uib-datepicker-popup="{{pickerDateFormat}}"
                               uib-datepicker-popup ng-model="endDate" ng-change="endDateChange()"
                               ng-required="true" is-open="state.endCalOpen"/>
                        <span class="input-group-addon" style="cursor: pointer;">
                                    <i class="glyphicon glyphicon-calendar text-muted" ng-click="openEndCal();"></i>
                                </span>
                    </div>
                </div>

                <span class="col-md-6 form-group">
                            <div uib-timepicker ng-model="endDate" ng-change="startTimeChange()"
                                 hour-step="1" minute-step="5"></div>

                        </span>
            </div>

            <!-- Destination Section -->
            <div class="form-group">
                <hr>
                <label for="destination" class="col-md-12">Destinations
                    <a class="btn btn-success pull-right" data-toggle="modal" data-target="#destinationSelect">
                        Add Destination</a>
                </label>
                <div id="destinations" class="btn-toolbar">
                    <a class="btn btn-primary flow-btn" ng-repeat="id in event.destinations">
                        {{getDestinationName(id)}}
                        <span class="glyphicon glyphicon-remove" ng-click="removeDestFromEvent(id);"></span></a>
                </div>
            </div>

            <!-- Destination Select Modal -->
            <div id="destinationSelect" class="modal fade" role="dialog">
                <div class="modal-sm centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Select a Destination
                        </div>
                        <div class="modal-body">
                            <div class="btn-toolbar">
                            <a class="btn btn-success flow-btn"
                               ng-repeat='destination in destinations | notInArray:event.destinations:"id"'
                               ng-click="addDestToEvent(destination.id);">
                                {{getDestinationName(destination.id)}}
                                <span class="glyphicon glyphicon-plus"
                                      data-toggle="modal" data-target="#destinationSelect"></span></a>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-primary" data-dismiss="modal">Close</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Section -->
            <hr>
            <div class="form-group">
                <label for="activities" class="col-md-12">Activities
                    <a class="btn btn-success pull-right" data-toggle="modal" data-target="#activitySelect">Add
                        Activity</a>
                </label>
                <div id="activities" class="btn-toolbar">
                    <a class="btn btn-primary flow-btn" ng-repeat="activity in event.detail.activities">
                        {{activity.category + '::' + activity.name}}
                        <span class="glyphicon glyphicon-remove"
                              ng-click="removeActivityFromEvent(activity.id);"></span></a>
                </div>
            </div>

            <!-- Activity Select Modal -->
            <div id="activitySelect" class="modal fade" role="dialog">
                <div class="modal-sm centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header form-group">
                            Select a Category and Activity
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select class="form-control" id="category" ng-change="catChange();"
                                        ng-model="selectedCategory"
                                        ng-options="category.name for category in categories">
                                </select>
                                <label for="activity">Activity</label>
                                <select class="form-control" id="activity"
                                        ng-model="selectedActivity"
                                        ng-options="activity.name for activity in selectedCategory.activities">
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer btn-toolbar">
                            <a type="button" class="btn btn-success" ng-click="addActivityToEvent();">
                                <span class="glyphicon glyphicon-plus"></span> Add</a>
                            <a type="button" class="btn btn-primary" data-dismiss="modal">Done</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incomplete Fields Modal -->
            <div id="incompleteEventModal" class="modal fade" role="dialog">
                <div class="modal-sm centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Missing required fields!
                        </div>
                        <div class="modal-body">
                            An event requires a <b>name, a short description, a long description,
                                an image and at least one associated activity.</b>
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-primary" data-dismiss="modal">Close</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invalid Image Size Modal -->
            <div id="invalidEventImageModal" class="modal fade" role="dialog">
                <div class="modal-sm centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Invalid Image Dimensions
                        </div>
                        <div class="modal-body">
                            The max width for images is 1024 px. Please resize your image.
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-primary" data-dismiss="modal">Okay</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save and Cancel Buttons -->
            <hr>
            <br>
            <div class="form-group text-center"> <!-- data-toggle="tab" href="#events-panel"-->
                <a class="btn btn-success" ng-click="onSave();">
                    <span class="glyphicon glyphicon-cloud-upload"></span> Save</a>
                &nbsp;
                <a class="btn btn-danger" data-toggle="tab" href="#events-panel" ng-click="onCancel();">
                    <span class="glyphicon glyphicon-remove"></span> Cancel</a>
            </div>
        </form>
        <br>
    </div>
</div>
