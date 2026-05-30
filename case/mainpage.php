<?php

if (isset($_GET['page'])) {

    switch ($_GET['page']) {

        case "home":
            include ABSLPATHROOT . "view/home.php";
            break;

        case "about":
            include ABSLPATHROOT . "view/about.php";
            break;

        case "find_agents":
            include ABSLPATHROOT . "view/find_agents.php";
            break;

        case "find_service_provider":
            include ABSLPATHROOT . "view/find_service_provider.php";
            break;

        case "property":
            if( access_control('property') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/property.php";
            break;

        case "add_service":
            if( access_control('add_service') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/add_service.php";
            break;

        case "edit_service":
            if( access_control('edit_service') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/edit_service.php";
            break;

        case "manage_service":
            if( access_control('manage_service') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/manage_service.php";
            break;

        case "add_property":
            if( access_control('add_property') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/add_property.php";
            break;

        case "edit_property":
            if( access_control('edit_property') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/edit_property.php";
            break;

        case "my_property":
            if( access_control('my_property') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/my_property.php";
            break;

        case "compare_property":
            if( access_control('compare_property') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/compare_property.php";
            break;

        case "my_property_details":
            if( access_control('my_property_details') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/my_property_details.php";
            break;

        case "property_details":
            include ABSLPATHROOT . "view/property_details.php";
            break;

        case "contact":
            include ABSLPATHROOT . "view/contact.php";
            break;

        case "login":
            include ABSLPATHROOT . "view/login.php";
            break;

        case "register":
            include ABSLPATHROOT . "view/register.php";
            break;

        case "account":
            include ABSLPATHROOT . "view/account.php";
            break;

        case "profile":
            if( access_control('profile') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/profile.php";
            break;

        case "change_password":
            if( access_control('change_password') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "/view/change_password.php";
            break;

        case "register_thanks":
            include ABSLPATHROOT . "view/register_thanks.php";
            break;

        case "verifyuser":
            include ABSLPATHROOT . "view/verifyuser.php";
            break;

        case "my_watchlist":
            if( access_control('my_watchlist') === false ) {
                redirect($HOMEPAGE_ROOT);
            }
            include ABSLPATHROOT . "view/my_watchlist.php";
            break;

        default:
            include ABSLPATHROOT . "view/home.php";
            break;
    }
}
?>