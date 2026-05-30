<?php
if (!empty($_GET['uid'])) {
    $uid = encryptor('decrypt', trim($_GET['uid']));
    $user_lines = $userModel->get($uid);
    if (!empty($user_lines)) {
        $where = array(
            'uid' => $uid
        );
        $data = array(
            'useractivated' => 1
        );
        $success_flag = $userModel->save($data, $where);

        if ($success_flag == 1) {
            ?>

            <section id="at-inner-title-sec">
                <div class="container">
                    <div class="row">
                    </div>
                </div>
            </section>

            <section class="at-account-sec">
                <div class="container">
                    <div class="row animatedParent animateOnce">
                        <div class="col-lg-12 col-md-12">

                            <div class="alert alert-success fade in">
                                <i class="fa fa-check-circle"></i>  Account verification
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div>Your account is now verified successfully, Please login to your account to access your information,  </div>
                                   Click the link to login  <a
                                                href="<?= $HOMEPAGE_ROOT ?>/index.php?page=login"><b>Login</b></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </section>
            <?php
        }
    } else {
        header('Location: index.php');
    }
} else {
    header('Location: index.php');
}

?>