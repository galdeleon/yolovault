<!DOCTYPE html>
<html lang="en">
<head>
  <title>Vault</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/readable/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
      </button>
      <a class="navbar-brand" href="/">YOLO</a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav navbar-left">
        <li><a>You only live once, cause yolo</a></li>
<?php if (logged_in()) { ?>
        <!-- admins only <li><a href="/?page=leAdminPanel&debug">Admin Panel</a></li> -->
<?php } 
if (isset($_GET["debug"])) { 
?>
    <li><a href="/?page=debug&what=<?=isset($_GET["page"]) ? $_GET["page"] : "index" ?>">View source</a></li>
<?php } ?>
    </ul>
      <ul class="nav navbar-nav navbar-right">
        <?php 
            if (!logged_in()) {
        ?>
                <li><a href="/?page=login">Login</a></li>
                <li><a href="/?page=register">Register</a></li>
        <?php
            } else {
        ?>
                <li><a href="/?page=secret">Your Secret</a></li>
                <li><a href="/?page=profile">Your Profile</a></li>
                <li><a href="/?page=contact">Contact</a></li>
                <li><a href="/?page=logout">Logout</a></li>
        <?php
            }
        ?>
      </ul>
    </div>
  </div>
</nav>