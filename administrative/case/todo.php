<?php
switch ($_GET['todo']) {

    case "dashboard":
        include ABSLPATHROOT . 'administrative/view/dashboard.php';
        break;

    case "property":
        include ABSLPATHROOT . 'administrative/view/property_list.php';
        break;

    case "slider":
        include ABSLPATHROOT . 'administrative/view/slider_list.php';
        break;

    case "slider_form":
        include ABSLPATHROOT . 'administrative/view/home_slider_form.php';
        break;

    case "latest_news":
        include ABSLPATHROOT . 'administrative/view/latest_news.php';
        break;

    case "newsticker":
        include ABSLPATHROOT . 'administrative/view/newsticker.php';
        break;

    case "newsticker_form":
        include ABSLPATHROOT . 'administrative/view/newsticker_form.php';
        break;

    case "latest_news_form":
        include ABSLPATHROOT . 'administrative/view/latest_news_form.php';
        break;

    case "adminusers":
        include ABSLPATHROOT . "administrative/view/adminusers.php";
        break;

    case "agents":
        include ABSLPATHROOT . "administrative/view/agents.php";
        break;

    case "addadmin":
        include ABSLPATHROOT . "administrative/view/addadmin.php";
        break;

    case "addagent":
        include ABSLPATHROOT . "administrative/view/addagent.php";
        break;

    case "editadmin":
        include ABSLPATHROOT . "administrative/view/editadmin.php";
        break;

    case "editagent":
        include ABSLPATHROOT . "administrative/view/editagent.php";
        break;

    case "users_list":
        include ABSLPATHROOT . 'administrative/view/users_list.php';
        break;

    case "subscriber_list":
        include ABSLPATHROOT . 'administrative/view/subscriber_list.php';
        break;

    case "service_list":
        include ABSLPATHROOT . 'administrative/view/service_list.php';
        break;

    case "edit_user":
        include ABSLPATHROOT . 'administrative/view/edit_users.php';
        break;

    case "edit_property":
        include ABSLPATHROOT . 'administrative/view/edit_property.php';
        break;


    default:
        include ABSLPATHROOT . 'administrative/view/dashboard.php';
        break;

}
?>