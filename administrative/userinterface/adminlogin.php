<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
    <title>Login | Admin Management</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="<?=$HOMEPAGE_ROOT?>/administrative/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=$HOMEPAGE_ROOT?>/administrative/assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?=$HOMEPAGE_ROOT?>/administrative/assets/vendor/linearicons/style.css">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="<?=$HOMEPAGE_ROOT?>/administrative/assets/css/main.css">
    <!-- FOR DEMO PURPOSES ONLY. You should remove this in your project -->
    <link rel="stylesheet" href="<?=$HOMEPAGE_ROOT?>/administrative/assets/css/demo.css">
    <link href="<?= $HOMEPAGE_ROOT; ?>/assets/css/common.css" rel="stylesheet" type="text/css"/>
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <!-- ICONS -->
    <link rel="apple-touch-icon" sizes="76x76" href="<?=$HOMEPAGE_ROOT?>/administrative/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?=$HOMEPAGE_ROOT?>/administrative/assets/img/favicon.png">
    <script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/jquery-1.20.2.min.js"></script>
</head>

<body>
<!-- WRAPPER -->
<div id="wrapper">
    <div class="vertical-align-wrap">
        <div class="vertical-align-middle">
            <div class="auth-box ">
                <div class="left">
                    <div class="content">
                        <?php if (!empty($_SESSION['login_error'])) { ?>
                            <div class="alert alert-danger fade in">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <i class="fa fa-times-circle"></i>
                                <?php echo $_SESSION['login_error'] ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="header">
                            <p class="lead">Login to your account</p>
                        </div>
                        <form class="form-auth-small" name="form_config"
                              action="index.php?action=admin_login" method="POST">
                            <div class="form-group">
                                <label for="signin-email" class="control-label sr-only">Username</label>
                                <input type="text" class="form-control" name="username" id="username" value="" placeholder="Username">
                            </div>
                            <div class="form-group">
                                <label for="signin-password" class="control-label sr-only">Password</label>
                                <input type="password" class="form-control" name="password" id="password" value="" placeholder="Password">
                            </div>

                            <div class="form-group">
                                <div class="col-sm-5" style="padding-left: 0">
                                    <input data-toggle="tooltip" tabindex="3"
                                           data-original-title="Captcha"
                                           data-placement="top-rightc" type="text" placeholder="Enter Code" id="captcha"
                                           name="captcha" class="inputcaptcha form-control" value="">
                                </div>
                                <div class="col-sm-7">
                                    <img style="width:200px;border:1px solid #ddd;height: 33px" src="<?php echo captchaImage(); ?>" class="imgcaptcha" alt="captcha"/>
                                    <img style="width:20px" src="<?= $HOMEPAGE_ROOT; ?>/assets/images/refresh.jpg" alt="reload" class="refresh"/>
                                </div>

                            </div>
                            <div class="form-group clearfix">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END WRAPPER -->
</body>

</html>

<script>
    $(".refresh").on('click', function () {
        $.ajax({

            type: "POST",
            url: '<?php echo $HOMEPAGE_ROOT;?>/route.php?ajax_page=validate_signup',
            data: 'type=reload_captcha',
            dataType: 'text',
            success: function (text) {
                $(".imgcaptcha").attr("src", text);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            }
        });

    });
</script>
