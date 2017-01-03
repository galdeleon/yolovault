<?php
include("header.php");
require_login();

if (isset($_POST["token"]) && isset($_POST["fname"]) && isset($_POST["lname"])) {
    verify_csrf($_POST["token"]);
    update_user($_POST["fname"], $_POST["lname"]);

    header("Location: /?page=profile");
} else {
?>
<div class="container">
    <div class="row">
        <form class="form-horizontal" action="/?page=profile" method="POST">
            <legend>Edit profile</legend>
            <div class="form-group">
                <label class="col-md-4 control-label"></label>
                <div class="col-md-4">
                <p><h3><?= $_SESSION['username']?></h3></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="id">ID</label>
                <div class="col-md-4">
                <input type="text" name="id"  class="form-control input" value="<?= htmlspecialchars($_SESSION['id']) ?>" readonly>
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-4 control-label" for="username">First Name</label>
                <div class="col-md-4">
                <input type="text" name="fname"  class="form-control input" value="<?= htmlspecialchars($_SESSION['fname']) ?>" placeholder="first name">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="username">Last Name</label>
                <div class="col-md-4">
                <input type="text" name="lname" class="form-control input-md" value="<?= htmlspecialchars($_SESSION['lname'])?>" placeholder="last name">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" ></label>
                <div class="col-md-4">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                <button class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php 
}
?>