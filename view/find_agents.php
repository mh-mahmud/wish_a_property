<?php
require_once ABSLPATHROOT . 'models/agents.php';
require_once ABSLPATHROOT . 'helper/html_helper.php';

$obj_agents= new Agents();
$htmlHelper = new HtmlHelper();

$search_data = "Please search for find data";
if(!empty($_POST)) {
    $search_result = $htmlHelper->prepareSqlForAgents($_POST);

    $total_agents = $search_result['total_agents'];

    $pagingLink = $search_result['pagingLink'];

    $all_agents = $search_result['all_agents'];

    $agent_found = $search_result['agent_found'];
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
                        <input class="at-input" type="text" name="agent_name" value="<?= $_POST['agent_name'] ?>"
                               placeholder="Agent Name">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="at-col-default-mar">
                        <select class="div-toggle" name="agent_type" data-target=".my-info-1">
                            <option value="agents">
                                Find Agents
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
            if (!empty($search_result['search_type']) && ($search_result['search_type'] == 'agents')) {
                $total_found_text = "Total Agents Found";
                if (!empty($all_agents)) {
                    foreach ($all_agents as $agent) {
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
                                    <h4 class="at-bg-black"><?= $agent['first_name'] . " " . $agent['last_name']; ?></h4>
                                    <h5 class="at-bg-black">$<?= $agent['phone']; ?></h5>
                                </div>
                                <div class="at-property-dis">
                                    <ul>
                                        <li><i class="fa fa-object-group"
                                               aria-hidden="true"></i> <?= $agent['email']; ?></li>
                                        <li><i class="fa fa-bed" aria-hidden="true"></i> <?= $agent['phone']; ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="at-property-location">
                                    <h4><i class="fa fa-home" aria-hidden="true"></i><a
                                                href="#"><?= $agent['first_name'] . " " . $agent['last_name']; ?></a>
                                    </h4>
                                    <p><i class="fa fa-map-marker"
                                          aria-hidden="true"></i> <?= $agent['email']; ?></p>

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
            <?php if ($total_agents > 1) { ?>
                <p class="text-left">
                    <strong>
                        <?=$total_found_text?>: <?php echo $total_agents; ?>
                    </strong>
                </p>
            <?php } ?>
        </div>

        <div class="col-lg-9 switch-alignment" style="padding: 0">
            <?php
            if ($agent_found > 0) {
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