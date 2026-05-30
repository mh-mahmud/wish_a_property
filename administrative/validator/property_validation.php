<script type="text/javascript">

    $(document).ready(function () {

        //CKEDITOR.replace( 'property_description' );

        $.validator.addMethod("checkProperty", function (value, element) {
            var isSuccess = 0;
            $.ajax({
                url: rootPath + '/route.php?ajax_page=validate_signup',
                data: "type=check_property_exists&property_name=" + value + "&csrf_token=" + encodeURIComponent('<?php echo $csrf_encrypted; ?>'),
                dataType: 'text',
                type: 'POST',
                async: false,
                success: function (msg) {
                    // alert(msg)
                    if (msg == 0) {
                        isSuccess = true;
                    } else {
                        isSuccess = false;
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                }
            });
            return isSuccess;
        }, "Sorry, this property name is already exist");

        $.validator.addMethod("checkEditProperty", function (value, element) {
            var property_id = "<?php echo $_GET['property_id']; ?>";
            var isSuccess = 0;
            $.ajax({
                url: rootPath + '/route.php?ajax_page=validate_signup',
                data: "type=check_edit_property_exists&id=" + property_id + "&property_name=" + value + "&csrf_token=" + encodeURIComponent('<?php echo $csrf_encrypted; ?>'),
                dataType: 'text',
                type: 'POST',
                async: false,
                success: function (msg) {
                    // alert(msg)
                    if (msg == 0) {
                        isSuccess = true;
                    } else {
                        isSuccess = false;
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                }
            });
            return isSuccess;
        }, "Sorry, this property name is already exist");

        $("#property_form").validate({
            onkeyup: false,
            rules: {
                phone: {
                    required: true
                },
                email: {
                    required: true
                },
                property_name: {
                    required: true,
                    checkProperty: true
                },
                property_description: {
                    required: true
                },
                property_location: {
                    required: true
                },
                property_type: {
                    required: true
                },
                price: {
                    required: true
                },
                full_area: {
                    required: true
                },
                flat_size: {
                    required: true
                },
                property_id: {
                    required: true,
                },
                bedrooms: {
                    required: true,
                },
                bathrooms: {
                    required: true,
                },
                captcha: {
                    required: true
                }
            },
            messages: {
                phone: {
                    required: "Phone is required",
                },
                email: {
                    required: "Email is required",
                },
                property_name: {
                    required: "Property name is required."
                },
                property_description: {
                    required: "Property description is required",
                },
                property_location: {
                    required: "Property location is required",
                },
                property_type: {
                    required: "Property type is required",
                },
                price: {
                    required: "Property price is required",
                },
                full_area: {
                    required: "Full area is required",
                },
                flat_size: {
                    required: "Flat size is required",
                },
                property_id: {
                    required: "Property ID is required",
                },
                bedrooms: {
                    required: "Bed rooms is required",
                },
                bathrooms: {
                    required: "Bath rooms is required",
                },
                captcha: {
                    required: "Captcha is required",
                }
            }
        });

        $("#property_edit_form").validate({
            onkeyup: false,
            rules: {
                phone: {
                    required: true
                },
                email: {
                    required: true
                },
                property_name: {
                    required: true,
                    checkEditProperty: true
                },
                property_description: {
                    required: true
                },
                property_location: {
                    required: true
                },
                property_type: {
                    required: true
                },
                price: {
                    required: true
                },
                full_area: {
                    required: true
                },
                property_id: {
                    required: true,
                },
                bedrooms: {
                    required: true,
                },
                bathrooms: {
                    required: true,
                },
                captcha: {
                    required: true
                }
            },
            messages: {
                phone: {
                    required: "Phone is required",
                },
                email: {
                    required: "Email is required",
                },
                property_name: {
                    required: "Property name is required."
                },
                property_description: {
                    required: "Property description is required",
                },
                property_location: {
                    required: "Property location is required",
                },
                property_type: {
                    required: "Property type is required",
                },
                price: {
                    required: "Property price is required",
                },
                full_area: {
                    required: "Full area is required",
                },
                property_id: {
                    required: "Property ID is required",
                },
                bedrooms: {
                    required: "Bed rooms is required",
                },
                bathrooms: {
                    required: "Bath rooms is required",
                },
                captcha: {
                    required: "Captcha is required",
                }
            }
        });
    });

    $(".refresh").on('click', function () {
        $.ajax({

            type: "POST",
            url: rootPath + '/route.php?ajax_page=validate_signup',
            data: 'type=reload_captcha',
            dataType: 'text',
            success: function (text) {
                $(".imgcaptcha").attr("src", text);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            }
        });

    });


    function showFileName(obj) {

        var current_element_number = obj.id.match(/\d+/);
        var btn_val = $(obj).val();
        $('#uploadFile_' + current_element_number).val(btn_val);
    }

    function showFileNameEdit(obj, imageSource) {

        var current_element_number = obj.id.match(/\d+/);
        var btn_val = $(obj).val();
        $('#uploadFile_' + current_element_number).val(btn_val);

        var deleted_images = $('#deleted_images').val();
        if (deleted_images == "" || deleted_images == null) {
            $('#deleted_images').val(imageSource);
        } else {
            deleted_images = deleted_images + "," + imageSource;
            $('#deleted_images').val(deleted_images);
        }
    }


    // add multiple file upload start

    //var rowCount = 1;

    var rowCount = $('#images_count').val();

    function addNewFile() {
        rowCount++;
        if (rowCount > 6) {
            alert("You can not upload more than 5 images");
            return;
        }
        var recRow = '';
        recRow += '<div id = "rowCount' + rowCount + '" class="property_image_area">';
        recRow += ' <div class="form-group">';
        recRow += '  <div class="col-sm-12">';
        recRow += '    &nbsp;';
        recRow += '     </div>';
        recRow += '     <div class="col-sm-12 upload-attach-file-task">';
        recRow += '         <div class="fileUpload btn btn-defaultcustom btn-md">';
        recRow += '             <span>Browse</span>';
        recRow += '             <input type="file" name="files[]"  id="file_' + rowCount + '" class="upload"  onchange="showFileName(this)" />';
        recRow += '         </div>';
        recRow += '         <input id="uploadFile_' + rowCount + '" placeholder="Choose File" disabled="disabled" style="border:none;background-color:#fdfdfd;">';
        recRow += '     </div>';
        recRow += ' </div>';
        recRow += '     <div class="custom-remove-icon">';
        recRow += '         <a href="javascript:void(0);" onclick="removeRow(' + rowCount + ');"><i class="fa fa-2x fa-remove cust_remove"></i></a>';
        recRow += '     </div>';
        recRow += '</div>';

        $('#muliFileUpload').append(recRow);

    }

    function removeRow(removeNum) {
        $('#rowCount' + removeNum).remove();
    }

    function removeTaskAttachment(attachment_id, original_name, file_name) {
        alertify.confirm("Do you want to delete " + original_name + " attachment ?", function (e) {
            if (e) {
                $.ajax({
                    url: rootPath + '/route.php',
                    data: "ajax_page=task_manager&action=remove_task_attachment&attachment_id=" + attachment_id + "&file_name=" + file_name,
                    dataType: 'text',
                    type: 'POST',
                    async: true,
                    success: function (result) {
                        if (result == 1) {
                            $('#image_div_' + attachment_id).remove();
                        }
                    }
                });
            }
        });
    }

    function removeProductImage(linkId, imageSource, count) {

        var txt;
        var r = confirm("Are you sure to delete this product image?");
        if (r == true) {
            var deleted_images = $('#deleted_images').val();
            if (deleted_images == "" || deleted_images == null) {
                $('#deleted_images').val(imageSource);
            } else {
                deleted_images = deleted_images + "," + imageSource;
                $('#deleted_images').val(deleted_images);
            }
            $('#image_content' + linkId).addClass('disabled').css({'background': '#DDDDDD'});

            $('#img_remover_' + linkId).css({'background': '#DDDDDD'});
            $('#img_remover_' + linkId + ' i').css({'color': '#DDDDDD'});

            // alert("The product image has been removed temporary, Please Submit the form to take effect");
        }
    }
</script>