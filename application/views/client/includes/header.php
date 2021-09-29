<div id="header">
    <?php
    $classHideMenu = 'hide-menu';
    $iconHeaderMenu = 'fa-bars';
    if(get_option_client('laisser_le_menu_toujours_ouvert') == 1) { 
        $classHideMenu = '';
        $iconHeaderMenu = 'fa-arrow-left';
    }
    ?>
    <div class="header-link menu-header-left <?= $classHideMenu ?>">
        <i class="fa <?= $iconHeaderMenu ?>" data-toggle="tooltip" data-placement="right" title="<?= _l('nav_sidebar_toggle_tooltip'); ?>"></i>
    </div>
    <a id="logo" href="<?= site_url('client'); ?>">
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
                    <li><a href="<?= client_url('profile'); ?>"><?= _l('nav_my_profile'); ?></a></li>
                    <li><a href="<?= client_url('profile/edit'); ?>"><?= _l('nav_edit_profile'); ?></a></li>
                    <li><a href="<?= site_url(); ?>authentication/logout_client"><?= _l('nav_logout'); ?></a></li>
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
                        <?= client_logo($expediteur->id, array('img', 'img-responsive', 'staff-profile-image-small', 'pull-left'), 'thumb'); ?>
                        <?= $expediteur->nom; ?>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu animated fadeIn">
                        <li>
                            <a href="<?= client_url('profile'); ?>"><?= _l('nav_my_profile'); ?></a>
                        </li>
                        <li>
                            <a href="<?= client_url('profile/edit'); ?>"><?= _l('nav_edit_profile'); ?></a>
                        </li>
                        <li>
                            <a href="<?= site_url(); ?>authentication/logout_client"><?= _l('nav_logout'); ?></a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <?php
                    $unread_notifications = total_rows('tblnotificationscustomer', array('isread' => 0, 'toclientid' => get_expediteur_user_id()), 100);
                    if ($unread_notifications > 100) {
                        $unread_notifications = 100;
                    }

                    ?>
                    <a href="#" class="dropdown-toggle notifications-icon-staffs <?php
                    if ($unread_notifications > 0) {
                        echo 'animated swing';
                    }

                    ?>" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell" data-toggle="tooltip" data-placement="bottom" title="<?= _l('nav_notifications_tooltip'); ?>"></i>
                        <?php if ($unread_notifications > 0) { ?>
                            <span class="label label-warning icon-total-indicator icon-notifications-staffs"><?= $unread_notifications; ?></span>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu notifications animated fadeIn activity-feed no-margin">
                        <?php foreach ($_notifications as $notification) { ?>
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
                                            echo client_logo($notification['toclientid'], array('staff-profile-image-small', 'img-circle', 'pull-left'), 'thumb') . ' - <b>' . get_client_full_name($notification['toclientid']) . '</b> : <a href="' . $link . '" style="color: #000;">' . $notification['description'] . '</a>';

                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if (count($_notifications) == 0) { ?>
                        <li>
                            <a href="#" onclick="return false;"><?= _l('nav_no_notifications'); ?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <!--li>
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
                </li-->
            </ul>
        </div>
    </nav>
</div>
<div id="mobile-search" class="hide">
    <ul></ul>
</div>
