<?php

require_once ABSLPATHROOT . 'models/agents.php';
$agentObj = new Agents();

$uid = trim($_GET['uid']);

$where = array(
    'id' => $uid
);
$agent_info = $agentObj->get($where);
$image_url = $HOMEPAGE_ROOT . KBConstant::UPLOAD_FILE_PATH . 'agents/';
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

<div class="panel-heading">
    <h3 class="panel-title">Edit Agent</h3>
</div>
<div class="panel-body custom_forms">
    <form id="editagent_form" class="form-horizontal label-left" name="agentform" action="index.php?action=editAgent" method="post" enctype="multipart/form-data">
        <input type="hidden" name="agent_id" id="agent_id" value='<?php echo $uid; ?>'>

        <div class="form-group">
            <label for="signup-firstname" class="col-sm-3 control-label">Agent Name</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="agent_name" id="agent_name" value="<?= $agent_info['agent_name'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-firstname" class="col-sm-3 control-label">Agent Title</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="agent_title" id="agent_title" value="<?= $agent_info['agent_title'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Phone Number</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="agent_phone" id="agent_phone" value="<?= $agent_info['agent_phone'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Facebook Link</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="facebook_link" id="facebook_link" value="<?= $agent_info['facebook_link'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Twitter Link</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="twitter_link" id="twitter_link" value="<?= $agent_info['twitter_link'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Linked Link</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="linkedin_link" id="linkedin_link" value="<?= $agent_info['linkedin_link'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Vimeo Link</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="vimeo_link" id="vimeo_link" value="<?= $agent_info['vimeo_link'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Upload Image</label>
            <div class="col-sm-9">
                <input type="file" class="form-control" name="agent_file" id="agent_file">
                <?php if ($agent_info['agent_image']) { ?>
                    <br>
                    <a href="<?= $image_url . $agent_info['agent_image'] ?>" data-lightbox="example-set">
                        <img width="100" src="<?= $image_url . $agent_info['agent_image'] ?>" title="Click for view" class="slider-image-area">
                    </a>
                <?php } ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-fullrounded center-block"><i
                class="fa fa-check-circle"></i>
            <span>Update</span>
        </button>
    </form>
</div>
<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/agent_validation.js"></script>
