<?php
require_once ABSLPATHROOT . 'models/properties.php';
require_once ABSLPATHROOT . 'models/property_attachment.php';
require_once ABSLPATHROOT . 'helper/html_helper.php';
require_once ABSLPATHROOT . 'library/kb_property_management.php';

$obj_property = new Properties();
$kbPropertyManagement = new KBPropertyManagement();
$rowsPerPage = 10;
$total_records = 0;

if (isset($_POST['paginatepgno']) && (int)$_POST['paginatepgno'] > 0) {
    $page = (int)$_POST['paginatepgno'];
} else {
    $page = 1;
}

// start fetching from this row number
$offset = ($page - 1) * $rowsPerPage;

// total entry

$where = array(
    'user_id' => $_SESSION['loggedin_userid']
);

$total_property = count($obj_property->getAll($where));
$pagingLink = HtmlHelper::globalPagingBox($total_property, $rowsPerPage);

$my_property = $obj_property->getAll($where, '*', '', $offset, $rowsPerPage);

$property_found = count($my_property);
?>


<!-- Inner page heading start from here -->
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

<!-- Property start from here -->
<section class="at-property-sec">
    <div class="container">
        <div class="row">

            <?php
            $attachment = new PropertyAttachment();
            if (!empty($my_property)) {
                foreach ($my_property as $property) {
                    $where = [
                        'property_id' => $property['id']
                    ];
                    $attachment_data = $attachment->get($where);
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="at-property-item at-col-default-mar">
                            <div class="at-property-img">
                                <?php if (is_array($attachment_data)) { ?>
                                    <img style="height: 300px" src="<?= $HOMEPAGE_ROOT ?>/uploads/property/Thumb/<?= $attachment_data['file_name']; ?>"
                                         alt="">

                                <?php } else { ?>
                                    <img style="height: 300px" src="<?= $HOMEPAGE_ROOT ?>/assets/images/property/7.jpg" alt="">
                                <?php } ?>
                                <div class="at-property-overlayer"></div>
                                <a class="btn btn-default at-property-btn"
                                   href="<?= $HOMEPAGE_ROOT ?>/index.php?page=my_property_details&property_id=<?= base64_encode($property['id']); ?>"
                                   role="button">View Details</a>
                                <h4 class="at-bg-black"><?= $property['property_type']; ?></h4>
                                <h5 class="at-bg-black">$<?= $property['price']; ?></h5>
                            </div>
                            <div class="at-property-dis">
                                <ul>
                                    <li><i class="fa fa-object-group"
                                           aria-hidden="true"></i> <?= $property['flat_size']; ?></li>
                                    <li><i class="fa fa-bed" aria-hidden="true"></i> <?= $property['bedrooms']; ?></li>
                                    <li><i class="fa fa-bath" aria-hidden="true"></i> <?= $property['bathrooms']; ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="at-property-location" style="height: 150px;max-height: 150px">
                                <h4><i class="fa fa-home" aria-hidden="true"></i><a
                                            href="<?= $HOMEPAGE_ROOT ?>/index.php?page=my_property_details&property_id=<?= base64_encode($property['id']); ?>"><?= $property['property_name']; ?></a>
                                </h4>
                                <p><i class="fa fa-map-marker"
                                      aria-hidden="true"></i> <?= $property['property_location']; ?></p>
                                <p><i class="fa fa-pencil" aria-hidden="true"></i> <a
                                            href="<?= $HOMEPAGE_ROOT ?>/index.php?page=edit_property&property_id=<?= base64_encode($property['id']); ?>">Edit</a>
                                </p>
                                <p>Status: <?= $kbPropertyManagement->getPropertyStatus($property['status']); ?></p>
                            </div>
                        </div>
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
                     Total Property Found:    <?php echo $total_property; ?>
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

</script>