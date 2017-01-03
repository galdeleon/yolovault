<?php
include "header.php";
if ($_SESSION["logged_in"]) {
    header("Location: /");
} else if (isset($_POST["username"]) && isset($_POST["password"])) {
    if (!register_user($_POST["username"], $_POST["password"])) {
        die("lol try to be more unique d00d");
    }

    header("Location: /?page=login");
} else {
?>
    <div class="container">
    <div class="row">
        <div class="col-md-6">
            <form class="form-horizontal" action="" method="POST">
            <fieldset>
                <div id="legend">
                <legend class="">Register</legend>
                </div>
                <div class="control-group">
                <label class="control-label" for="username">Username</label>
                <div class="controls">
                    <input id="username" name="username" placeholder="" class="form-control input-lg" type="text">
                    <p class="help-block">Username should be something cool</p>
                </div>
                </div>
                <div class="control-group">
                <label class="control-label" for="password">Password</label>
                <div class="controls">
                    <input id="password" name="password" placeholder="" class="form-control input-lg" type="password">
                    <p class="help-block">At least 8 characters, 2-3 numbers, one uppercase and one chinese symbol plz, kthx.</p>
                </div>
                </div>
                <div class="control-group">
                <label class="control-label" for="password_confirm">Password (Confirm)</label>
                <div class="controls">
                    <input id="password_confirm" name="password_confirm" placeholder="" class="form-control input-lg" type="password">
                    <p class="help-block">Please confirm password</p>
                </div>
                </div>
                <div class="control-group">
                <div class="controls">
                    <button class="btn btn-success">Register</button>
                </div>
                </div>
            </fieldset>
            </form>
        </div> 
    </div>
    </div>
<?php
}

?>