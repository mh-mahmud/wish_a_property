<?php
// Initialize admin user information
if (isset($_SESSION['admin_uid']) && $_SESSION['admin_uid'] != "") {
    require_once ABSLPATHROOT . 'models/admin_users.php';
    $adminUsers = new AdminUsers();

    $adminUserInfo = $adminUsers->get(['uid' => $_SESSION['admin_uid']]);

    // Check admin current status
    if ($adminUserInfo['status'] != 1) {
        if (isset($_SESSION['admin_uid'])) {
            session_destroy();
        }
        header("Location: index.php");
        exit;
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <title>Admin Management</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <?php require_once ABSLPATHROOT . 'assets/js/common_js.php'; ?>
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT ?>/administrative/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="<?= $HOMEPAGE_ROOT ?>/administrative/assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT ?>/administrative/assets/vendor/linearicons/style.css">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT ?>/administrative/assets/css/main.css">
    <!-- FOR DEMO PURPOSES ONLY. You should remove this in your project -->
    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT ?>/administrative/assets/css/demo.css">
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">

    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT; ?>/assets/css/alertify.core.css"/>
    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT; ?>/assets/css/alertify.default.css"/>
    <link href="<?= $HOMEPAGE_ROOT; ?>/assets/css/bootstrap-select.min.css" media="screen"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT; ?>/assets/css/datatable_blue.css"
          title="currentStyle"/>
    <link href="<?= $HOMEPAGE_ROOT; ?>/assets/css/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="<?= $HOMEPAGE_ROOT; ?>/assets/css/common.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT; ?>/assets/css/lightbox.css">
    <link href="<?= $HOMEPAGE_ROOT; ?>/assets/css/stylesheet-pure-css.css" media="screen"
          rel="stylesheet" type="text/css">

    <!-- ICONS -->
    <link rel="apple-touch-icon" sizes="76x76" href="<?= $HOMEPAGE_ROOT ?>/administrative/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= $HOMEPAGE_ROOT ?>/administrative/assets/img/favicon.png">

    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/jquery-1.20.2.min.js"></script>
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/jquery-ui.js"></script>
    <script src="<?= $HOMEPAGE_ROOT; ?>/assets/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT ?>/assets/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/alertify.min.js"></script>
    <script src="<?= $HOMEPAGE_ROOT; ?>/assets/js/lightbox.min.js"></script>
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/editor/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/common.js"></script>

</head>
<body class="get-root-path" getRootPath="<?= $HOMEPAGE_ROOT ?>">
<!-- WRAPPER -->
<div id="wrapper">
    <!-- NAVBAR -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="brand">
            <a href="<?= $HOMEPAGE_ROOT; ?>/administrative/index.php"><img style="width: 139px;height: 21px"
                                                                           src="<?= $HOMEPAGE_ROOT ?>/assets/images/logo.png"
                                                                           alt="Property"
                                                                           class="img-responsive logo"></a>
        </div>
        <div class="container-fluid">
            <div class="navbar-btn">
                <button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-arrow-left-circle"></i></button>
            </div>
            <div id="navbar-menu">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img
                                    src="<?= $HOMEPAGE_ROOT ?>/administrative/assets/img/user.png" class="img-circle"
                                    alt="Avatar"> <span><?=ucwords($adminUserInfo['username'])?></span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="lnr lnr-user"></i> <span>My Profile</span></a></li>
                            <li><a href="<?= $HOMEPAGE_ROOT ?>/administrative/index.php?action=admin_logout"><i
                                            class="lnr lnr-exit"></i> <span>Logout</span></a></li>
                        </ul>
                    </li>
                    <!-- <li>
                        <a class="update-pro" href="https://www.themeineed.com/downloads/klorofil-pro-bootstrap-admin-dashboard-template/?utm_source=klorofil&utm_medium=template&utm_campaign=KlorofilPro" title="Upgrade to Pro" target="_blank"><i class="fa fa-rocket"></i> <span>UPGRADE TO PRO</span></a>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>