<link rel="stylesheet" href="<?= $HOMEPAGE_ROOT ?>/assets/css/property/add_property.css"/>
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

<?php
if (isset($_SESSION['input_data']) && !empty($_SESSION['input_data'])) {
    $form_data = $_SESSION['input_data'];
    unset($_SESSION['input_data']);
}

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
                        <form id="property_form" class="form-horizontal" name="property_form"
                              action="index.php?action=add_property" enctype="multipart/form-data" method="POST">
                            <input type="hidden" name="images_count" id="images_count" value="1">

                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="property_name">Property Name: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="property_name" id="property_name"
                                           value="<?= $form_data['property_name'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="property_description">Description: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="property_description"
                                           id="property_description" value="<?= $form_data['property_description'] ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="property_location">Location: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="property_location"
                                           id="property_location"
                                           value="<?= $form_data['property_location'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="property_type">Type: <span class="star">*</span></label>
                                    <select class="form-control" name="property_type" id="property_type">
                                        <option value="">Select Property Type</option>
                                        <option value="rent" <?php if ($form_data['property_type'] == "rent") echo "selected"; ?>>
                                            For Rent
                                        </option>
                                        <option value="sale" <?php if ($form_data['property_type'] == "sale") echo "selected"; ?>>
                                            For Sale
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="price">Price: <span class="star">*</span></label>
                                    <input type="number" class="form-control" name="price" id="price"
                                           value="<?= $form_data['price'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="property_id">Property ID: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="property_id" id="property_id"
                                           value="<?= $form_data['property_id'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="phone">Phone: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="phone" id="phone"
                                           value="<?= $form_data['phone'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="email">Email: <span class="star">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                           value="<?= $form_data['email'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="email">Email: <span class="star">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                           value="<?= $form_data['email'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="full_area">Full Area: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="full_area" id="full_area"
                                           value="<?= $form_data['full_area'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="full_area">Flat Size <span style="font-size:12px;">(square feet)</span>:
                                        <span class="star">*</span></label>
                                    <input type="number" class="form-control" name="flat_size" id="flat_size"
                                           value="<?= $form_data['flat_size'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="bedrooms">Bedrooms: <span class="star">*</span></label>
                                    <input type="number" min="1" max="10" class="form-control" name="bedrooms"
                                           id="bedrooms"
                                           value="<?= $form_data['bedrooms'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="bathrooms">Bathrooms: <span class="star">*</span></label>
                                    <input type="number" min="1" max="5" class="form-control" name="bathrooms"
                                           id="bathrooms" value="<?= $form_data['bathrooms'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="garages">Garages: </label>
                                    <input type="number" min="1" max="5" class="form-control" name="garages"
                                           id="garages"
                                           value="<?= $form_data['garages'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="swimming_pool">Swimming Pool: </label>
                                    <select class="form-control" name="swimming_pool" id="swimming_pool">
                                        <option value="0">Select Swimming Pool</option>
                                        <option value="1" <?php if ($form_data['swimming_pool'] == "1") echo "selected"; ?>>
                                            Yes
                                        </option>
                                        <option value="0" <?php if ($form_data['swimming_pool'] == "0") echo "selected"; ?>>
                                            No
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="party_rooms">Select Party Rooms: </label>
                                    <select class="form-control" name="party_rooms" id="party_rooms">
                                        <option value="0">Party Rooms</option>
                                        <option value="1" <?php if ($form_data['party_rooms'] == "1") echo "selected"; ?>>
                                            Yes
                                        </option>
                                        <option value="0" <?php if ($form_data['party_rooms'] == "0") echo "selected"; ?>>
                                            No
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="property_status">Property Status: <span class="star">*</span></label>
                                    <select class="form-control" name="property_status" id="property_status">
                                        <option value="0" <?php if ($form_data['property_status'] == 0) echo "selected"; ?>>
                                            Available
                                        </option>
                                        <option value="2" <?php if ($form_data['property_status'] == "2") echo "selected"; ?>>
                                            Under Construction
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="kitchen">Kitchen: </label>
                                    <input type="number" min="1" max="5" class="form-control" name="kitchen"
                                           id="kitchen"
                                           value="<?= $form_data['kitchen'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="ac_rooms">Ac Rooms: </label>
                                    <input type="number" min="1" max="10" class="form-control" name="ac_rooms"
                                           id="ac_rooms"
                                           value="<?= $form_data['ac_rooms'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="internet">Internet: </label>
                                    <select class="form-control" name="internet" id="internet">
                                        <option value="0">Select Internet</option>
                                        <option value="1" <?php if ($form_data['internet'] == "1") echo "selected"; ?>>
                                            Yes
                                        </option>
                                        <option value="0" <?php if ($form_data['internet'] == "0") echo "selected"; ?>>
                                            No
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="cable_tv">Cable TV: </label>
                                    <select class="form-control" name="cable_tv" id="cable_tv">
                                        <option value="0">Select Cable TV</option>
                                        <option value="1" <?php if ($form_data['cable_tv'] == "1") echo "selected"; ?>>
                                            Yes
                                        </option>
                                        <option value="0" <?php if ($form_data['cable_tv'] == "0") echo "selected"; ?>>
                                            No
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="balcony">Balcony: </label>
                                    <select class="form-control" name="balcony" id="balcony">
                                        <option value="0">Select Balcony</option>
                                        <option value="1" <?php if ($form_data['balcony'] == "1") echo "selected"; ?>>
                                            Yes
                                        </option>
                                        <option value="0" <?php if ($form_data['balcony'] == "buyers") echo "selected"; ?>>
                                            No
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="pool">Pool: </label>
                                    <select class="form-control" name="pool" id="pool">
                                        <option value="0">Select Pool</option>
                                        <option value="1" <?php if ($form_data['pool'] == "1") echo "selected"; ?>>Yes
                                        </option>
                                        <option value="0" <?php if ($form_data['pool'] == "0") echo "selected"; ?>>No
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group browse-section">
                                    <label class="margb_5">Select File</label><br>
                                    <div class="fileUpload btn btn-defaultcustom btn-md">
                                        <span>Browse</span>
                                        <input type="file" name="files[]" id="file_1" class="upload" tabindex="12"
                                               onchange="showFileName(this)"/>
                                    </div>
                                    <input id="uploadFile_1" placeholder="Choose File" disabled="disabled"
                                           style="border:none;background-color:#fdfdfd;">

                                    <div>
                                        <div id="muliFileUpload"></div>
                                        <div class="pull-left custom_plus">
                                            <i onclick="addNewFile();" class="fa fa-2x fa-plus"
                                               style="margin-top:8px;cursor:pointer"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button class="btn btn-default hvr-bounce-to-right" name="submit" type="submit">Add
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