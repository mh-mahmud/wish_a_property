<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Property Sales</title>

    <?php require_once ABSLPATHROOT . 'assets/js/common_js.php'; ?>
    <!-- Bootstrap -->
    <link href="<?= $HOMEPAGE_ROOT ?>/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Needed CSS -->
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/icofont.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/bootstrap-dropdownhover.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/featherlight.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/featherlight.gallery.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/hover.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/flexslider.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/slick.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/animations.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/animate.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/morphext.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/jquery.mb.YTPlayer.min.css">
    <link type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/common.css" rel="stylesheet">

    <!-- Main stylesheet  -->
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/style.css">
    <!-- Responsive stylesheet  -->
    <link rel="stylesheet" type="text/css" href="<?= $HOMEPAGE_ROOT ?>/assets/css/responsive.css">
    <!-- Favicon -->
    <link href="<?= $HOMEPAGE_ROOT ?>/assets/images/favicon.png" rel="shortcut icon" type="image/png">
    <link href="<?= $HOMEPAGE_ROOT ?>/assets/images/apple-icon.png" rel="icon" type="image/png">
    <link href="<?= $HOMEPAGE_ROOT; ?>/assets/css/stylesheet-pure-css.css" media="screen"
          rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/jquery-1.20.2.min.js"></script>
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/jquery-migrate-1.2.1.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?=$HOMEPAGE_ROOT?>/assets/js/bootstrap.min.js"></script>
    <script src="<?= $HOMEPAGE_ROOT; ?>/assets/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/alertify.min.js"></script>
    <script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/jquery.validate.min.js"></script>
</head>

<body class="get-root-path" getRootPath="<?= $HOMEPAGE_ROOT ?>">
<!--<div id="preloader"></div>-->
<!-- Main Heder Start -->

<section class="at-main-herader-sec">
    <!-- Header top start -->
    <div class="at-header-topbar">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 at-full-wd480">
                    <p class="menu-font">
                        <span><i class="icofont icofont-ui-head-phone"></i> +000 111 222 333</span>
                        <span class="menu-info"><i class="icofont icofont-email"></i> <a href="#">info@wishaproperty.com</a></span>
                    </p>
                </div>


                <?php if (isset($_SESSION['loggedin_userid'])) { ?>
                    <div class="col-lg-5 col-lg-offset-3 col-md-6 col-md-offset-1 col-sm-6 col-xs-12 at-full-wd480">
                        <div class="at-sign-in-up clearfix up-menu-design">
                            <p>
                                <i class="icofont icofont-sign-in"></i><a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=account"> Welcome :  <?php echo $MEMBERS['first_name']; ?></a>
                            </p>
                            <p><i class="icofont icofont-pencil-alt-2"></i> <a href="<?= $HOMEPAGE_ROOT ?>/index.php?action=logout">Logout</a></p>
                            <?php if( access_control('change_password') ) { ?>
                                <p>
                                    <i class="fa fa-key"></i>
                                    <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=change_password">Change Password</a>
                                </p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-lg-2 col-lg-offset-6 col-md-3 col-md-offset-1 col-sm-3 col-xs-6 at-full-wd480">
                        <div class="at-sign-in-up clearfix">
                            <p><i class="icofont icofont-sign-in"></i><a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=login">sign in</a>
                            </p>
                            <p><i class="icofont icofont-pencil-alt-2"></i> <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=register">sign up</a>
                            </p>
                        </div>
                    </div>
                <?php } ?>


            </div>
        </div>
    </div>
    <!-- Header top end -->

   <?php
        require_once ABSLPATHROOT . 'models/newsticker.php';
        $newsticker = new Newsticker();
        $has_data = 0;
        $where = ['status' => 1];
        $getData = $newsticker->getAll($where, 'news_title');
        $all_marquee_data = [];
        if( !empty($getData) ) {
            foreach($getData as $val) {
                $all_marquee_data[] = $val['news_title'];
            }
            $all_marquee_data = implode(". ", $all_marquee_data);
            $has_data = 1;
        }

        if($has_data == 1) {
    ?>
            <app-notice-bar _ngcontent-ilu-c0="" _nghost-ilu-c6=""><!---->
                <div _ngcontent-ilu-c6="" class="notice-bar-area ng-star-inserted">
                    <div _ngcontent-ilu-c6="" class="notice-text">Notice</div>
                    <div _ngcontent-ilu-c6="" class="notice-lists">
                        <ul _ngcontent-ilu-c6=""><!---->
                            <li _ngcontent-ilu-c6="" class="ng-star-inserted"><?= $all_marquee_data; ?></li>
                        </ul>
                    </div>
                </div>
            </app-notice-bar>
    <?php } ?>

    <!-- Header navbar start -->
    <div class="at-navbar fixed-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <nav class="navbar navbar-default">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="<?= $HOMEPAGE_ROOT ?>"><img
                                        src="<?= $HOMEPAGE_ROOT ?>/assets/images/logo.png" alt="">
                            </a>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" data-hover="dropdown"
                             data-animations="fadeInUp">
                            <ul class="nav navbar-nav navbar-right">
                                <li <?= $active_home ?>>
                                    <a href="<?= $HOMEPAGE_ROOT ?>">Home</a>
                                </li>
                                <li <?= $active_about ?>><a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=about">About</a>
                                </li>

                                <li class="dropdown <?= $active_find_resource ?>">
                                    <a id="search" href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Find Resource <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                            <li <?= $active_find_agents ?> >
                                                <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=find_agents">Find Agents</a>
                                            </li>
                                            <li <?= $active_find_service ?> >
                                                <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=find_service_provider">Find Service</a>
                                            </li>
                                    </ul>
                                </li>


                                <?php if (isset($_SESSION['loggedin_userid'])) { ?>

                                    <li class="dropdown <?= $active_sub_property; ?>">
                                        <a id="property" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Properties <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                        <ul class="dropdown-menu">
                                            <?php if( access_control('add_property') ) { ?>
                                                <li <?= $active_add_property ?> >
                                                    <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=add_property">Add Property</a>
                                                </li>
                                            <?php } ?>
                                            <?php if( access_control('my_property') ) { ?>
                                                <li <?= $active_my_property ?> >
                                                    <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=my_property">My Property</a>
                                                </li>
                                            <?php } ?>
                                            <?php if( access_control('property') ) { ?>
                                                <li <?= $active_property ?> >
                                                    <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=property">Search Properties</a>
                                                </li>
                                            <?php } ?>
                                            <?php if( access_control('compare_property') ) { ?>
                                                <li <?= $active_compare_property ?> >
                                                    <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=compare_property">Compare Property</a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>

                                    <?php if( access_control('manage_service') || access_control('add_service') ) { ?>
                                        <li class="dropdown <?= $active_sub_service ?>">
                                            <a id="service" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Services <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                            <ul class="dropdown-menu">
                                                <?php if( access_control('add_service') ) { ?>
                                                    <li <?= $active_add_service ?> >
                                                        <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=add_service">Add Service</a>
                                                    </li>
                                                <?php } ?>
                                                <?php if( access_control('manage_service') ) { ?>
                                                    <li <?= $active_manage_service ?> >
                                                        <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=manage_service">Manage Service</a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>






                                    <?php if( access_control('profile') ) { ?>
                                        <li <?= $active_profile ?> >
                                            <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=profile">Profile</a>
                                        </li>
                                    <?php } ?>
                                    <?php if( access_control('my_watchlist') ) { ?>
                                        <li <?= $active_my_watchlist ?> >
                                            <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=my_watchlist">My Watchlist</a>
                                        </li>
                                    <?php } ?>

                                <?php } ?>

                                <li <?= $active_contact ?>>
                                    <a href="<?= $HOMEPAGE_ROOT ?>/index.php?page=contact">Contact</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header navbar end -->
</section>
<!-- Main Heder End -->

