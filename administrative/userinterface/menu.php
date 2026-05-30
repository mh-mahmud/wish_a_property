<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
        <nav>
            <ul class="nav">
                <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php" class="<?=$active_general?>"><i class="lnr lnr-home"></i> <span>Dashboard</span></a></li>
                <li>
                    <a href="#propertyPage" data-toggle="collapse" class="collapsed <?=$active_list_property?>"><i class="lnr lnr-file-empty"></i> <span>Property Management</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
                    <div id="propertyPage" class="collapse <?=$active_property?>">
                        <ul class="nav">
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=property" class="<?=$active_list_property?>">Property</a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#usersPage" data-toggle="collapse" class="collapsed <?=$active_users_menu?>"><i class="lnr lnr-file-empty"></i> <span>Users Management</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
                    <div id="usersPage" class="collapse <?=$active_users?>">
                        <ul class="nav">
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=users_list" class="<?=$active_users_list?>">Users</a></li>
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=adminusers" class="<?=$active_admin_list?>">Admin</a></li>
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=agents" class="<?=$active_agent_list?>">Agents</a></li>
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=subscriber_list" class="<?=$active_subscriber_list?>">Subscribers</a></li>
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=service_list" class="<?=$active_service_list?>">Services</a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#settingsPage" data-toggle="collapse" class="collapsed <?=$active_setting_menu?>"><i class="lnr lnr-file-empty"></i> <span>Settings</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
                    <div id="settingsPage" class="collapse <?=$active_settings?>">
                        <ul class="nav">
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=slider" class="<?=$active_slider?>">Slider</a></li>
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=latest_news" class="<?=$active_news?>">Latest News</a></li>
                            <li><a href="<?=$HOMEPAGE_ROOT?>/administrative/index.php?todo=newsticker" class="<?=$active_newsticker?>">Newsticker</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>