<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="images/favicon.png">

        <title><?php echo $pagetitle ?> : Gaming with Lemons</title>
        
        <!-- CSS -->
        <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="/css/stylesheet.css" rel="stylesheet">
    </head>
    <body>
      <!-- navbar -->
      <div class="navbar navbar-default navbar-fixed-top">
          <div class="container">
              <div class="navbar-header">
                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand clearfix" href="/">
                    <div class="pull-left"><img src="/images/gwl_masthead.png" /></div>
                    <div class="navbar-brandname pull-left">Gaming with Lemons</div>
                  </a>
              </div>
              <div class="navbar-collapse collapse">
                  <ul class="nav navbar-nav">
                      <li id="navHome"><a href="/">Home</a></li>
                      <li id="navBlog"><a href="/blog">Blog</a></li> 
                      <li id="navSearch"><a href="/search">Search</a></li>  
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <?php if($sessionUserID != null) { ?>
                      <li id="navUser"><a href="/user/<?php echo $sessionUserID; ?>"><?php echo $sessionUsername; ?></a></li>
                      <?php if($sessionAdmin == 1) { ?>
                      <li id="navAdmin"><a href="/admin">Admin</a></li>
                      <?php } ?>
                      <li id="navLogin"><a href="/logout">Logout</a></li>
                    <?php } else { ?>
                      <li id="navRegister"><a href="/register">Register</a></li>
                      <li id="navLogin"><a href="/login">Login</a></li>
                    <?php } ?>
                  </ul>
              </div>
          </div>
      </div>
      <div class="container">
        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Oh no!</h4>
              </div>
              <div class="modal-body" id="errorModalMessage"></div>
              <div class="modal-footer">
                <a href="#" class="btn btn-success" data-dismiss="modal" id="errorModalDismiss">Okie doke</a>
              </div>
            </div>
          </div>
        </div>