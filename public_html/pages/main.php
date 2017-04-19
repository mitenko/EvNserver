<html lang="en" ng-app="evnApp">
<head>
<title>Events Nanaimo Manager</title>
<?php
require __PAGES__ . 'inc/HeaderRequirements.php';
?>
</head>
<body>

<div class="col-md-10 col-md-offset-1">
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#" class="h3">Events Nanaimo Manager</a></li>
    </ul><br>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#events-panel" data-toggle="tab" class="h3" >Events</a></li>
        <li><a href="#destination-panel" data-toggle="tab" class="h3" >Destinations</a></li>
        <li><a href="#activity-panel" data-toggle="tab" class="h3" >Activities</a></li>
    </ul>

    <div class="tab-content" ng-controller="RootCtrl">
        <?php
            require __PAGES__ . 'inc/events.php';
            require __PAGES__ . 'inc/destinations.php';
            require __PAGES__ . 'inc/activities.php';
        ?>
    </div>

    <!-- Invalid Image Size Modal -->
    <div id="invalidImageModal" class="modal fade" role="dialog">
        <div class="modal-sm centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    Invalid Image Dimensions
                </div>
                <div class="modal-body">
                    The minimum ratio is 1:1 and the max width for images is 1024 px. Please resize your image.
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-primary" data-dismiss="modal">Okay</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>