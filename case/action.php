<?php
require_once ABSLPATHROOT . 'library/kb_user_management.php';
require_once ABSLPATHROOT . 'library/kb_property_management.php';

$kbUserManagement = new KBUsermanagement();
$kbPropertyManagement = new KBPropertyManagement();

switch ($_GET['action']) {

    case "register":
        $checkResult = $kbUserManagement->register($_POST);
        if ($checkResult === KBUserManagement::SUCCESS) {
            $_SESSION['register_thanks_name'] = $_POST['first_name'] . ' ' . $_POST['last_name'];
            $_SESSION['register_thanks_email'] = $_POST['email'];
            header("location: index.php?page=register_thanks");
        } else {
            // set all input data to session for repopulate
            setAllInputDataToSession($_POST);
            $_SESSION['register_message_error'] = $checkResult;
            header("location: index.php?page=register");
        }
        exit;
        break;

    case "add_property":
        checkMemberLogin();
        $response = $kbPropertyManagement->addProperty($_POST);
        if ($response === KBPropertyManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = 'Property added successfully';
            header("location: index.php?page=add_property");
        } else {
            setAllInputDataToSession($_POST);
            $_SESSION['flash_message_error'] = $response;
            header("location: index.php?page=add_property");
        }
        exit;
        break;

    case "add_service":
        checkMemberLogin();
        $response = $kbPropertyManagement->addService($_POST);
        if ($response === KBPropertyManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = 'Service added successfully';
            header("location: index.php?page=manage_service");
        } else {
            setAllInputDataToSession($_POST);
            $_SESSION['flash_message_error'] = $response;
            header("location: index.php?page=add_service");
        }
        exit;
        break;

    case "edit_service":
        checkMemberLogin();
        $response = $kbPropertyManagement->editService($_POST);
        if ($response === KBPropertyManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = 'Service updated successfully';
            header("location: index.php?page=manage_service");
        } else {
            setAllInputDataToSession($_POST);
            $_SESSION['flash_message_error'] = $response;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
        exit;
        break;

    case 'delete_service':
        checkMemberLogin();
        $response = $kbPropertyManagement->deleteService($_GET);
        if ($response === KBPropertyManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = 'Service deleted successfully';
            header("location: index.php?page=manage_service");
        } else {
            setAllInputDataToSession($_POST);
            $_SESSION['flash_message_error'] = "Service not deleted";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
        exit();
        break;

    case "add_subscriber":
        $response = $kbUserManagement->addSubscriber($_POST);
        if ($response === KBUserManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = 'Subscriber added successfully';
        } elseif ($response === KBUserManagement::FAILED) {
            $_SESSION['flash_message_error'] = 'Subscriber added failed';
        } else {
            $_SESSION['flash_message_error'] = $response;
        }
        header("location: index.php");
        exit;
        break;

    case "edit_property":
        checkMemberLogin();
        $response = $kbPropertyManagement->editProperty($_POST);
        if ($response === KBPropertyManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = 'Property edited successfully';
        } else {
            $_SESSION['flash_message_error'] = $response;
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
        break;

    case "profile":
        checkMemberLogin();
        $response = $kbUserManagement->editProfile($_POST, $_SESSION['loggedin_userid']);
        if ($response === $kbUserManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = 'Profile edited successfully';
        } else {
            $_SESSION['flash_message_error'] = $response;
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
        break;

    case "change_password":
        checkMemberLogin();
        $res = $kbUserManagement->changeAdminPassword($_POST);
        if ($res == 1) {
            unset($_SESSION['IS_PASSWORD_MATCH_PREV']);
            $_SESSION['flash_message_success'] = "  Password Updated Successfully";
        } else if ($res == 0) {
            $_SESSION['flash_message_error'] = "  Sorry, password not updated";
        }
        else {
            $_SESSION['flash_message_error'] = $res;
        }

        header("Location: index.php?page=change_password");
        exit;
        break;

    case "login":
        $response = $kbUserManagement->login($_POST);
        if ($response == KBUserManagement::SUCCESS) {
            header("location: index.php?page=property");
        } else {
            if ($response == KBUserManagement::FAILED) {
                $_SESSION['flash_message_error'] = 'Please enter valid captcha';
            } elseif ($response == KBUserManagement::INACTIVE) {
                $_SESSION['flash_message_error'] = 'Your account is not active, Please check your registration email and active account using activation link';
            } else {
                $_SESSION['flash_message_error'] = 'Please enter valid username and password';
            }
            header("location: index.php?page=login");
        }
        exit;
        break;

    case "contact_us":
        $response = $kbUserManagement->contactUs($_POST);
        if ($response == KBUserManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = "Thank You! Your message has been sent.";
        } else if ($response == 1) {
            // set all input data to session for repopulate
            setAllInputDataToSession($_POST);
            $_SESSION['flash_message_error'] = "There was a problem with your submission. Please complete the form and try again.";
        } else if ($response == 2) {
            // set all input data to session for repopulate
            setAllInputDataToSession($_POST);
            $_SESSION['flash_message_error'] = "Something went wrong and we couldn't send your message.";
        } else {
            // set all input data to session for repopulate
            setAllInputDataToSession($_POST);
            $_SESSION['flash_message_error'] = "There was a problem with your submission, please try again.";
        }
        header("location: index.php?page=contact");
        exit;
        break;

    case "comment":
        $response = $kbPropertyManagement->addComment($_POST);
        if ($response === KBPropertyManagement::SUCCESS) {
            $_SESSION['flash_message_success'] = 'Comment added successfully';
        } else {
            $_SESSION['flash_message_error'] = $response;
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
        break;

    case "logout":
        $kbUserManagement->logout();
        header('Location: index.php');
        break;

}
?>