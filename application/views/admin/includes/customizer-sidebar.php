<div id="customize-sidebar" class="animated">
	<ul class="nav metis-menu">
		<li>
			<a href="#" class="btn btn-default close-customizer"><i class="fa fa-close"></i></a>
			<span class="text-left bold customizer-heading"><i class="fa fa-cog"></i> <?= _l('setting_bar_heading'); ?></span>
		</li>
		<?php 
		//Insertion de l'ID de l'entreprise
    	$id_E = $this->session->userdata('staff_user_id_entreprise');

		$menu_active = get_option('setup_menu_active');
		$menu_active = json_decode($menu_active);
		$total_setup_items = count($menu_active->setup_menu_active);
        $m                 = 0;
		foreach($menu_active->setup_menu_active as $item){
			if(isset($item->permission) && !empty($item->permission)){
				/*if(!has_permission_settings($item->permission, '', 'create') || !has_permission_settings($item->permission, '', 'edit')){
                    $total_setup_items--;
					continue;
				}*/
				if(!is_admin()){
                    $total_setup_items--;
					continue;
				}
			}
			$submenu = false;
			$remove_main_menu = false;
			$url = '';
			if(isset($item->children)){
				$submenu                 = true;
				$total_sub_items_removed = 0;
				foreach($item->children as $_sub_menu_check){
					if(isset($_sub_menu_check->permission) && !empty($_sub_menu_check->permission)){
						/*if(!has_permission_settings($_sub_menu_check->permission, '', 'create') || !has_permission_settings($_sub_menu_check->permission, '', 'edit')){
							$total_sub_items_removed++;
						}*/
						if(!is_admin()){
							$total_sub_items_removed++;
						}
					}
				}
				if($total_sub_items_removed == count($item->children)){
					$submenu          = false;
					$remove_main_menu = true;
                    $total_setup_items--;
				}
			} else {
				// child items removed
				if($item->url == '#'){
					continue;
				}
				$url = $item->url;
			}
			if($remove_main_menu == true){
				continue;
			}

			$url = $item->url;
			if(!_startsWith($url,'http://') && $url != '#'){
					$url = admin_url($url);
			}

			$name = _l($item->name);
			if(strpos($name,'translate_not_found') !== false){
				$name = $item->name;
			}

			if($item->name == 'acs_settings') {
			?>	
				<li>
					<a href="<?= $url; ?>"><i class="<?= $item->icon; ?> menu-icon"></i><?= $name; ?>
						<?php if($submenu == true){ ?>
						<span class="fa arrow"></span>
						<?php } ?>
					</a>
				</li>
			<?php
			}
			if($item->name == 'menu_builder'){
			?>
				<li>
				<a href="<?= $url; ?>"><i class="<?= $item->icon; ?> menu-icon"></i><?= $name; ?>
					<?php if($submenu == true){ ?>
					<span class="fa arrow"></span>
					<?php } ?>
				</a>
				<?php if(isset($item->children)){ ?>
				<ul class="nav nav-second-level collapse" aria-expanded="false">
					<?php foreach($item->children as $submenu){
						if(isset($submenu->permission) && !empty($submenu->permission)){
							/*if(!has_permission_settings($submenu->permission,'','create') || !has_permission_settings($submenu->permission,'','edit')){
								continue;
							}*/
							if(!is_admin()){
								continue;
							}
						}

						$name = _l($submenu->name);
						if(strpos($name,"translate_not_found") !== false){
							$name = $submenu->name;
						}

						$url = $submenu->url;
						if(!_startsWith($url,"http://")){
							$url = admin_url($url);
						}

						?>
						<li>
							<a href="<?= $url; ?>">
								<?php if(!empty($submenu->icon)){ ?>
								<i class="<?= $submenu->icon; ?> menu-icon"></i>
								<?php } ?>
								<?= $name; ?>
							</a>
						</li>
							<?php } ?>
						</ul>
						<?php } ?>
					</li>
			<?php
			}

			if($item->name != 'menu_builder' && $item->name != 'acs_settings')
			{
                if (isset($item->display) && get_permission_module($item->display) == 0) {
                    continue;
                }
			?>
			<li><a href="<?= $url; ?>"><i class="<?= $item->icon; ?> menu-icon"></i><?= $name; ?>
				<?php if($submenu == true){ ?>
				<span class="fa arrow"></span>
				<?php } ?>
			</a>
			<?php if(isset($item->children)){ ?>
			<ul class="nav nav-second-level collapse" aria-expanded="false">
				<?php foreach($item->children as $submenu){
                    if (isset($submenu->display) && get_permission_module($submenu->display) == 0) {
                        continue;
                    }
					if(isset($submenu->permission) && !empty($submenu->permission)){
						/*if(!has_permission_settings($submenu->permission, '', 'create') || !has_permission_settings($submenu->permission, '', 'edit')){
							continue;
						}*/
						if(!is_admin()){
							continue;
						}
					}
					$name = _l($submenu->name);
					if(strpos($name,'translate_not_found') !== false){
						$name = $submenu->name;
					}
					$url = $submenu->url;

					if(!_startsWith($url,'http://')){
						$url = admin_url($url);
					}
                    
					?>
					<li>
						<a href="<?= $url; ?>">
							<?php if(!empty($submenu->icon)){ ?>
							<i class="<?= $submenu->icon; ?> menu-icon"></i>
							<?php } ?>
							<?= $name; ?>
						</a>
					</li>
						<?php } ?>
					</ul>
					<?php } ?>
				</li>
				<?php }} ?>
			</ul>
		</div>
