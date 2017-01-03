<?php
include("header.php");
require_login();

if (isset($_POST["token"]) && isset($_POST["secret"])) {
    verify_csrf($_POST["token"]);
    update_secret($_POST["secret"]);

    header("Location: /?page=secret");
} else {
?>
<div class="container">
    <div class="row">
        <form class="form-horizontal" action="/?page=secret" method="POST">
            <legend>Vault</legend>

            <div class="form-group">
                <label class="col-md-4 control-label" for="username">Secret</label>
                <div class="col-md-4">
                <textarea name="secret" class="form-control" placeholder="it's safe, trust me"><?= htmlspecialchars($_SESSION['secret']) ?></textarea>
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