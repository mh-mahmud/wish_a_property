<?php
require_once ABSLPATHROOT . 'models/properties.php';
require_once ABSLPATHROOT . 'models/property_attachment.php';

$property_id = base64_decode($_GET['property_id']);

if (!isset($property_id) || filter_var($property_id, FILTER_VALIDATE_INT) === false) {
    redirect($HOMEPAGE_ROOT . '/index.php');
}

$obj_property = new Properties();
$propertyAttachment = new PropertyAttachment();

$where = array(
    'id' => $property_id,
    'user_id' => $_SESSION['loggedin_userid']
);
$data = $obj_property->get($where);

if (empty($data)) {
    redirect($HOMEPAGE_ROOT . '/index.php');
}

$attachmentImage = $propertyAttachment->getAll(['property_id' => $property_id]);
?>

    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT ?>/assets/css/property/add_property.css"/>
    <section id="at-inner-title-sec">
        <div class="container">
            <div class="row">
            </div>
        </div>
    </section>
    <!-- Inner page heading end -->

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

    <section class="at-account-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <form id="property_edit_form" class="form-horizontal" name="property_edit_form"
                                  action="index.php?action=edit_property&property_id=<?= $data['id']; ?>"
                                  enctype="multipart/form-data"
                                  method="POST">
                                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                <input type="hidden" name="deleted_images" id="deleted_images" value="">
                                <input type="hidden" name="old_property_status" id="old_property_status"
                                       value='<?php echo $data['status']; ?>'>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label for="property_name">Property Name: <span class="star">*</span></label>
                                        <input type="text" class="form-control" name="property_name"
                                               value="<?= $data['property_name']; ?>"
                                               id="edit_property_name" placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="property_description">Description: <span
                                                    class="star">*</span></label>
                                        <input type="text" class="form-control" name="property_description"
                                               value="<?= $data['property_description']; ?>" id="property_description"
                                               placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="property_location">Location: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="property_location"
                                           value="<?= $data['property_location']; ?>"
                                           id="property_location" placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="property_type">Type: <span class="star">*</span></label>
                                        <select class="form-control" name="property_type" id="property_type">
                                            <option value="">Select Property Type</option>
                                            <option value="rent" <?php echo ($data['property_type'] == 'rent') ? "selected=selected" : ""; ?>>
                                                For Rent
                                            </option>
                                            <option value="sale" <?php echo ($data['property_type'] == 'sale') ? "selected=selected" : ""; ?>>
                                                For Sale
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="price">Price: <span class="star">*</span></label>
                                    <input type="number" class="form-control" name="price"
                                           value="<?= $data['price']; ?>" id="price" placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="property_id">Property ID: <span class="star">*</span></label>
                                        <input type="text" class="form-control" name="property_id"
                                               value="<?= $data['property_id']; ?>" id="property_id"
                                               placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="phone">Phone: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="phone" value="<?= $data['phone']; ?>"
                                           id="phone" placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email">Email: <span class="star">*</span></label>
                                        <input type="email" class="form-control" name="email" value="<?= $data['email']; ?>"
                                               id="email" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="full_area">Full Area: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="full_area"
                                           value="<?= $data['full_area']; ?>" id="full_area"
                                           placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="full_area">Flat Size <span style="font-size:12px;">(square feet)</span>:
                                            <span class="star">*</span></label>
                                        <input type="number" class="form-control" name="flat_size"
                                               value="<?= $data['flat_size']; ?>" id="flat_size" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="bedrooms">Bedrooms: <span class="star">*</span></label>
                                    <input type="number" min="1" max="10" class="form-control" name="bedrooms"
                                           value="<?= $data['bedrooms']; ?>" id="bedrooms"
                                           placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bathrooms">Bathrooms: <span class="star">*</span></label>
                                        <input type="number" min="1" max="5" class="form-control" name="bathrooms"
                                               value="<?= $data['bathrooms']; ?>" id="bathrooms"
                                               placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="garages">Garages: </label>
                                    <input type="number" min="1" max="5" class="form-control" name="garages"
                                           value="<?= $data['garages'] > 0 ? $data['garages'] : ""; ?>" id="garages"
                                           placeholder="">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="swimming_pool">Swimming Pool: </label>
                                        <select class="form-control" name="swimming_pool" id="swimming_pool">
                                            <option value="0">Select Swimming Pool</option>
                                            <option value="1"
                                                    <?= ($data['swimming_pool'] == 1) ? "selected=selected" : ""; ?>>
                                                Yes
                                            </option>
                                            <option value="0"
                                                    <?= ($data['swimming_pool'] == 0) ? "selected=selected" : ""; ?>>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="party_rooms">Select Party Rooms: </label>
                                    <select class="form-control" name="party_rooms" id="party_rooms">
                                        <option value="0">Party Rooms</option>
                                        <option value="1"
                                                <?= ($data['party_rooms'] == 1) ? "selected=selected" : ""; ?>>
                                            Yes
                                        </option>
                                        <option value="0"
                                                <?= ($data['party_rooms'] == 0) ? "selected=selected" : ""; ?>>
                                            No
                                        </option>
                                    </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="property_status">Property Status: <span class="star">*</span></label>
                                        <select class="form-control" name="property_status" id="property_status">
                                            <option value="0" <?php if ($data['status'] == 0) echo "selected"; ?>>
                                                Available
                                            </option>
                                            <option value="2" <?php if ($data['status'] == 2) echo "selected"; ?>>Under
                                                Construction
                                            </option>
                                            <?php if ($data['activated'] == 1) { ?>
                                                <option value="1" <?php if ($data['status'] == 1) echo "selected"; ?>>Sold
                                                </option>
                                                <option value="3" <?php if ($data['status'] == 3) echo "selected"; ?>>Under
                                                    Demolition
                                                </option>
                                                <option value="4" <?php if ($data['status'] == 4) echo "selected"; ?>>Under
                                                    Renovation
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="kitchen">Kitchen: </label>
                                    <input type="number" min="1" max="5" class="form-control" name="kitchen"
                                           value="<?= $data['kitchen'] > 0 ? $data['kitchen'] : ""; ?>" id="kitchen"
                                           placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ac_rooms">Ac Rooms: </label>
                                        <input type="number" min="1" max="10" class="form-control" name="ac_rooms"
                                               value="<?= $data['ac_rooms']; ?>" id="ac_rooms"
                                               placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="internet">Internet: </label>
                                    <select class="form-control" name="internet" id="internet">
                                        <option value="0">Internet</option>
                                        <option value="1" <?= ($data['internet'] == 1) ? "selected=selected" : ""; ?>>
                                            Yes
                                        </option>
                                        <option value="0" <?= ($data['internet'] == 0) ? "selected=selected" : ""; ?>>
                                            No
                                        </option>
                                    </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cable_tv">Cable TV: </label>
                                        <select class="form-control" name="cable_tv" id="cable_tv">
                                            <option value="0">Cable TV</option>
                                            <option value="1" <?= ($data['cable_tv'] == 1) ? "selected=selected" : ""; ?>>
                                                Yes
                                            </option>
                                            <option value="0" <?= ($data['cable_tv'] == 0) ? "selected=selected" : ""; ?>>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                    <label for="balcony">Balcony: </label>
                                    <select class="form-control" name="balcony" id="balcony">
                                        <option value="0">Balcony</option>
                                        <option value="1" <?= ($data['balcony'] == 1) ? "selected=selected" : ""; ?>>
                                            Yes
                                        </option>
                                        <option value="0" <?= ($data['balcony'] == 0) ? "selected=selected" : ""; ?>>
                                            No
                                        </option>
                                    </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pool">Pool: </label>
                                        <select class="form-control" name="pool" id="pool">
                                            <option value="0">Pool</option>
                                            <option value="1" <?= ($data['pool'] == 1) ? "selected=selected" : ""; ?>>Yes
                                            </option>
                                            <option value="0" <?= ($data['pool'] == 0) ? "selected=selected" : ""; ?>>No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group browse-section" style="margin-left: 18px">

                                    <?php
                                    $product_image_path = '/uploads/property';
                                    $imagepath = $HOMEPAGE_ROOT . $product_image_path . '/Icon/';
                                    $thumbimagepath = $HOMEPAGE_ROOT . $product_image_path . '/Thumb/';
                                    if ($attachmentImage > 0) {
                                        $i = 1;
                                        foreach ($attachmentImage as $prdImag) {
                                            ?>
                                            <div id="rowCount<?= $i ?>">
                                                <div class="form-group">
                                                    <div class="custom-remove-icon"
                                                         id="image_content<?= $prdImag['id']; ?>">
                                                        <img id="<?= $prdImag['id']; ?>" class="upload-img"
                                                             src="<?php echo $thumbimagepath . $prdImag['file_name']; ?>"
                                                             align="absmiddle"
                                                             border="0"/>
                                                        <a id="img_remover_<?= $prdImag['id']; ?>"
                                                           href="javascript:void(0);"
                                                           onclick="removeProductImage('<?= $prdImag['id']; ?>', '<?= $prdImag['file_name']; ?>','<?= $i ?>');"><i
                                                                    class="fa fa-2x fa-remove cust_remove"></i></a>
                                                    </div>
                                                    <div class="col-sm-12 upload-attach-file-task">
                                                        <div class="fileUpload btn btn-defaultcustom btn-md"><span>Browse</span>
                                                            <input type="file" name="files[]" id="file_<?= $i ?>"
                                                                   value="<?= $prdImag['file_name'] ?>" class="upload"
                                                                   onchange="showFileNameEdit(this, '<?= $prdImag['file_name']; ?>')">
                                                        </div>
                                                        <input id="uploadFile_<?= $i ?>" placeholder="Choose File"
                                                               value=""
                                                               disabled="disabled"
                                                               style="border:none;background-color:#fdfdfd;"></div>
                                                </div>

                                            </div>
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>

                                    <input type="hidden" name="images_count" id="images_count" value="<?= $i ?>">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div id="muliFileUpload"></div>
                                            <div class="pull-left custom_plus" style="margin-top: 0 !important;">
                                                <i onclick="addNewFile();" class="fa fa-2x fa-plus"
                                                   style="margin-top:8px;cursor:pointer"></i>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="text-center">
                                    <button class="btn btn-default hvr-bounce-to-right" name="submit" type="submit">
                                        Update
                                        Property
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End -->

<?php require_once ABSLPATHROOT . "validator/property_validation.php"; ?>