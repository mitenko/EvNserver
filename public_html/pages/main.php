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
    </ul>

    <div class="tab-content" ng-controller="RootCtrl">
        <?php
            require __PAGES__ . 'inc/events.php';
            require __PAGES__ . 'inc/destinations.php';
        ?>
    </div>
</div>
</body>
</html>