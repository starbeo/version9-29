<aside id="menu" class="animated fadeIn">
    <ul class="nav metis-menu" id="side-menu">
        <li class="dashboard_user">
            <?= _l('welcome_top', $_staff->firstname); ?> <i class="fa fa-power-off top-left-logout pull-right" data-toggle="tooltip" data-title="<?= _l('nav_logout'); ?>" data-placement="left" onclick="window.location.href = '<?= site_url('authentication/logout'); ?>'"></i>
        </li>
        <?php
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $menu_active = get_option('aside_menu_active');
        $menu_active = json_decode($menu_active);

        foreach ($menu_active->aside_menu_active as $item) {
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
                    } else if ($_sub_menu_check->permission == 'payments') {
                        if (!has_permission('payments', '', 'view') && !has_permission('invoices', '', 'view_own')) {
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
                $url = admin_url($url);
            }

            if (isset($item->name)) {
                if (isset($item->display) && get_permission_module($item->display) == 0) {
                    continue;
                }
                ?>
                <li>
                    <a href="<?= $url; ?>" aria-expanded="true">
                        <i class="<?= $item->icon; ?> menu-icon"></i>
                        <?= $name; ?>
                        <?php if ($submenu == true) { ?>
                            <span class="fa arrow"></span>
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
                                if (isset($submenu->display) && get_permission_module($submenu->display) == 0) {
                                    continue;
                                }

                                $name = _l($submenu->name);
                                if (strpos($name, 'translation_not_found') !== false) {
                                    $name = $submenu->name;
                                }

                                $url = $submenu->url;
                                if (!_startsWith($url, 'http://')) {
                                    $url = admin_url($url);
                                }

                                ?>
                                <li>
                                    <a href="<?= $url; ?>">
                                        <?php if (!empty($submenu->icon)) { ?>
                                            <i class="<?= $submenu->icon; ?> menu-icon"></i>
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
        <?php if (is_admin()) { ?>
            <li>
                <a href="#" class="open-customizer"><i class="fa fa-cog menu-icon"></i><?= _l('setting_bar_heading'); ?></a>
            </li>
        <?php } ?>
        <li class="borderbot text-center">
            <i class="fa fa-arrow-circle-left menu-icon icon-circle-left-aside" title="Fermeture menu"></i>
        </li>
        <li class="version-menu">
            version <?= $this->config->item('version_app'); ?>
        </li>
    </ul>
</aside>
