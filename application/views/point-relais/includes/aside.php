<aside id="menu" class="animated fadeIn">
    <ul class="nav metis-menu" id="side-menu">
        <li class="dashboard_user">
            <?= _l('welcome_top', ''); ?> <i class="fa fa-power-off top-left-logout pull-right" data-toggle="tooltip" data-title="<?= _l('nav_logout'); ?>" data-placement="left" onclick="window.location.href = '<?= site_url('authentication/logout_point_relais'); ?>'"></i>
        </li>
        <?php
        $menu_active = get_option('aside_menu_active_point_relais');
        $menu_active = json_decode($menu_active);
        foreach ($menu_active->aside_menu_active_point_relais as $item) {
            if (isset($item->permission) && !empty($item->permission)) {
                if (!has_permission($item->permission, '', 'view') && !has_permission($item->permission, '', 'view_own')) {
                    continue;
                }
            }

            $submenu = false;
            $remove_main_menu = false;
            $url = '';
            if (isset($item->children)) {
                $submenu = true;
                $total_sub_items_removed = 0;
                foreach ($item->children as $_sub_menu_check) {
                    if (isset($_sub_menu_check->permission) && !empty($_sub_menu_check->permission) && $_sub_menu_check->permission != 'payments') {
                        if (!has_permission($_sub_menu_check->permission, '', 'view') && !has_permission($_sub_menu_check->permission, '', 'view_own')) {
                            $total_sub_items_removed++;
                        }
                    }
                }
                if ($total_sub_items_removed == count($item->children)) {
                    $submenu = false;
                    $remove_main_menu = true;
                }
            } else {
                // child items removed
                if ($item->url == '#') {
                    continue;
                }
                $url = $item->url;
            }
            if ($remove_main_menu == true) {
                continue;
            }

            $name = _l($item->name);
            if (strpos($name, 'translation_not_found') !== false) {
                $name = $item->name;
            }
            $url = $item->url;
            if (!_startsWith($url, 'http://') && $url != '#') {
                $url = point_relais_url($url);
            }

            if (isset($item->name) && $item->name != 'entreprise') {

                ?>
                <li>
                    <a href="<?= $url; ?>" aria-expanded="true">
                        <img class="menu-img" src="<?= site_url('assets/images/defaults/menus/' . $item->icon . '.png') ?>" />
                        <?= $name; ?>
                        <?php if ($submenu == true) { ?>
                            <span class="fa arrow ptop10"></span>
                        <?php } ?>
                    </a>
                    <?php if (isset($item->children)) { ?>
                        <ul class="nav nav-second-level collapse" aria-expanded="false">
                            <?php
                            foreach ($item->children as $submenu) {
                                if (isset($submenu->permission) && !empty($submenu->permission)) {
                                    if (!has_permission($submenu->permission, '', 'view') && !has_permission($submenu->permission, '', 'view_own')) {
                                        continue;
                                    }
                                }

                                $name = _l($submenu->name);
                                if (strpos($name, 'translation_not_found') !== false) {
                                    $name = $submenu->name;
                                }

                                $url = $submenu->url;
                                if (!_startsWith($url, 'http://')) {
                                    $url = point_relais_url($url);
                                }

                                ?>
                                <li>
                                    <a href="<?= $url; ?>">
                                        <?php if (!empty($submenu->icon)) { ?>
                                            <img class="menu-img" src="<?= site_url('assets/images/defaults/menus/' . $item->icon . '.png') ?>" />
                                        <?php } ?>
                                        <?= $name; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </li>
                <?php
            }
        }

        ?>
        <li>
            <center>
                <i class="fa fa-arrow-circle-left menu-icon icon-circle-left-aside" title="Fermeture menu"></i>
            </center>
        </li>
    </ul>
</aside>
