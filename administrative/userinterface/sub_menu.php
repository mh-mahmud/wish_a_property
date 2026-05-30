<?php
switch ($_GET['todo']) {
    case "admin_login":
        $active_login = "active";
        $active_admin_login = "active";
        break;

    case "users_list":
    case "edit_user":
        $active_users = "in";
        $active_users_list = "active";
        $active_users_menu = "active";
        break;

    case "adminusers":
    case "editadmin":
    case "addadmin":
        $active_users = "in";
        $active_admin_list = "active";
        $active_users_menu = "active";
        break;

    case "agents":
    case "addagent":
    case "editagent":
        $active_users = "in";
        $active_agent_list = "active";
        $active_users_menu = "active";
        break;

    case "subscriber_list":
        $active_users = "in";
        $active_subscriber_list = "active";
        $active_users_menu = "active";
        break;

    case "service_list":
        $active_users = "in";
        $active_service_list = "active";
        $active_users_menu = "active";
        break;

    case "slider":
    case "slider_form":
        $active_settings = "in";
        $active_slider = "active";
        $active_setting_menu = "active";
        break;

    case "latest_news":
    case "latest_news_form":
        $active_settings = "in";
        $active_news = "active";
        $active_setting_menu = "active";
        break;

    case "newsticker":
    case "newsticker_form":
        $active_settings = "in";
        $active_newsticker = "active";
        $active_setting_menu = "active";
        break;
        
    case "property":
    case "edit_property":
        $active_property = "in";
        $active_list_property = "active";
        break;

    default:
        $active_general = "active";
        $active_general_link = "active";
        break;

}
?>