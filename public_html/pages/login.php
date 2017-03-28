<html lang="en">
<head>
    <title>Login</title>
    <?php
        require __PAGES__ . 'inc/HeaderRequirements.php';
    ?>
</head>
<body>
<?php
if (isset($invalid)) {?>
<div class="modal fade" id="invalidModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-sm centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Invalid Login</h4>
            </div>
            <div class="modal-body">
                <p>Please try again</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-primary">Okay</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<script type="text/javascript">
    $(window).on('load', function(){
        $('#invalidModal').modal('show');
    });
</script>
<?}?>
<br><br><br><br>
    <div class="centered row">
        <h2 style="text-align:center">Login</h2>
        <div class="col-md-pull-3">
            <div id="Border-Radius">
                <div class="well">
                    <form class="form-horizontal ng-pristine ng-valid" role="form" method="post">
                        <div class="form-group">
                            <label class="sr-only" for="login_email">Email address</label>
                            <input type="email" class="form-control" id="login_email" name="login_email" placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="login_password">Password</label>
                            <input type="password" class="form-control" id="login_password" name="login_password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary col-md-offset-4 col-md-4">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>