<!-- The Destination Table -->
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
                    <a class="btn btn-success" href="#edit-dest-panel"
                       data-toggle="tab" ng-click="editDestination(buildEmptyDestination());">
                        <span class="glyphicon glyphicon-plus"></span> Add Destination</a></th>
            </tr>

            <tr ng-repeat="destination in destinations">
                <td>{{destination.detail.name}}</td>
                <td class='hideOverflow'>
                    {{destination.detail.shortDesc | limitTo : 60}}{{destination.detail.shortDesc.length > 60 ? '...' : ''}}
                </td>
                <td class="money">{{getCostName(destination.detail.cost);}}</td>
                <td>
                            <span class="btn-toolbar">
                                <a class="btn btn-primary" href="#edit-dest-panel" data-toggle="tab"
                                   ng-click="editDestination(destination);">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                                <a class="btn btn-warning" data-toggle="modal"
                                   data-target="#confirmDeleteDestModal"
                                   ng-click="confirmDeleteDest(destination);">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                            </span>
                </td>
            </tr>
        </table>

        <!-- Confirm Delete Modal -->
        <div id="confirmDeleteDestModal" class="modal fade" role="dialog">
            <div class="modal-sm centered" role="document">
                <div class="modal-content">
                    <div class="modal-header form-group">
                        Confirm Delete
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete the destination <b>{{deleteDestination.detail.name}}</b>?
                    </div>
                    <div class="modal-footer btn-toolbar">
                        <a type="button" class="btn btn-danger"
                           data-dismiss="modal" ng-click="onConfirmDeleteDest(deleteDestination.id);">
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
        <form name="destEditForm">
            <!-- Destination Name -->
            <div class="row">
                <span class="col-md-9 form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control input-lg" ng-required="true"
                           id="name" ng-model='dest.detail.name' placeholder="Destination Name">
                </span>
            </div>

            <!-- Image Upload -->
            <div class="row">
                <div class="form-group col-md-12">
                    <label>Primary Image</label><br>
                    <div name="imageInput"
                         ngf-select="" ngf-drop=""
                         ng-model="uploadImage"
                         ngf-multiple="false" ngf-accept="'image/*'"
                         ngf-drop-available="dropSupported"
                         ngf-max-width="{{maxImageWidth}}"
                         ngf-change="validateDestImage($files, $file);">
                        <img class="col-md-12 img-thumbnail" ngf-src="uploadImage"><br>
                        <a type="button"
                           class="btn btn-primary btn-lrg col-md-6 col-md-offset-3">
                            Select <span ng-show="dropSupported">or Drag </span>Image Here&nbsp;
                            <span class="glyphicon glyphicon-picture"></span>
                        </a>
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

            <!-- Activity Section -->
            <hr>
            <div class="form-group">
                <label for="activities" class="col-md-12">Activities
                    <a class="btn btn-success pull-right" data-toggle="modal" data-target="#destActivitySelect">Add
                        Activity</a>
                </label>
                <div id="activities" class="btn-toolbar">
                    <a class="btn btn-primary flow-btn" ng-repeat="activity in dest.detail.activities">
                        {{activity.category + '::' + activity.name}}
                        <span class="glyphicon glyphicon-remove"
                              ng-click="removeActivityFromDest(activity.id);"></span></a>
                </div>
            </div>

            <!-- Activity Select Modal -->
            <div id="destActivitySelect" class="modal fade" role="dialog">
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
                            <a type="button" class="btn btn-success" ng-click="addActivityToDest();">
                                <span class="glyphicon glyphicon-plus"></span> Add</a>
                            <a type="button" class="btn btn-primary" data-dismiss="modal">Done</a>
                        </div>
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
                    <input type="text" class="form-control"
                           id="addressTwo" ng-model='dest.address.lineTwo' placeholder="">
                </div>
            </div>

            <!-- Postal Code and City -->
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="postalCode">Postal Code</label>
                    <input type="text" class="form-control" ng-required="true"
                           id="postalCode" ng-model='dest.address.postalCode' placeholder="Postal Code">
                </div>

                <span class="col-md-6 form-group">
                    <label for="city">City</label>
                    <input type="text" class="form-control" ng-required="true"
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
            <hr>
            <div class="row">
                <div class="form-group col-md-12">
                    <div map-lazy-load="https://maps.google.com/maps/api/js"
                       map-lazy-load-params="{{googleMapsUrl}}" >
                        <ng-map default-style="true"
                                center="{{defaultCenter.lat() + ',' + defaultCenter.lng()}}"
                                zoom="{{defaultZoom}}">
                        </ng-map>
                    </div>
                </div>
            </div>

            <!-- Latitude and Longitude -->
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="latitude">Latitude</label>
                    <input type="text" class="form-control" ng-required="true"
                           id="latitude" ng-model='dest.latitude' placeholder="Latitude">
                </div>

                <span class="col-md-6 form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" class="form-control" ng-required="true"
                           id="longitude" ng-model='dest.longitude' placeholder="Longitude">
                </span>
            </div>

            <!-- Incomplete Fields Modal -->
            <div id="incompleteDestModal" class="modal fade" role="dialog">
                <div class="modal-sm centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Missing required fields!
                        </div>
                        <div class="modal-body">
                            An event requires a <b>name, a short description, a long description,
                                an image, a valid address and location and at least one associated activity.</b>
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-primary" data-dismiss="modal">Close</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save and Cancel Buttons -->
            <hr>
            <br>
            <div class="form-group text-center"> <!-- data-toggle="tab" href="#events-panel"-->
                <a class="btn btn-success" ng-click="onSaveDest();">
                    <span class="glyphicon glyphicon-cloud-upload"></span> Save</a>
                &nbsp;
                <a class="btn btn-danger" data-toggle="tab" href="#destination-panel" ng-click="onCancelDest();">
                    <span class="glyphicon glyphicon-remove"></span> Cancel</a>
            </div>

        </form>
    </div>
</div>