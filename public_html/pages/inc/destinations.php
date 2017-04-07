<div id="destination-panel" class="tab-pane fade" ng-controller="DestTblCtrl">
    <div class="panel panel-default">
        <table class="table table-bordered">
            <tr>
                <th>Name <i class="btn pull-right glyphicon glyphicon-sort-by-alphabet"
                            ng-click="sortDestTable('name');"></i></th>
                <th>Short Description <i class="btn pull-right glyphicon glyphicon-sort-by-alphabet"
                                         ng-click="sortDestTable('short_desc');"></i></th>
                <th>Cost <i class="btn pull-right glyphicon glyphicon-sort"
                            ng-click="sortDestTable('cost');"></i></th>
                <th>&nbsp
                    <a class="btn btn-success" href="#edit-event-panel"
                       data-toggle="tab" ng-click="editEvent(buildEmptyEvent());">
                        <span class="glyphicon glyphicon-plus"></span> Add Destination</a></th>
            </tr>

            <tr ng-repeat="destination in destinations">
                <td>{{destination.detail.name}}</td>
                <td class='hideOverflow'>
                    {{destination.detail.shortDesc | limitTo : 60}}{{destination.detail.shortDesc.length > 60 ? '...' : ''}}
                </td>
                <td class="money">{{getCostName(event.detail.cost);}}</td>
                <td>
                            <span class="btn-toolbar">
                                <a class="btn btn-primary" href="#edit-dest-panel" data-toggle="tab"
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

<!-- The Edit Destination Form -->
<div id="edit-dest-panel" class="tab-pane fade" ng-controller="EditDestCtrl">
    <br>
    <div class="panel panel-default col-md-6 col-md-offset-3"><br>
        <form name="eventDestForm">
            <!-- Destination Name -->
            <div class="row">
                <span class="col-md-9 form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control input-lg" ng-required="true"
                           id="name" ng-model='dest.detail.name' placeholder="Destination Name">
                </span>
            </div>

            <!-- Image Upload -->
            <div class="form-group">
                <div class="fileinput " ng-class="state.hasImage? 'fileinput-exists' : 'fileinput-new'"
                     data-provides="fileinput" ng-model="newImage">
                    <div class="fileinput-preview fileinput-exists thumbnail" data-trigger="fileinput">
                        <img src="{{dest.detail.imageURL}}" alt="...">
                    </div>
                    <div class="fileinput-new thumbnail" data-trigger="fileinput">
                        <img src="https://eventsnanaimo.com/img/placeholder.png" alt="..."></div>
                    <div class="text-center">
                                <span class="btn btn-primary btn-file">
                                    <span class="fileinput-new">Add Image</span>
                                    <span class="fileinput-exists">Change</span>
                                    <input type="file" name="file" file-model="uploadImage">
                                </span>
                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                </div>
            </div>


            <!-- Short Description -->
            <div class="form-group">
                <label for="shortDesc">Short Description</label>
                <textarea class="form-control" rows='3' id="shortDesc" ng-required="true"
                          placeholder="A short summary of the event" ng-model='dest.detail.shortDesc'></textarea>
            </div>

            <!-- Long Description -->
            <div class="form-group">
                <label for="longDesc">Long Description</label>
                <textarea class="form-control" rows='8' id="longDesc" ng-required="true"
                          placeholder="A more detailed description of the event. No longer than three paragraphs."
                          ng-model='dest.detail.longDesc'></textarea>
            </div>

            <!-- Website and Phone -->
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="website">Website</label>
                    <input type="text" class="form-control"
                           id="website" ng-model='dest.detail.website' placeholder="Website URL">
                </div>

                <span class="col-md-6 form-group">
                            <label for="phone">Contact Phone</label>
                            <input type="text" class="form-control"
                                   id="phone" ng-model='dest.detail.phone' placeholder="Contact Phone Number">
                        </span>
            </div>

            <!-- Cost -->
            <div class="row">
                <div class="form-group col-md-4 col-md-offset-4">
                    <div class="dropdown">
                        <label for="cost">Cost</label>
                        <select class="form-control money" id="cost"
                                ng-model='dest.detail.cost'
                                ng-options="cost.value as cost.text for cost in costData">
                        </select>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="row"><hr>
                <div class="form-group col-md-12">
                    <label for="addressOne">Address Line One</label>
                    <input type="text" class="form-control" ng-required="true"
                           id="addressOne" ng-model='dest.address.lineOne' placeholder="Destination Address">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label for="addressTwo">Address Line Two</label>
                    <input type="text" class="form-control" ng-required="true"
                           id="addressTwo" ng-model='dest.address.lineTwo' placeholder="">
                </div>
            </div>

            <!-- Postal Code and City -->
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="postalCode">Postal Code</label>
                    <input type="text" class="form-control"
                           id="postalCode" ng-model='dest.address.postalCode' placeholder="Postal Code">
                </div>

                <span class="col-md-6 form-group">
                    <label for="city">City</label>
                    <input type="text" class="form-control"
                           id="city" ng-model='dest.address.city' placeholder="City">
                </span>
            </div>

            <!-- Cost -->
            <div class="row">
                <div class="form-group col-md-6 col-md-offset-3">
                    <a class="btn btn-primary" ng-click="getLocationFromAddress();">
                        <span class="glyphicon glyphicon-map-marker"></span> Get Location Using Address</a>

                </div>
            </div>

            <!-- Destination Map -->
            <div class="row">
                <div class="form-group col-md-12">
                    <div map-lazy-load="https://maps.google.com/maps/api/js"
                       map-lazy-load-params="{{googleMapsUrl}}" >
                        <ng-map default-style="true"
                                center="{{dest.latitude}},{{dest.longitude}}"
                                zoom="15">
                            <marker position="{{dest.latitude}},{{dest.longitude}}" title="hello"></marker>
                        </ng-map>
                    </div>
                </div>
            </div>

            <!-- Latitude and Longitude -->
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="latitude">Latitude</label>
                    <input type="text" class="form-control"
                           id="latitude" ng-model='dest.latitude' placeholder="Latitude">
                </div>

                <span class="col-md-6 form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" class="form-control"
                           id="longitude" ng-model='dest.longitude' placeholder="Longitude">
                </span>
            </div>

        </form>
    </div>
</div>