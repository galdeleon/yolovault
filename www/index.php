<?php 
include("functions.php");

if (isset($_GET["page"])) {
    switch ($_GET["page"]) {
        case "debug":
            if (isset($_GET["what"])) {
                download($_GET["what"]);
            }
            break;
        case "profile":
        case "secret":
        case "contact":
        case "logout":
        case "login":
        case "register":
        case "leAdminPanel":
            include $_GET["page"].".php";
            break;
        default:
            header("Location: /");
            break;
    }
} else {
    include("header.php");
?>


<div class="container">
    <div class="row">
        <div class="col-md-6">
            <ul class="list-group">
            </ul>
        </div>
    </div>
</div>
    <div class="jumbotron text-center">
    <h1>YOLO VAULT</h1>
    <p>-- under construction --</p>
    <p></p>

    </div>
    <div class="container">
    <div class="row">
        <h3>full features coming soon...</h3>
    <?php if (logged_in()) { ?>
        <p>Until then,  store one secret <a href="?page=secret">here</a> !</p>
    <?php } else { ?>
        <p>Making your secrets secret again, cause you only live once d00d. <a href="?page=register">register now!</a> </p>
    <?php } ?>
    </div>
<?php } ?>

</body>
</html>
