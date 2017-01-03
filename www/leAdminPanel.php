<?php
require_admin();

if ($_SERVER["REMOTE_ADDR"] !== "127.0.0.1") {
    die("only from localhost pls");
}

// export users
if (isset($_GET["ids"])) {
    $export = "";
    foreach(explode(",", $_GET["ids"]) as $id) {
        $export .= "\"" . implode("\",\"", array_values(get_user($id))) ."\"\n";
    } 

    header('Content-Disposition: attachment; filename="users.csv"');
    header('Content-Type: text/csv');
    header("Content-Length: " . strlen($export));
    echo $export;
    exit;

// import users
} else if (isset($_GET["users"])) {
    $import = "";
    // fuck it... who needs import... they better sign up themselves 
} else {
?>
<div class="container">
    <div class="row">
        <form class="form-horizontal">
            <legend>Export users</legend>


            <div class="form-group">
                <label class="col-md-4 control-label" for="ids">Users to export</label>
                <div class="col-md-4">
                <input type="hidden" name="page" value="leAdminPanel">
                <input type="text" name="ids"  class="form-control input" placeholder="IDs seperated by comma...">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" ></label>
                <div class="col-md-4">
                <button class="btn btn-primary">Export</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <form class="form-horizontal" action="/?page=leAdminPanel" method="POST">
            <legend>Import users</legend>


            <div class="form-group">
                <label class="col-md-4 control-label" for="username">Users to import</label>
                <div class="col-md-4">
                <textarea name="users"  class="form-control input" placeholder="CSV plzzz"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" ></label>
                <div class="col-md-4">
                <button class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php 
}