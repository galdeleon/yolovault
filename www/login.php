<?php
include("header.php");
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $user_id = verify_password($_POST["username"], $_POST["password"]);
    if ($user_id !== FALSE) {
        $_SESSION["logged_in"] = true;
        $_SESSION["csrf"] = substr(md5( openssl_random_pseudo_bytes(32)), 0, 20);
        $_SESSION["id"] = $user_id;
    } else {
        die("Noooooope");
    }


    header("Location: /");
    exit;
} else if (logged_in()) {
    header("Location: /");
    exit;
} else {
?>
<div class="container">
  <div class="row">
    <div class="col-md-6">
        <form class="form-horizontal" action="/?page=login" method="POST">
          <fieldset>
            <div id="legend">
              <legend class="">Login</legend>
            </div>
            <div class="control-group">
              <label class="control-label" for="username">Username</label>
              <div class="controls">
                <input id="username" name="username" placeholder="" class="form-control input-lg" type="text">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="password">Password</label>
              <div class="controls">
                <input id="password" name="password" placeholder="" class="form-control input-lg" type="password">
                <p></p>
              </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <button class="btn btn-success">Login</button>
              </div>
            </div>
          </fieldset>
        </form>
    </div> 
</div>
<?php
}

?>