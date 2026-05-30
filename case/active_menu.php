<?php
if (isset($_GET['page'])) {
    $active_home = '';
    $active_property = '';
    $active_add_property = '';
    $active_about = '';
    $active_contact = '';
    switch ($_GET['page']) {
        case "home":
            $active_home = "class='active'";
            break;
        case "property":
            $active_property = "class='active'";
            $active_sub_property = "active";
            break;
        case "add_property":
            $active_add_property = "class='active'";
            $active_sub_property = "active";
            break;
        case "my_property":
            $active_my_property = "class='active'";
            $active_sub_property = "active";
            break;
        case "add_service":
            $active_add_service = "class='active'";
            $active_sub_service = "active";
            break;
        case "edit_service":
            $active_add_service = "class='active'";
            $active_sub_service = "active";
            break;
        case "manage_service":
            $active_manage_service = "class='active'";
            $active_sub_service = "active";
            break;
        case "compare_property":
            $active_compare_property = "class='active'";
            $active_sub_property = "active";
            break;
        case "about":
            $active_about = "class='active'";
            break;
        case "contact":
            $active_contact = "class='active'";
            break;
        case "profile":
            $active_profile = "class='active'";
            break;
        case "my_watchlist":
            $active_my_watchlist = "class='active'";
            break;
        case "find_service_provider":
            $active_find_service = "class='active'";
            $active_find_resource = "active";
            break;
        case "find_agents":
            $active_find_agents = "class='active'";
            $active_find_resource = "active";
            break;
        case "change_password":
            $active_change_password = "class='active'";
            break;
    }
}
?>