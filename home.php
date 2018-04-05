<?php
include('./classes/DB.php');
include('./classes/TESTLOGIN.php');
session_start();

if (!TESTLOGIN::isLoggedIn()) {
  header("Location: http://localhost/monosmash/login.php?set=1");
} else {
   $userid = TESTLOGIN::isLoggedIn();
   //header("Location: http://localhost/monosmash/youtube/ajax/youtube_feed_GET.php");
}
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M"
      crossorigin="anonymous">
    <style>
      .myhomenav {
        border-bottom: 1px solid #D3D3D3;
      }
      .myhomenav i {
        font-size: 30px;
      }
    </style>
  </head>

  <body>
    <div class="myhomenav">
      <nav class="navbar navbar-light bg-light justify-content-between">
        <a class="navbar-brand" href="#">
          <img src="img/planless.svg" width="40" height="40" alt="">
        </a>
        <form class="form-inline">
          <input class="form-control mr-sm-2" type="search" placeholder="Search">
        </form>
        <div>
          <a class="navbar-brand" href="settings.php">
            <i class="material-icons">settings</i>
          </a>
          <a class="navbar-brand" href="logout.php">
            <i class="material-icons">exit_to_app</i>
          </a>
        </div>
      </nav>
    </div>
    <div class="feed" id="feed">
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
      crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1"
      crossorigin="anonymous"></script>
    <script>
     $(document).ready(function () {
        $.ajax({
          type: "GET",
          dataType: "html",
          url: "./facebook/ajax/facebook_feed_GET.php",
          success: function (data) {
            $("#feed").append(data);
            console.log(data);
          }
        })
      });
/*      $(document).ready(function () {
      $.ajax({
        type: "GET",
        dataType: "html",
        url: "./twitter/ajax/twitter_feed_GET.php",
        success: function (data) {
          $("#feed").append(data);
          console.log(data);
        }
      })
    });  */
/*      $(document).ready(function () {
      $.ajax({
        type: "GET",
        dataType: "html",
        url: "./youtube/ajax/youtube_feed_GET.php",
        success: function (data) {
          $("#feed").append(data);
          console.log(data);
        }
      })
    });  */
    </script>
  </body>

  </html>