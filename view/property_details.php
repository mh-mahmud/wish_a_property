<?php
require_once ABSLPATHROOT . 'models/properties.php';
require_once ABSLPATHROOT . 'models/property_attachment.php';
require_once ABSLPATHROOT . 'models/comments.php';
require_once ABSLPATHROOT . 'library/kb_property_management.php';

$obj_property = new Properties();
$attachment = new PropertyAttachment();
$comments_obj = new Comments();
$kbPropertyManagement = new KBPropertyManagement();
$property_id = base64_decode($_GET['property_id']);
$where = array(
    'id' => $property_id
);
$my_property = $obj_property->get($where);
$where_att = [
    'property_id' => $my_property['id']
];
$attachment_data = $attachment->getAll($where_att);

// -- get latest 3 proprty
$new_property = $obj_property->getAll(['id' =>[ $my_property['id'], 'NOT IN']], 'id, property_name, property_description', 'id DESC', 0, 5);
// -- get comments data
$comments_data = $comments_obj->getAll(['property_id' => $my_property['id']]);
?>
    <style>
        .form-control {
            margin-bottom: 10px !important;
        }
        .star {
            color: red;
            font-size: 12px;
        }
    </style>

<!-- Inner page heading start from here -->
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

<!-- Property start from here -->
<section class="at-property-sec at-property-right-sidebar">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="at-property-details-col">
                    <?php if(!empty($attachment_data)) : ?>
                        <div id="myCarousel" class="carousel slide" data-ride="carousel">
                            <!-- Wrapper for slides -->
                            <div class="carousel-inner">

                                <?php for($i=0; $i<count($attachment_data); $i++) : ?>
                                    <div class="item <?php echo ($i==0) ? 'active' : ''; ?>">
                                        <img src="<?= $HOMEPAGE_ROOT ?>/uploads/property/<?= $attachment_data[$i]['file_name']; ?>" alt="">
                                        <div class="carousel-caption">
                                            <h2><?= $my_property['property_name']; ?></h2>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                                <!-- End Item -->

                            </div>
                            <!-- End Carousel Inner -->
                            <ul class="nav nav-pills nav-justified">

                                <?php if(count($attachment_data) >= 4) : for($i=0; $i<count($attachment_data); $i++) : ?>
                                    <li data-target="#myCarousel" data-slide-to="<?= $i; ?>" class="<?php echo ($i==0) ? 'active' : ''; ?>">
                                        <a href="#"><img src="<?= $HOMEPAGE_ROOT ?>/uploads/property/<?= $attachment_data[$i]['file_name']; ?>" alt="">
                                        </a>
                                    </li>
                                <?php endfor; endif; ?>

                            </ul>
                        </div>
                    <?php endif; ?>
                    <!-- End Carousel -->
                    <p><?= $my_property['property_description']; ?></p>
                    <div class="at-sec-title at-sec-title-left">
                        <h2>Property <span>Features</span></h2>
                        <div class="at-heading-under-line">
                            <div class="at-heading-inside-line"></div>
                        </div>

                    </div>
                    <div class="row at-property-features">
                        <div class="col-md-6 clearfix">
                            <ul>
                                <li>Property ID : <span class="pull-right"><?= $my_property['property_id']; ?></span>
                                </li>
                                <li>Full Area : <span class="pull-right"><?= $my_property['full_area']; ?></span></li>
                                <li>Flat Size : <span class="pull-right"><?= $my_property['flat_size']; ?> Sq Ft</span></li>
                                <li>Bedrooms : <span class="pull-right"><?= $my_property['bedrooms']; ?></span>
                                </li>
                                <li>Bathrooms : <span class="pull-right"><?= $my_property['bathrooms']; ?></span>
                                </li>
                                <li>Garages : <span class="pull-right"><?= $my_property['garages']; ?></span>
                                </li>
                                <li>swimming pool : <span class="pull-right"><?= ($my_property['swimming_pool'] ==1 ) ? 'Yes' : 'No'; ?></span>
                                </li>
                                <li>Party Rooms : <span class="pull-right"><?= ($my_property['party_rooms'] ==1 ) ? 'Yes' : 'No'; ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li>Status : <span class="pull-right"> <?= $kbPropertyManagement->getPropertyStatus($my_property['status']); ?></span>
                                </li>
                                <li>Kitchen : <span class="pull-right"><?= $my_property['kitchen']; ?></span>
                                </li>
                                <li>AC Rooms: <span class="pull-right"><?= $my_property['ac_rooms']; ?></span>
                                </li>
                                <li>Internet : <span class="pull-right"><?= ($my_property['internet'] ==1 ) ? 'Yes' : 'No'; ?></span>
                                </li>
                                <li>Cable TV : <span class="pull-right"><?= ($my_property['cable_tv'] ==1 ) ? 'Yes' : 'No'; ?></span>
                                </li>
                                <li>Balcony : <span class="pull-right"><?= ($my_property['balcony'] ==1 ) ? 'Yes' : 'No'; ?></span>
                                </li>
                                <li>Pool : <span class="pull-right"><?= ($my_property['pool'] ==1 ) ? 'Yes' : 'No'; ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- previous comment section was here -->

                    <div class="row">
                        <div class="at-form-area" style="margin-top: 50px;">
                            <div class="col-md-12">

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

                                <form id="comment_form" class="form-horizontal" name="comment_form" action="index.php?action=comment" method="POST">
                                    <input type="hidden" name="property_id" value="<?= $my_property['id'] ?>">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="name" placeholder="Your name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control" type="email" name="email" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <textarea class="form-control" name="message" rows="5" placeholder="Write message here"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-default hvr-bounce-to-right" type="submit">SUBMIT</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="at-sidebar at-col-default-mar">

                    <div class="at-latest-news">
                        <h3 class="at-sedebar-title">Comments (<?php echo !empty($comments_data) ? count($comments_data) : 0; ?>)</h3>
                        <?php if(!empty($comments_data)) : ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="at-comment-row">
                                        <?php for($i=0; $i<count($comments_data); $i++) : ?>
                                            <div class="at-comment-item">
                                                <img src="<?= $HOMEPAGE_ROOT ?>/assets/images/avatar.png" alt="">
                                                <h5><?= $comments_data[$i]['name']; ?></h5>
                                                <span><?= date("D j F Y g.iA", strtotime($comments_data[$i]['created_date'])); ?></span>
                                                <p><?= $comments_data[$i]['message']; ?></p>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="at-comment-row">
                                        <h3><a href="#">No Comment</a></h3>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>


                    <!--<div class="at-latest-news">
                        <h3 class="at-sedebar-title">latest property</h3>
                        <ul>
                            <?php /*for($i=0; $i<count($new_property); $i++) : */?>
                            <li>
                                <div class="at-news-item">
                                    <img src="<?/*= $HOMEPAGE_ROOT */?>/assets/images/about/wishaproperty.jpg" alt="">
                                    <h4><a href="<?/*= $HOMEPAGE_ROOT */?>/index.php?page=property_details&property_id=<?/*= $new_property[$i]['id']; */?>"><?/*= $new_property[$i]['property_name'] */?></a></h4>
                                    <p><?/*= $new_property[$i]['property_description'] */?></p>
                                </div>
                            </li>
                            <?php /*endfor; */?>
                        </ul>
                    </div>

                    <div class="at-preview">
                        <h3 class="at-sedebar-title">preview</h3>
                        <img src="<?/*= $HOMEPAGE_ROOT */?>/assets/images/about/1.png" alt="">
                    </div>
                </div>-->
            </div>
        </div>
    </div>
</section>
<!-- Property End -->

<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/comments_validation.js"></script>
