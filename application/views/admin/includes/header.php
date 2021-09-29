<div id="header">
    <?php
    $classHideMenu = 'hide-menu';
    $iconHeaderMenu = 'fa-bars';
    if(get_staff()->menu_ouvert == 1) {
        $classHideMenu = '';
        $iconHeaderMenu = 'fa-arrow-left';
    }
    ?>
    <div class="header-link menu-header-left <?= $classHideMenu ?>">
        <i class="fa <?= $iconHeaderMenu ?>" data-toggle="tooltip" data-placement="right" title="<?= _l('nav_sidebar_toggle_tooltip'); ?>"></i>
    </div>
    <a id="logo" href="<?= site_url(); ?>">
        <?= '<img src="' . base_url() . 'uploads/company/' . get_option('companyalias') . '/logo.png" alt="' . get_option('companyname') . '" />'; ?>
    </a>
    <nav>
        <div class="small-logo">
            <span class="text-primary"><?= '<img src="' . base_url() . 'uploads/company/' . get_option('companyalias') . '/logo.png" alt="' . get_option('companyname') . '" />'; ?></span>
        </div>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation" >
                <ul class="nav navbar-nav">
                    <li><a href="<?= admin_url('profile'); ?>"><?= _l('nav_my_profile'); ?></a></li>
                    <li><a href="<?= admin_url('staff/edit_profile'); ?>"><?= _l('nav_edit_profile'); ?></a></li>
                    <li><a href="<?= site_url(); ?>authentication/logout"><?= _l('nav_logout'); ?></a></li>
                </ul>
            </div>
        </div>
        <ul class="hidden-xs navbar header-left">
            <li id="top_date"></li>
        </ul>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <li id="top_search" class="dropdown">
                    <input type="search" id="search_input" class="form-control" placeholder="<?= _l('search'); ?>">
                    <div id="search_results"></div>
                </li>
                <li id="top_search_button">
                    <button class="btn"><i class="fa fa-search"></i></button>
                </li>
                <li>
                    <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
                        <?= staff_profile_image($_staff->staffid, array('img', 'img-responsive', 'staff-profile-image-small', 'pull-left'), 'thumb'); ?>
                        <?= $_staff->firstname . ' ' . $_staff->lastname; ?>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu animated fadeIn">
                        <li>
                            <a href="<?= admin_url('profile'); ?>"><?= _l('nav_my_profile'); ?></a>
                        </li>
                        <li>
                            <a href="<?= admin_url('staff/edit_profile'); ?>"><?= _l('nav_edit_profile'); ?></a>
                        </li>
                        <li>
                            <a href="<?= site_url(); ?>authentication/logout"><?= _l('nav_logout'); ?></a>
                        </li>
                    </ul>
                </li>
                <?php if (is_admin()) { ?>
                    <li class="dropdown">
                        <?php
                        $unread_notifications_staff = total_rows('tblnotifications', array('isread' => 0), 100);
                        if ($unread_notifications_staff > 100) {
                            $unread_notifications_staff = 100;
                        }

                        ?>
                        <a href="#" class="dropdown-toggle notifications-icon-staffs <?php
                        if ($unread_notifications_staff > 0) {
                            echo 'animated swing';
                        }

                        ?>" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bell" data-toggle="tooltip" data-placement="bottom" title="<?= _l('nav_notifications_tooltip'); ?>"></i>
                            <?php if ($unread_notifications_staff > 0) { ?>
                                <span class="label label-warning icon-total-indicator icon-notifications-staffs"><?= $unread_notifications_staff; ?></span>
                            <?php } ?>
                        </a>
                        <ul class="dropdown-menu notifications animated fadeIn activity-feed no-margin">
                            <?php foreach ($_notifications_staff as $notification) { ?>
                                <li class="feed-item">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="date">
                                                <span data-toggle="tooltip" data-title="<?= $notification['date']; ?>"><?= time_ago($notification['date']); ?></span>
                                            </div>
                                            <div class="text">
                                                <?php
                                                $link = '#';
                                                if (!is_null($notification['link'])) {
                                                    $link = $notification['link'];
                                                }
                                                if (is_numeric($notification['fromcompany'])) {
                                                    echo client_logo($notification['fromcompany'], array('staff-profile-image-small', 'img-circle', 'pull-left'), 'thumb') . ' - <b>' . get_client_full_name($notification['fromcompany']) . '</b> : <a href="' . $link . '" style="color: #000;">' . $notification['description'] . '</a>';
                                                } else {
                                                    echo staff_profile_image($notification['fromuserid'], array('staff-profile-image-small', 'img-circle', 'pull-left'), 'thumb') . ' - <b>' . get_staff_full_name($notification['fromuserid']) . '</b> : <a href="' . $link . '" style="color: #000;">' . $notification['description'] . '</a>';
                                                }

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="#" onclick="return false;"><?= _l('nav_no_notifications'); ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown mright10">
                        <?php
                        $unread_notifications_clients = total_rows('tblnotificationsadmin', array('isread' => 0), 100);
                        if ($unread_notifications_clients > 100) {
                            $unread_notifications_clients = 100;
                        }

                        ?>
                        <a href="#" class="dropdown-toggle notifications-icon-clients <?php
                        if ($unread_notifications_clients > 0) {
                            echo 'animated swing';
                        }

                        ?>" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bell" data-toggle="tooltip" data-placement="bottom" title="<?= _l('nav_notifications_tooltip'); ?>"></i>
                            <?php if ($unread_notifications_clients > 0) { ?>
                                <span class="label label-warning icon-total-indicator icon-notifications-clients"><?= $unread_notifications_clients; ?></span>
                            <?php } ?>
                        </a>
                        <ul class="dropdown-menu notifications animated fadeIn activity-feed">
                            <?php foreach ($_notifications_clients as $notification) { ?>
                                <li class="feed-item">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="date">
                                                <span data-toggle="tooltip" data-title="<?= $notification['date']; ?>" data-original-title="" title=""><?= time_ago($notification['date']); ?></span>
                                            </div>
                                            <div class="text">
                                                <?php
                                                $link = '#';
                                                if (!is_null($notification['link'])) {
                                                    $link = $notification['link'];
                                                }
                                                echo client_logo($notification['fromclientid'], array('staff-profile-image-small', 'img-circle', 'pull-left'), 'thumb') . ' - <b>' . get_client_full_name($notification['fromclientid']) . '</b> : <a href="' . $link . '" style="color: #000;">' . $notification['description'] . '</a>';

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="#" onclick="return false;"><?= _l('nav_no_notifications'); ?></a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <li>
                    <select class="" id="_language" style="padding: 18px 10px; background-color: #323a45; color: #fff; border-right: 0; border-top: 0; border-bottom: 0; border-left: 1px solid #5A5A5A; margin-right: 20px; font-size: 13px;">
                        <option value="french" <?php
                        if ($this->session->userdata('language') == 'french') {
                            echo "selected";
                        }

                        ?>>FR</option>
                        <option value="english" <?php
                        if ($this->session->userdata('language') == 'english') {
                            echo "selected";
                        }

                        ?>>EN</option>
                    </select>
                </li>
            </ul>
        </div>
    </nav>
</div>
<div id="mobile-search" class="hide">
    <ul></ul>
</div>
