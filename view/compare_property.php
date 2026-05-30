<?php
require_once ABSLPATHROOT . 'models/properties.php';
require_once ABSLPATHROOT . 'models/property_attachment.php';
require_once ABSLPATHROOT . 'helper/html_helper.php';
require_once ABSLPATHROOT . 'library/kb_property_management.php';

$obj_property = new Properties();
$htmlHelper = new HtmlHelper();
$kbPropertyManagement = new KBPropertyManagement();

$search_result = $htmlHelper->prepareSqlForProperty($_POST, $MEMBERS);
//pr($search_result);

$total_property = $search_result['total_property'];

$pagingLink = $search_result['pagingLink'];

$my_property = $search_result['my_property'];

$property_found = $search_result['property_found'];

//pr($MEMBERS);

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
                                <h4 class="hide">At first select Property Status</h4>
                                <div class="acitveon sale ">
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

<!-- Property start from here -->
<section class="at-property-sec">
    <div class="container">
        <div class="row animatedParent animateOnce">
            <?php
            $total_found_text = "Total Property Found";
            if (!empty($search_result['search_type']) && $search_result['search_type'] == 'agents') {
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
                } else { ?>

                    <div class="alert alert-warning fade in">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <i class="fa fa-times-circle"></i> No Agents Found
                    </div>

                <?php }
            } else {
                $attachment = new PropertyAttachment();
                if (!empty($my_property)) {
                    foreach ($my_property as $property) {
                        $where = [
                            'property_id' => $property['id']
                        ];
                        $attachment_data = $attachment->get($where);
                        ?>
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
                } else { ?>
                    <div class="alert alert-warning fade in">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <i class="fa fa-times-circle"></i> No Property Found
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


<script>

    function getpaginatepage(pgno) {
        document.invoicefrm.paginatepgno.value = pgno;
        document.invoicefrm.submit();
    }

</script>