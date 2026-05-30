<?php
require_once ABSLPATHROOT . "models/slider.php";
require_once ABSLPATHROOT . 'models/properties.php';
require_once ABSLPATHROOT . 'models/property_attachment.php';
require_once ABSLPATHROOT . 'models/agents.php';
require_once ABSLPATHROOT . 'helper/html_helper.php';
require_once ABSLPATHROOT . 'library/kb_property_management.php';

$homeSliderSetting = new Slider();
$obj_property = new Properties();
$attachment = new PropertyAttachment();
$agents = new Agents();
$htmlHelper = new HtmlHelper();
$kbPropertyManagement = new KBPropertyManagement();

$slider_image_path = $HOMEPAGE_ROOT . KBConstant::UPLOAD_FILE_PATH . 'home_slider/';
$slider_data = $homeSliderSetting->getAll();
$agents_data = $agents->getAll();


$rowsPerPage = 10;

if (isset($_POST['paginatepgno']) && (int)$_POST['paginatepgno'] > 0) {
    $page = (int)$_POST['paginatepgno'];
} else {
    $page = 1;
}

// start fetching from this row number
$offset = ($page - 1) * $rowsPerPage;

// total entry

$search_result = $htmlHelper->prepareSql($_POST);
//pr($search_result);

$total_property = $search_result['total_property'];

$pagingLink = $search_result['pagingLink'];

$my_property = $search_result['my_property'];

$property_found = $search_result['property_found'];

//pr($_POST);

?>

<!-- Main Slider Start -->
<section class="main-slider-section">
    <ul class="main-slider slide">
        <?php if (!empty($slider_data)) { ?>
            <?php foreach ($slider_data as $slide) { ?>
                <li class="slide-item slide-item-bg">
                    <img src="<?= $HOMEPAGE_ROOT ?>/uploads/home_slider/<?= $slide['slider_image']; ?>" alt=""
                         style="height: 400px">
                    <div class="slide-caption">
                        <p class="slide-caption-desc"><?= $slide['slider_subtitle']; ?></p>
                        <h2 class="slide-caption-title"><?= $slide['slider_title']; ?></span></h2>
                        <a href="<?= $slide['target_link']; ?>" class="btn"><?= $slide['button_text']; ?></a>
                    </div>
                </li>
            <?php } ?>
        <?php } else { ?>
            <li class="slide-item slide-item-bg">
                <img src="<?= $HOMEPAGE_ROOT ?>/assets/images/slider/1.jpg" alt="" style="height: 400px">
                <div class="slide-caption">
                    <p class="slide-caption-desc">Get your dream Home</p>
                    <h2 class="slide-caption-title">welcome to our <span>Homy</span></h2>
                    <a href="#" class="btn">Learn More</a>
                </div>
            </li>
            <li class="slide-item slide-item-bg">
                <img src="<?= $HOMEPAGE_ROOT ?>/assets/images/slider/2.jpg" alt="" style="height: 400px">
                <div class="slide-caption">
                    <p class="slide-caption-desc">Get your dream Home</p>
                    <h2 class="slide-caption-title">We know your <span>dreams</span></h2>
                    <a href="#" class="btn">Learn More</a>
                </div>
            </li>
            <li class="slide-item slide-item-bg">
                <img src="<?= $HOMEPAGE_ROOT ?>/assets/images/slider/3.jpg" alt="" style="height: 400px">
                <div class="slide-caption">
                    <p class="slide-caption-desc">Get your dream Home</p>
                    <h2 class="slide-caption-title">Get Awesome <span>interior</span> design</h2>
                    <a href="#" class="btn">Learn More</a>
                </div>
            </li>
            <li class="slide-item slide-item-bg">
                <img src="<?= $HOMEPAGE_ROOT ?>/assets/images/slider/4.jpg" alt="" style="height: 400px">
                <div class="slide-caption">
                    <p class="slide-caption-desc">Get your dream Home</p>
                    <h2 class="slide-caption-title">Get Awesome <span>interior</span> design</h2>
                    <a href="#" class="btn">Learn More</a>
                </div>
            </li>
        <?php } ?>
    </ul>
</section>
<!-- Main Slider End -->

<!-- Main Search start from here -->
<section class="main-search-field mt10">
    <div class="container">
        <form id="main_search" class="form-horizontal" name="main_search"
              action="" method="POST">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="at-col-default-mar">
                        <input class="at-input" type="text" name="location" value="<?= $_POST['location'] ?>"
                               placeholder="Location">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="at-col-default-mar">
                        <select class="div-toggle" name="property_type" data-target=".my-info-1">
                            <option value="" data-show=".acitveon" selected>Type</option>
                            <option <?php if ($_POST['property_type'] == "sale") echo "selected"; ?> value="sale"
                                                                                                     data-show=".sale">
                                For Buy
                            </option>
                            <option <?php if ($_POST['property_type'] == "rent") echo "selected"; ?> value="rent"
                                                                                                     data-show=".rent">
                                For Rent
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="at-col-default-mar">
                        <input class="at-input" type="text" value="<?= $_POST['min_area'] ?>" name="min_area"
                               placeholder="Square feet Min">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="at-col-default-mar">
                        <input class="at-input" type="text" value="<?= $_POST['max_area'] ?>" name="max_area"
                               placeholder="Square feet Max">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="at-col-default-mar">
                        <select name="bedrooms">
                            <option value="" selected>Bedrooms</option>
                            <option <?php if ($_POST['bedrooms'] == 1) echo "selected"; ?> value="1">1</option>
                            <option <?php if ($_POST['bedrooms'] == 2) echo "selected"; ?> value="2">2</option>
                            <option <?php if ($_POST['bedrooms'] == 3) echo "selected"; ?> value="3">3</option>
                            <option <?php if ($_POST['bedrooms'] == 4) echo "selected"; ?> value="4">4</option>
                            <option <?php if ($_POST['bedrooms'] == 5) echo "selected"; ?> value="5">5</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="at-col-default-mar">
                        <select name="bathrooms">
                            <option value="" selected>Bathroom</option>
                            <option <?php if ($_POST['bathrooms'] == 1) echo "selected"; ?> value="1">1</option>
                            <option <?php if ($_POST['bathrooms'] == 2) echo "selected"; ?> value="2">2</option>
                            <option <?php if ($_POST['bathrooms'] == 3) echo "selected"; ?> value="3">3</option>
                            <option <?php if ($_POST['bathrooms'] == 4) echo "selected"; ?> value="4">4</option>
                            <option <?php if ($_POST['bathrooms'] == 5) echo "selected"; ?> value="5">5</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="at-col-default-mar">
                        <div class="at-pricing-range">
                            <div class="my-info-1">
                                <h4>At first select Property Status</h4>
                                <div class="acitveon sale hide">
                                    <label>Price : </label>
                                    <input type="text" name="price_min" class="amount at-input-price" readonly>
                                    <div class="slider-range"></div>
                                </div>
                                <div class="rent hide">
                                    <label>Price : </label>
                                    <input type="text" name="price_max" class="amount-two at-input-price" readonly>
                                    <div class="slider-range-two"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="at-col-default-mar">
                        <button class="btn btn-default hvr-bounce-to-right" type="submit">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<!-- Main Search End -->

<?php
if (($_SESSION['flash_message_success'] != '') || ($_SESSION['flash_message_error'] != '')) {
    if ($_SESSION['flash_message_success'] != '') {
        $msg_text2 = $_SESSION['flash_message_success'];
        $msg_type2 = "success";
        $circle_type2 = "fa fa-check-circle";
    } else {
        $msg_text2 = $_SESSION['flash_message_error'];
        $msg_type2 = "danger";
        $circle_type2 = "fa fa-times-circle";
    }
    ?>

    <div class="form_special_incentive">
        <div class="alert alert-<?= $msg_type2 ?> fade in">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <i class="<?= $circle_type2 ?>"></i> <?= $msg_text2 ?>
        </div>
    </div>

    <?php
    unset($_SESSION['flash_message_success']);
    unset($_SESSION['flash_message_error']);
}
?>

<!-- About start from here -->
<!--<section class="at-about-sec">
    <div class="container">
        <div class="row animatedParent animateOnce">
            <div class="col-lg-7 col-md-12">
                <div class="at-about-col at-col-default-mar">
                    <div class="at-about-title">
                        <h1>Few description<br> <span>about Homy</span></h1>
                        <h6>Real Estate</h6>
                    </div>
                    <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered
                        alteration in some form, by injected humour, or randomised words which don't look even slightly
                        believable. If you are going to use a passage of Lorem Ipsum.</p> <br>

                    <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered
                        alteration in some form, by injected humour, or randomised words which don't look even slightly
                        believable. If you are going to use a passage of Lorem Ipsum.</p>
                </div>
            </div>
            <div class="col-lg-5 hidden-md">
                <div class="at-about-col animated fadeInRightShort slow delay-250">
                    <img src="<? /*= $HOMEPAGE_ROOT */ ?>/assets/images/about/1.jpg" alt="">
                </div>
            </div>
        </div>
    </div>
</section>-->
<!-- About End -->

<!-- Call start from here -->
<!--<section class="at-Call-sec jarallax at-over-layer-black">
    <div class="at-Call-both-side clearfix">
        <div class="at-Call-left">
            <div class="at-inside-Call">
                <h5>BOOK YOUR</h5>
                <h2>APPARTMENT OR HOUSE</h2>
            </div>
        </div>
        <div class="at-Call-right">
            <div class="at-Call-right-inside">
                <h2>we are ready to receive your call</h2>
                <div class="at-short-line"></div>
                <h3><span>+0412 001 123</span></h3>
            </div>
        </div>
    </div>
</section>-->
<!-- Call End -->

<!-- Property start from here -->
<section class="at-property-sec">
    <div class="container">
        <div class="row animatedParent animateOnce">
            <?php
            $total_found_text = "Total Property Found";
            $attachment = new PropertyAttachment();
            if (!empty($my_property)) {

                require_once ABSLPATHROOT . 'models/property_whitelist.php';
                $propertyWhitelist = new PropertyWhitelist();
                $where = array(
                    'user_id' => $_SESSION['loggedin_userid']
                );
                $whiteListDataArray = $propertyWhitelist->getAll($where, 'property_id');

                $whiteListData = array_column($whiteListDataArray, 'property_id');

                foreach ($my_property as $property) {
                    $where = [
                        'property_id' => $property['id']
                    ];
                    $attachment_data = $attachment->get($where);

                    $whiteListTitle = "Add Watchlist";
                    $whiteListClass = "fa-heart-o";
                    $status_value = 0;
                    if (!empty($whiteListData)) {
                        if (in_array($property['id'], $whiteListData)) {
                            $whiteListTitle = "Remove Watchlist";
                            $whiteListClass = "fa-heart";
                            $status_value = 1;
                        }
                    }
                    ?>
                    <div id="whiteListStatus<?= $property['id'] ?>" style="display: none"><?= $status_value ?></div>
                    <div class="col-md-4 col-sm-6">
                        <div class="at-property-item at-col-default-mar animated fadeInUpShort slow">
                            <div class="at-property-img">
                                <?php if (is_array($attachment_data)) { ?>
                                    <img style="height: 300px"
                                         src="<?= $HOMEPAGE_ROOT ?>/uploads/property/Thumb/<?= $attachment_data['file_name']; ?>"
                                         alt="">

                                <?php } else { ?>
                                    <img style="height: 300px"
                                         src="<?= $HOMEPAGE_ROOT ?>/assets/images/property/7.jpg"
                                         alt="">
                                <?php } ?>
                                <div class="at-property-overlayer"></div>
                                <a class="btn btn-default at-property-btn"
                                   href="<?= $HOMEPAGE_ROOT ?>/index.php?page=property_details&property_id=<?= base64_encode($property['id']); ?>"
                                   role="button">View Details</a>
                                <h4 class="at-bg-black"><?= $property['property_type']; ?></h4>
                                <h5 class="at-bg-black">$<?= $property['price']; ?></h5>
                            </div>
                            <div class="at-property-dis">
                                <ul>
                                    <li><i class="fa fa-object-group"
                                           aria-hidden="true"></i> <?= $property['full_area']; ?></li>
                                    <li><i class="fa fa-bed" aria-hidden="true"></i> <?= $property['bedrooms']; ?>
                                    </li>
                                    <li><i class="fa fa-bath" aria-hidden="true"></i> <?= $property['bathrooms']; ?>
                                    </li>
                                    <?php if (isset($_SESSION['loggedin_userid'])) { ?>
                                        <li class="whitlist-area" onclick="addRemoveWhilteList(<?= $property['id'] ?>)">
                                            <i title="<?= $whiteListTitle ?>" id="whitelist_icon_<?= $property['id'] ?>"
                                               class="fa <?= $whiteListClass ?>" aria-hidden="true"></i>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="at-property-location">
                                <h4><i class="fa fa-home" aria-hidden="true"></i><a
                                            href="<?= $HOMEPAGE_ROOT ?>/index.php?page=property_details&property_id=<?= base64_encode($property['id']); ?>">
                                        <?= strlen($property['property_name']) >= 80 ? substr($property['property_name'], 0, 80) . ' ...' : $property['property_name']; ?>
                                    </a>
                                </h4>
                                <p><i class="fa fa-map-marker"
                                      aria-hidden="true"></i> <?= $property['property_location']; ?></p>
                                <p>Status: <?= $kbPropertyManagement->getPropertyStatus($property['status']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="alert alert-warning fade in">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <i class="fa fa-times-circle"></i> No Property Found
                </div>
            <?php } ?>


        </div>

        <form name="invoicefrm" id="invoicefrm" action="" method="post">
            <input type="hidden" name="paginatepgno" id="paginatepgno" value=""/>
        </form>
        <div class="col-lg-3" style="padding-top: 23px">
            <?php if ($total_property > 1) { ?>
                <p class="text-left">
                    <strong>
                        <?= $total_found_text ?>: <?php echo $total_property; ?>
                    </strong>
                </p>
            <?php } ?>
        </div>

        <div class="col-lg-9 switch-alignment" style="padding: 0">
            <?php
            if ($property_found > 0) {
                echo $pagingLink;
            }
            ?>
        </div>
    </div>
</section>
<!-- Property End -->

<!-- Agents start from here -->
<?php if (!empty($agents_data)) { ?>
    <section class="at-agents-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="at-sec-title">
                        <h2>Our valuable <span>Agents</span></h2>
                        <div class="at-heading-under-line">
                            <div class="at-heading-inside-line"></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="agent-carousel" data-slick='{"slidesToShow": 4, "slidesToScroll": 1}'>

                        <?php foreach ($agents_data as $agent) { ?>
                            <div class="at-agent-col">
                                <div class="at-agent-img">
                                    <img src="<?= $HOMEPAGE_ROOT ?>/uploads/agents/<?= $agent['agent_image'] ?>" alt="">
                                    <div class="at-agent-social">
                                        <a target="_blank" href="<?= $agent['facebook_link'] ?>"><i
                                                    class="fa fa-facebook" aria-hidden="true"></i></a>
                                        <a target="_blank" href="<?= $agent['twitter_link'] ?>"><i class="fa fa-twitter"
                                                                                                   aria-hidden="true"></i></a>
                                        <a target="_blank" href="<?= $agent['linkedin_link'] ?>"><i
                                                    class="fa fa-linkedin" aria-hidden="true"></i></a>
                                        <a target="_blank" href="<?= $agent['vimeo_link'] ?>"><i class="fa fa-vimeo"
                                                                                                 aria-hidden="true"></i></a>
                                        <div class="at-agent-call">
                                            <p><?= $agent['agent_phone'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="at-agent-info">
                                    <h4><a href="#"><?= $agent['agent_name'] ?></a></h4>
                                    <p><?= $agent['agent_title'] ?></p>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<!-- Agents End -->

<!-- Blog start from here -->

<!-- End -->

<!-- Newsletter start from here -->
<section class="at-newsletter-sec jarallax at-over-layer-black">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h2>subscribe <span>newsletter</span></h2>
                <p>To subscribe, please enter your email address below</p>
                <form id="subscriber-form" class="input-group" action="index.php?action=add_subscriber" method="POST">
                    <div class="form-group">
                        <input class="form-control" id="subscriber_email" name="subscriber_email" type="email">
                    </div>
                    <span class="input-group-btn">
                          <button class="btn btn-default at-sub-btn hvr-bounce-to-right" type="submit">
                            SUBSCRIBE
                          </button>
                    </span>
                </form>
                <div id="subscriber_error"></div>
            </div>
        </div>
    </div>
</section>
<!-- Newsletter end -->

<!-- Brand start from here -->

<!-- Brand End -->


<script>

    function getpaginatepage(pgno) {
        document.invoicefrm.paginatepgno.value = pgno;
        document.invoicefrm.submit();
    }

    $("#subscriber-form").validate({
        onkeyup: false,
        errorPlacement: function (error, element) {
            if (element.attr("name") == "subscriber_email") {
                $("#subscriber_error").html(error);

                if ($('.error').length == 0) {
                    errorImg.insertAfter(element);
                }
            } else {
                error.insertAfter(element);
            }
        },

        rules: {
            subscriber_email: {
                required: true
            }
        },
        messages: {
            subscriber_email: {
                required: "Email is required",
            }
        }
    });

    function addRemoveWhilteList(PID) {
        var option = $('#whiteListStatus' + PID).html();
        $.ajax({
            type: "POST",
            data: 'ajax_page=ajax_manager&PID=' + PID + '&action=addRemoveWhiteList&option=' + option,
            url: '<?php echo $HOMEPAGE_ROOT;?>/route.php',
            dataType: 'text',
            success: function (response) {
                response = parseInt(response);
                if (response === 0) {
                    showSuccessAlert('This property added watchlist');
                    $('#whiteListStatus' + PID).html(1);
                    $("#whitelist_icon_" + PID).removeClass("fa-heart-o").addClass("fa-heart");
                } else if (response === 1) {
                    showSuccessAlert('This property removed from watchlist');
                    $('#whiteListStatus' + PID).html(0);
                    $("#whitelist_icon_" + PID).removeClass("fa-heart").addClass("fa-heart-o");
                } else if (response === 2) {
                    showErrorAlert('This property failed to add watchlist');
                } else if (response === 3) {
                    showErrorAlert('This property already added watchlist');
                } else if (response === 4) {
                    showErrorAlert('This property is not watchlist');
                } else if (response === 5) {
                    showErrorAlert('This property failed to remove from watchlist');
                } else if (response === 6) {
                    showErrorAlert('You can add maximum' + <?=$add_limit?> + ' property for watchlist');
                }
            }
        });
    }

    function showErrorAlert(message) {
        // set message
        $('#msgBox').html(message);

        var alertDangerObj = $('.alert-danger');
        if (alertDangerObj.is(':hidden')) {
            alertDangerObj.css({opacity: 1});
            alertDangerObj.fadeIn();
        }

        // scroll top
        scrollToTop();
    }

    function showSuccessAlert(message) {
        // set message
        $('#successMsgBox').html(message);

        var alertSuccessObj = $('.alert-success');
        if (alertSuccessObj.is(':hidden')) {
            alertSuccessObj.css({opacity: 1});
            alertSuccessObj.fadeIn();
        }

        // scroll top
        scrollToTop();
    }

    function scrollToTop() {
        $('html,body').animate({scrollTop: $(".at-property-sec").offset().top});
    }
</script>