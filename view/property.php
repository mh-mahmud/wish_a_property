<?php
require_once ABSLPATHROOT . 'models/properties.php';
require_once ABSLPATHROOT . 'models/property_attachment.php';
require_once ABSLPATHROOT . 'models/users_search_list.php';
require_once ABSLPATHROOT . 'helper/html_helper.php';
require_once ABSLPATHROOT . 'library/kb_property_management.php';

$obj_property = new Properties();
$htmlHelper = new HtmlHelper();
$usersSearchList = new UsersSearchList();
$kbPropertyManagement = new KBPropertyManagement();

$search_data = "Please search for find data";
if(!empty($_POST)) {
    $search_result = $htmlHelper->prepareSqlForProperty($_POST, $MEMBERS);

    $total_property = $search_result['total_property'];

    $pagingLink = $search_result['pagingLink'];

    $my_property = $search_result['my_property'];

    $property_found = $search_result['property_found'];

    if(!empty($search_result['search_type']) && $search_result['search_type'] == 'agents'){
        $data = [
                'user_id' => $_SESSION['loggedin_userid'],
                'search_type' => 1,
                'type' => 'agents'
        ];
        $usersSearchList->save($data);
    } else {
        $data = [
            'user_id' => $_SESSION['loggedin_userid'],
            'type' => $_POST['property_type'],
            'search_type' => 0,
            'location' => !empty($_POST['location']) ? $_POST['location'] : null,
            'bedrooms' => !empty($_POST['bedrooms']) ? $_POST['bedrooms'] : 0
        ];
        $usersSearchList->save($data);
    }
}
$add_limit = KBConstant::WHITELIST_LIMIT_OTHERS;
if($MEMBERS['user_type'] == 'agents'){
    $add_limit = KBConstant::WHITELIST_LIMIT_AGENTS;
}
?>


<!-- Inner page heading start from here -->
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

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
                            <?php
                            if ($MEMBERS['user_type'] == 'service_provider') { ?>
                                <option value="" data-show=".acitveon" selected>Type</option>
                                <option <?php if ($_POST['property_type'] == "agents") echo "selected"; ?>
                                        value="agents"
                                        data-show=".rent">
                                    Find Agents
                                </option>
                                <option <?php if ($_POST['property_type'] == "sale") echo "selected"; ?> value="sale"
                                                                                                         data-show=".sale">
                                    For Buy
                                </option>
                            <?php } elseif ($MEMBERS['user_type'] == 'agents') { ?>
                                <option value="" data-show=".acitveon" selected>Type</option>
                                <option <?php if ($_POST['property_type'] == "sale") echo "selected"; ?> value="sale"
                                                                                                         data-show=".sale">
                                    For Buy
                                </option>
                                <option <?php if ($_POST['property_type'] == "rent") echo "selected"; ?> value="rent"
                                                                                                         data-show=".rent">
                                    For Rent
                                </option>
                                <option <?php if ($_POST['property_type'] == "sold") echo "selected"; ?> value="sold"
                                                                                                         data-show=".rent">
                                    Sold
                                </option>
                                <option <?php if ($_POST['property_type'] == "service_provider") echo "selected"; ?> value="service_provider"
                                                                                                         data-show=".sale">
                                    Service Provider
                                </option>
                            <?php } elseif ($MEMBERS['user_type'] == 'buyers') { ?>
                                <option <?php if ($_POST['property_type'] == "agents") echo "selected"; ?>
                                        value="agents"
                                        data-show=".rent">
                                    Find Agents
                                </option>
                                <option <?php if ($_POST['property_type'] == "sale") echo "selected"; ?> value="sale"
                                                                                                         data-show=".sale">
                                    For Sale
                                </option>
                            <?php } elseif ($MEMBERS['user_type'] == 'sellers') { ?>
                                <option <?php if ($_POST['property_type'] == "agents") echo "selected"; ?>
                                        value="agents"
                                        data-show=".rent">
                                    Find Agents
                                </option>
                                <option <?php if ($_POST['property_type'] == "sale") echo "selected"; ?> value="sale"
                                                                                                         data-show=".sale">
                                    For Sale
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
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
                        <button class="btn btn-default hvr-bounce-to-right" type="submit">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Property start from here -->
<section class="at-property-sec">
    <div class="container">

        <div class="alert alert-danger collapse">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <i class="fa fa-info-circle"></i>
            <span id="msgBox"></span>
        </div>

        <div class="alert alert-success collapse">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <i class="fa fa-info-circle"></i>
            <span id="successMsgBox"></span>
        </div>
        <div class="row animatedParent animateOnce">
            <?php
            $total_found_text = "Total Property Found";
            if (!empty($search_result['search_type']) && ($search_result['search_type'] == 'agents'|| $search_result['search_type'] == 'service_provider')) {
                $total_found_text = "Total Agents Found";
                if (!empty($my_property)) {
                    foreach ($my_property as $property) {
                        ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="at-property-item at-col-default-mar animated fadeInUpShort slow">
                                <div class="at-property-img">
                                    <img style="height: 300px"
                                         src="<?= $HOMEPAGE_ROOT ?>/assets/images/property/7.jpg"
                                         alt="">

                                    <div class="at-property-overlayer"></div>
                                    <a class="btn btn-default at-property-btn"
                                       href="#"
                                       role="button">View Details</a>
                                    <h4 class="at-bg-black"><?= $property['first_name'] . " " . $property['last_name']; ?></h4>
                                    <h5 class="at-bg-black">$<?= $property['phone']; ?></h5>
                                </div>
                                <div class="at-property-dis">
                                    <ul>
                                        <li><i class="fa fa-object-group"
                                               aria-hidden="true"></i> <?= $property['email']; ?></li>
                                        <li><i class="fa fa-bed" aria-hidden="true"></i> <?= $property['phone']; ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="at-property-location">
                                    <h4><i class="fa fa-home" aria-hidden="true"></i><a
                                                href="#"><?= $property['first_name'] . " " . $property['last_name']; ?></a>
                                    </h4>
                                    <p><i class="fa fa-map-marker"
                                          aria-hidden="true"></i> <?= $property['email']; ?></p>

                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else {
                    if(!empty($_POST)) {
                        $search_data = "No Agents Found";
                    }
                    ?>

                    <div class="alert alert-warning fade in">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <i class="fa fa-times-circle"></i> <?=$search_data?>
                    </div>

                <?php }
            } else {
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
                        if(!empty($whiteListData)) {
                            if (in_array($property['id'], $whiteListData)) {
                                $whiteListTitle = "Remove Watchlist";
                                $whiteListClass = "fa-heart";
                                $status_value = 1;
                            }
                        }

                        ?>
                        <div id="whiteListStatus<?=$property['id']?>" style="display: none"><?=$status_value?></div>
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
                                        <li class="whitlist-area" onclick="addRemoveWhilteList(<?=$property['id']?>)"><i title="<?=$whiteListTitle?>" id="whitelist_icon_<?=$property['id']?>" class="fa <?=$whiteListClass?>" aria-hidden="true"></i>
                                        </li>
                                    </ul>
                                </div>
                                <div class="at-property-location">
                                    <h4><i class="fa fa-home" aria-hidden="true"></i><a
                                                href="<?= $HOMEPAGE_ROOT ?>/index.php?page=property_details&property_id=<?= base64_encode($property['id']); ?>"><?= $property['property_name']; ?></a>
                                    </h4>
                                    <p><i class="fa fa-map-marker"
                                          aria-hidden="true"></i> <?= $property['property_location']; ?></p>
                                    <p>Status: <?= $kbPropertyManagement->getPropertyStatus($property['status']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else {
                    if(!empty($_POST)) {
                        $search_data = "No Property Found";
                    }
                    ?>
                    <div class="alert alert-warning fade in">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <i class="fa fa-times-circle"></i> <?=$search_data?>
                    </div>
                <?php }
            } ?>

        </div>
        <form name="invoicefrm" id="invoicefrm" action="" method="post">
            <input type="hidden" name="paginatepgno" id="paginatepgno" value=""/>
        </form>
        <div class="col-lg-3" style="padding-top: 23px">
            <?php if ($total_property > 1) { ?>
                <p class="text-left">
                    <strong>
                        <?=$total_found_text?>: <?php echo $total_property; ?>
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


<script>

    function getpaginatepage(pgno) {
        document.invoicefrm.paginatepgno.value = pgno;
        document.invoicefrm.submit();
    }

    function addRemoveWhilteList(PID) {
        var option = $('#whiteListStatus'+PID).html();
        $.ajax({
            type: "POST",
            data: 'ajax_page=ajax_manager&PID=' + PID + '&action=addRemoveWhiteList&option='+option,
            url: '<?php echo $HOMEPAGE_ROOT;?>/route.php',
            dataType: 'text',
            success: function (response) {
                response = parseInt(response);
                if (response === 0) {
                    showSuccessAlert('This property added watchlist');
                    $('#whiteListStatus'+PID).html(1);
                    $("#whitelist_icon_"+PID).removeClass("fa-heart-o").addClass("fa-heart");
                } else if (response === 1) {
                    showSuccessAlert('This property removed from watchlist');
                    $('#whiteListStatus'+PID).html(0);
                    $("#whitelist_icon_"+PID).removeClass("fa-heart").addClass("fa-heart-o");
                } else if (response === 2) {
                    showErrorAlert('This property failed to add watchlist');
                } else if (response === 3) {
                    showErrorAlert('This property already added watchlist');
                } else if (response === 4) {
                    showErrorAlert('This property is not watchlist');
                } else if (response === 5) {
                    showErrorAlert('This property failed to remove from watchlist');
                } else if (response === 6) {
                    showErrorAlert('You can add maximum'+ <?=$add_limit?> +' property for watchlist');
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