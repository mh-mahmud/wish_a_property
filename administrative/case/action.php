<?php
require_once ABSLPATHROOT . 'library/kb_property_management.php';
$kbPropertyManagement = new KBPropertyManagement();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {

        case "admin_login":
            require_once ABSLPATHROOT . "library/kb_user_management.php";
            $kbUserManagement = new KBUserManagement();
            $login_status = $kbUserManagement->adminLogin($_POST);

            if ($login_status != '') {
                $_SESSION['login_error'] = $login_status;
            }
            header("Location: index.php");
            exit();
            break;

        case "updateUserFromAdmin":
            checkAdminLogin();
            require_once ABSLPATHROOT . "library/kb_user_management.php";
            $kbUserManagement = new KBUserManagement();
            $response = $kbUserManagement->updateUserFromAdmin($_POST);

            if ($response === KBUserManagement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'User data edited successfully';
                header("Location: index.php?todo=users_list");
            } else {
                $_SESSION['flash_message_error'] = $response;
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "addAdmin":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';

            $kbAdminManamement = new KBAdminManamement();
            $response = $kbAdminManamement->addAdminUser($_POST);

            if ($response === KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Admin user added successfully.';
                header("Location: index.php?todo=adminusers");
            } else {
                setAllInputDataToSession($_POST);
                $_SESSION['flash_message_error'] = $response;
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

            case "addAgent":
                checkAdminLogin();
                require_once ABSLPATHROOT . 'library/kb_admin_management.php';

                $kbAdminManamement = new KBAdminManamement();
                $response = $kbAdminManamement->addAgent($_POST);
                /*pr($response);
                die();*/

                if ($response === KBAdminManamement::SUCCESS) {
                    $_SESSION['flash_message_success'] = 'Agent added successfully.';
                    header("Location: index.php?todo=agents");
                } else {
                    setAllInputDataToSession($_POST);
                    $_SESSION['flash_message_error'] = $response;
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                }
                exit();
                break;

        case "editAgent":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';

            $kbAdminManamement = new KBAdminManamement();
            $response = $kbAdminManamement->editAgent($_POST);

            if ($response === KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Agent edited successfully.';
                header("Location: index.php?todo=agents");
            } else {
                $_SESSION['flash_message_error'] = $response;
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "editAdmin":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';

            $kbAdminManamement = new KBAdminManamement();
            $response = $kbAdminManamement->editAdminUser($_POST);

            if ($response === KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Admin user edited successfully.';
                header("Location: index.php?todo=adminusers");
            } else {
                $_SESSION['flash_message_error'] = $response;
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "updateUserProperty":
            checkAdminLogin();
            $response = $kbPropertyManagement->updateUserProperty($_POST);
            if ($response === KBPropertyManagement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Property edited successfully';
                header("Location: index.php?todo=property");
            } else {
                $_SESSION['flash_message_error'] = $response;
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }

            exit();
            break;

        case "addSlider":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';
            $kbAdminManamement = new KBAdminManamement();
            $post_data = $_POST;
            $result = $kbAdminManamement->addSlider($post_data);
            if ($result == KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Slider Added Successfully.';
                header("Location: index.php?todo=slider");
            } else {
                setAllInputDataToSession($_POST);
                $_SESSION['flash_message_error'] = 'Failed to added';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "addNews":

            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';
            $kbAdminManamement = new KBAdminManamement();
            $post_data = $_POST;
            /*pr($post_data);
            die();*/
            $result = $kbAdminManamement->addNews($post_data);
            if ($result == KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'News Added Successfully.';
                header("Location: index.php?todo=latest_news");
            } else {
                setAllInputDataToSession($_POST);
                $_SESSION['flash_message_error'] = 'Failed to added';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "addNewstickers":

            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';
            $kbAdminManamement = new KBAdminManamement();
            $post_data = $_POST;
            $result = $kbAdminManamement->addNewstickers($post_data);
            if ($result == KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Newstickers Added Successfully.';
                header("Location: index.php?todo=newsticker");
            } else {
                setAllInputDataToSession($_POST);
                $_SESSION['flash_message_error'] = 'Failed to added newsticker';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "editNewstickers":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';
            $kbAdminManamement = new KBAdminManamement();
            $post_data = $_POST;
            $result = $kbAdminManamement->editNewstickers($post_data);
            if ($result == KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Newsticker updated Successfully.';
                header("Location: index.php?todo=newsticker");
            } else {
                $_SESSION['flash_message_error'] = 'Failed to update Newsticker.';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "editSlider":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';
            $kbAdminManamement = new KBAdminManamement();
            $post_data = $_POST;
            $result = $kbAdminManamement->editSilder($post_data);
            if ($result == KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Slider updated Successfully.';
                header("Location: index.php?todo=slider");
            } else {
                $_SESSION['flash_message_error'] = 'Failed to update slider.';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "editNews":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';
            $kbAdminManamement = new KBAdminManamement();
            $post_data = $_POST;
            $result = $kbAdminManamement->editNews($post_data);
            if ($result == KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'News updated Successfully.';
                header("Location: index.php?todo=latest_news");
            } else {
                $_SESSION['flash_message_error'] = 'Failed to update news.';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "update_slider_listorder":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';
            $kbAdminManamement = new KBAdminManamement();
            $post_data = $_POST;
            $result = $kbAdminManamement->updateSliderListorder($post_data);

            if ($result == KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'Slider orderlist updated successfully';
                header("Location: index.php?todo=slider");
            } else {
                $_SESSION['flash_message_error'] = 'Failed to update Slider orderlist';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "update_news_listorder":
            checkAdminLogin();
            require_once ABSLPATHROOT . 'library/kb_admin_management.php';
            $kbAdminManamement = new KBAdminManamement();
            $post_data = $_POST;
            $result = $kbAdminManamement->updateNewsListorder($post_data);

            if ($result == KBAdminManamement::SUCCESS) {
                $_SESSION['flash_message_success'] = 'News orderlist updated successfully';
                header("Location: index.php?todo=latest_news");
            } else {
                $_SESSION['flash_message_error'] = 'Failed to update news orderlist';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
            break;

        case "admin_logout":
            if (isset($_SESSION['admin_uid'])) {
                session_destroy();
            }
            header("Location: index.php");
            break;
    }
}
?>