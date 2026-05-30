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

<div class="panel-heading">
    <h3 class="panel-title">Create Agent</h3>
</div>
<div class="panel-body custom_forms">
    <form id="agent_form" class="form-horizontal label-left" name="agentform" action="index.php?action=addAgent" method="post" enctype="multipart/form-data">

        <div class="form-group">
            <label for="signup-firstname" class="col-sm-3 control-label">Agent Name</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="agent_name" id="agent_name" value="<?=$form_data['agent_name']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-firstname" class="col-sm-3 control-label">Agent Title</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="agent_title" id="agent_title" value="<?=$form_data['agent_title']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Phone Number</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="agent_phone" id="agent_phone" value="<?=$form_data['agent_phone']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Facebook Link</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="facebook_link" id="facebook_link" value="<?=$form_data['facebook_link']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Twitter Link</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="twitter_link" id="twitter_link" value="<?=$form_data['twitter_link']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Linked Link</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="linkedin_link" id="linkedin_link" value="<?=$form_data['linkedin_link']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Vimeo Link</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="vimeo_link" id="vimeo_link" value="<?=$form_data['vimeo_link']?>">
            </div>
        </div>

        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Upload Image</label>
            <div class="col-sm-9">
                <input type="file" class="form-control" name="agent_file" id="agent_file">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-fullrounded center-block"><i
                class="fa fa-check-circle"></i>
            <span>Submit</span>
        </button>
    </form>
</div>
<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/agent_validation.js"></script>