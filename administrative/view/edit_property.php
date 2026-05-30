<?php
if ($_GET['id'] > 0) {
    require_once ABSLPATHROOT . 'models/country.php';
    require_once ABSLPATHROOT . 'models/properties.php';
    require_once ABSLPATHROOT . 'models/property_attachment.php';

    $countryObj = new Country();
    $obj_property = new Properties();
    $propertyAttachment = new PropertyAttachment();

    $property_id = trim($_GET['id']);
    $where = array(
        'id' => $property_id
    );
    $data = $obj_property->get($where);

    if (empty($data)) {
        redirect($HOMEPAGE_ROOT . '/administrative/index.php');
    }

    $attachmentImage = $propertyAttachment->getAll(['property_id' => $property_id]);
    ?>

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
    <link rel="stylesheet" href="<?= $HOMEPAGE_ROOT ?>/administrative/assets/css/property/add_property.css"/>
    <div class="panel-heading">
        <h3 class="panel-title">Edit Property</h3>
    </div>
    <div class="panel-body custom_forms">
        <form id="property_form" class="form-horizontal label-left" name="property_form"
              action="index.php?action=updateUserProperty" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id" value='<?php echo $property_id; ?>'>
            <input type="hidden" name="old_property_status" id="old_property_status" value='<?php echo $data['status']; ?>'>
            <input type="hidden" name="deleted_images" id="deleted_images" value="">

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Property Name:</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" name="property_name"
                           value="<?= $data['property_name']; ?>"
                           id="edit_property_name" placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Description:</label>
                <div class="col-sm-9 required">
                    <textarea name="property_description" id="property_description" class="form-control" cols="50"
                              rows="10"><?= $data['property_description']; ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Location:</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" name="property_location"
                           value="<?= $data['property_location']; ?>"
                           id="property_location" placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Property Type:</label>
                <div class="col-sm-9 required">
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
                <label for="signup-firstname" class="col-sm-3 control-label">Price:</label>
                <div class="col-sm-9 required">
                    <input type="number" class="form-control" name="price"
                           value="<?= $data['price']; ?>" id="price" placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Property ID:</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" name="property_id"
                           value="<?= $data['property_id']; ?>" id="property_id"
                           placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Phone:</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" name="phone" value="<?= $data['phone']; ?>"
                           id="phone" placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Email:</label>
                <div class="col-sm-9 required">
                    <input type="email" class="form-control" name="email" value="<?= $data['email']; ?>"
                           id="email" placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Full Area:</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" name="full_area"
                           value="<?= $data['full_area']; ?>" id="full_area"
                           placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Flat Size <br><span style="font-size:12px;">(square feet)</span>:</label>
                <div class="col-sm-9 required">
                    <input type="number" class="form-control" name="flat_size"
                           value="<?= $data['flat_size']; ?>" id="flat_size"
                           placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Bedrooms:</label>
                <div class="col-sm-9 required">
                    <input type="number" min="1" max="10" class="form-control" name="bedrooms"
                           value="<?= $data['bedrooms']; ?>" id="bedrooms"
                           placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Bathrooms:</label>
                <div class="col-sm-9 required">
                    <input type="number" min="1" max="5" class="form-control" name="bathrooms"
                           value="<?= $data['bathrooms']; ?>" id="bathrooms"
                           placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Garages:</label>
                <div class="col-sm-9">
                    <input type="number" min="1" max="5" class="form-control" name="garages"
                           value="<?= $data['garages'] > 0 ? $data['garages'] :""; ?>" id="garages"
                           placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Swimming Pool:</label>
                <div class="col-sm-9">
                    <select class="form-control" name="swimming_pool" id="swimming_pool">
                        <option value="0">Select Swimming Pool</option>
                        <option value="1" <?= ($data['swimming_pool'] == 1) ? "selected=selected" : ""; ?>>
                            Yes
                        </option>
                        <option value="0" <?= ($data['swimming_pool'] == 0) ? "selected=selected" : ""; ?>>
                            No
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Select Party Rooms:</label>
                <div class="col-sm-9">
                    <select class="form-control" name="party_rooms" id="party_rooms">
                        <option value="0">Select Party Rooms</option>
                        <option value="1" <?= ($data['party_rooms'] == 1) ? "selected=selected" : ""; ?>>
                            Yes
                        </option>
                        <option value="0" <?= ($data['party_rooms'] == 0) ? "selected=selected" : ""; ?>>
                            No
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="property_status" class="col-sm-3 control-label">Property Status:</label>
                <div class="col-sm-9">
                    <select class="form-control" name="property_status" id="property_status">
                        <option value="0"  <?php if ($data['status'] == 0) echo "selected"; ?>>Available</option>
                        <option value="2"  <?php if ($data['status'] == 2) echo "selected"; ?>>Under Construction </option>
                        <option value="1"  <?php if ($data['status'] == 1) echo "selected"; ?>>Sold </option>
                        <option value="3"  <?php if ($data['status'] == 3) echo "selected"; ?>>Under demolition </option>
                        <option value="4"  <?php if ($data['status'] == 4) echo "selected"; ?>>Under renovation </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Kitchen:</label>
                <div class="col-sm-9">
                    <input type="number" min="1" max="5" class="form-control" name="kitchen"
                           value="<?= $data['kitchen'] > 0 ? $data['kitchen'] :""; ?>" id="kitchen"
                           placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Ac Rooms:</label>
                <div class="col-sm-9">
                    <input type="number" min="1" max="10" class="form-control" name="ac_rooms"
                           value="<?= $data['ac_rooms']; ?>" id="ac_rooms"
                           placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Internet:</label>
                <div class="col-sm-9">
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
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Cable TV:</label>
                <div class="col-sm-9">
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
                <label for="signup-firstname" class="col-sm-3 control-label">Balcony:</label>
                <div class="col-sm-9">
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
            </div>

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Pool:</label>
                <div class="col-sm-9">
                    <select class="form-control" name="pool" id="pool">
                        <option value="0">Select Pool</option>
                        <option value="1" <?= ($data['pool'] == 1) ? "selected=selected" : ""; ?>>Yes
                        </option>
                        <option value="0" <?= ($data['pool'] == 0) ? "selected=selected" : ""; ?>>No
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">Status: </label>
                <div class="col-sm-9 required">
                    <div class="col-sm-4">
                        <input type="radio" name="activated" value="1"
                               id="active" <?= ($data['activated'] == 1) ? "checked='checked'" : "" ?>>
                        <label for="active"><span><span></span></span>&nbsp;Active </label>
                    </div>
                    <div class="col-sm-4">
                        <input type="radio" name="activated" value="0"
                               id="inactive" <?= ($data['activated'] == 0) ? "checked='checked'" : "" ?>>
                        <label for="inactive"><span><span></span></span>&nbsp;Inactive </label>
                    </div>
                    <div class="col-sm-4">
                        <input type="radio" name="activated" value="2"
                               id="pending" <?= ($data['activated'] == 2) ? "checked='checked'" : "" ?>>
                        <label for="pending"><span><span></span></span>&nbsp;Pending </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">Type: </label>
                <div class="col-sm-9">
                    <div class="col-sm-4">
                        <input type="radio" name="business_type" value="1"
                               id="classified" <?= ($data['business_type'] == 1) ? "checked='checked'" : "" ?>>
                        <label for="classified"><span><span></span></span>&nbsp;Classified </label>
                    </div>
                    <div class="col-sm-4">
                        <input type="radio" name="business_type" value="2"
                               id="latest" <?= ($data['business_type'] == 2) ? "checked='checked'" : "" ?>>
                        <label for="latest"><span><span></span></span>&nbsp;Latest </label>
                    </div>
                    <div class="col-sm-4">
                        <input type="radio" name="business_type" value="4"
                               id="premium" <?= ($data['business_type'] == 4) ? "checked='checked'" : "" ?>>
                        <label for="premium"><span><span></span></span>&nbsp;Premium</label>
                    </div>
                </div>
            </div>

            <div class="form-group browse-section">
                <label class="col-sm-3 control-label">Property Image:</label>
                <div class="col-sm-9 required">
                    <?php
                    $product_image_path = '/uploads/property';
                    $imagepath = $HOMEPAGE_ROOT . $product_image_path . '/Icon/';
                    $thumbimagepath = $HOMEPAGE_ROOT . $product_image_path . '/Thumb/';
                    if ($attachmentImage > 0) {
                        $i = 1;
                        foreach ($attachmentImage as $prdImag) {
                            ?>
                            <div id="rowCount<?= $i ?>" class="property_image_area">
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
                                               style="border:none;"></div>
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
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-fullrounded center-block"><i
                        class="fa fa-check-circle"></i>
                <span>Update</span>
            </button>
        </form>
    </div>
<?php } ?>
<?php require_once ABSLPATHROOT . "administrative/validator/property_validation.php"; ?>
