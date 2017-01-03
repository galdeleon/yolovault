<?php
include("header.php");

if (isset($_POST["link"]) && isset($_POST["g-recaptcha-response"])) {
    verify_captcha($_POST["g-recaptcha-response"]);

    preg_match_all('!https?://\S+!', $_POST["link"], $matches);
    $url = $matches[0];

    if (count($url) > 0) {
        $link_id = add_bot_link($url[0]);
        echo "Sent it! Admin will take a look asap! You can check <a href=\"?page=contact&status=${link_id}\">here</a> if he is busy at the moment or not!";
    } else {
        echo "<b>That is not a valid link stupido!</b>";
    }
} else if (isset($_GET["status"])) {
    $link_id = $_GET["status"];
    $link =  get_bot_link_details($link_id);
    if ($link["visited"]) {
        echo "<p>Admin has looked at your link {$link["when"]} seconds ago!</p>";
        echo "<p>In total you had to wait {$link["how_long"]} seconds for the admin.</p>";
    } else { 
        echo "<p>Admin is busy looking at other links!</p>";
        echo "<p>He still has <b>{$link["position"]}</b> links to check before visiting yours!</p>";
    }
} else {
?>
<div class="container">
    <div class="row">
        <form class="form-horizontal" action="/?page=contact" method="POST">
            <legend>Contact the almighty</legend>

            <div class="form-group">
                <label class="col-md-4 control-label" for="link">Hey admin, me super clever, me have feature idea:</label>
                <div class="col-md-4">
                <textarea class="form-control input" name="garbage_to_discard" placeholder="very important feature request details go here"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="link"></label>
                <div class="col-md-4">
                <input type="text" name="link"  class="form-control input" placeholder="Link with additional info goes here">
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-4 control-label" ></label>
                <div class="col-md-4">
                <button class="btn btn-primary">Send it!</button>
                </div>
                <div class="col-md-4">
                    <div class="g-recaptcha" data-sitekey="6Lf61g4UAAAAAAZee8H9ji-I3MeXAD6KBBeyaMpx"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php 
}
?>