<?php
require_once ABSLPATHROOT . 'helper/html_helper.php';
$htmlHelper = new HtmlHelper();

$search_data = "Please search for find data";
if(!empty($_POST)) {
    $search_result = $htmlHelper->prepareSqlForServiceProvider($_POST);

    $total_provider = $search_result['total_provider'];

    $pagingLink = $search_result['pagingLink'];

    $all_provider = $search_result['all_provider'];

    $provider_found = $search_result['provider_found'];
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
                <div class="col-lg-4 col-md-6">
                    <div class="at-col-default-mar">
                        <input class="at-input" type="text" name="service_name" value="<?= $_POST['service_name'] ?>"
                               placeholder="Service Provider">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="at-col-default-mar">
                        <select class="div-toggle" name="user_type" data-target=".my-info-1">
                            <option value="service_provider">
                                Find Service
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
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
            $total_found_text = "Total Agent Found";
            if (!empty($search_result['search_type']) && ($search_result['search_type'] == 'service_provider')) {
                $total_found_text = "Total Service Provider Found";
                if (!empty($all_provider)) {
                    foreach ($all_provider as $provider) {
                        ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="at-agent-col" style="border:1px solid #ddd">
                                <div class="at-agent-img">
                                    <img src="<?= $HOMEPAGE_ROOT ?>/assets/images/profile.png" alt="" style="height: 250px">
                                </div>
                                <div class="at-agent-info">
                                    <h4><a href="#"><?= $provider['first_name'] . ' ' . $provider['last_name'] ?></a></h4>
                                    <p>Service: <?= $provider['service_name']; ?></p>
                                    <p style="margin-top: 10px">Phone: <?= $provider['phone']; ?></p>
                                    <p style="margin-top: 10px">Email: <?= $provider['email']; ?></p>
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
            } else { ?>
                <div class="alert alert-warning fade in">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <i class="fa fa-times-circle"></i> <?=$search_data?>
                </div>
            <?php } ?>

        </div>

        <form name="invoicefrm" id="invoicefrm" action="" method="post">
            <input type="hidden" name="paginatepgno" id="paginatepgno" value=""/>
        </form>
        <div class="col-lg-3" style="padding-top: 23px">
            <?php if ($total_provider > 1) { ?>
                <p class="text-left">
                    <strong>
                        <?=$total_found_text?>: <?php echo $total_provider; ?>
                    </strong>
                </p>
            <?php } ?>
        </div>

        <div class="col-lg-9 switch-alignment" style="padding: 0">
            <?php
            if ($provider_found > 0) {
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