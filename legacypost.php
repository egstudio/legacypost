<?php
/*
Plugin Name: Legacy Post
Plugin URI: http://www.egstudio.biz/legacypostplugin
Description: Making 'fake' posts which redirects to legacy website to keep old links rank on search engines
Author: egstudio.biz (Liad Guez, Itay Sidis, Guy Maliar)
Version: 1.1
Author URI: http://www.egstudio.biz/
*/

if (!class_exists("LegacyPost")) {
	class LegacyPost {
		public $_errors = array();
		
		private $_post_id = null;
		
		static public $_table_name = 'legacy_posts';

		function __construct() {
			self::is_valid_user();
			if (isset($_GET['id']))
				$this->_post_id = $_GET['id'];
			$pages = array();
			$pages[] = add_menu_page('Legacy Posts', 'Legacy Posts', 'manage_options', 'legacy-post', array($this, 'view_all_posts'), null, 6);
			$pages[] = add_submenu_page('legacy-post', 'Add New', 'Add New', 'manage_options', 'legacy-post-new', (array($this, 'view_new_post')));
			foreach ($pages as $page)
				add_action('admin_print_styles-' . $page, 'legacypost_plugin_admin_styles');
		}
		
		private function get_category_last_position($category) {
			return (int)(($this->fix_category_last_position($category)) + 1);
		}
		
		private function fix_category_last_position($category) {
			global $wpdb;
			$table = $wpdb->prefix.self::$_table_name;
			$posts = $wpdb->get_results("SELECT id, position FROM $table WHERE category = '$category' ORDER BY position ASC", ARRAY_A);
			$i = 0;
			$posts_size = sizeof($posts);
			for ($i; $i < $posts_size; $i++)
				$posts[$i]['position'] = $i+1;
			foreach ($posts as $post) {
				try {
					$this->update($post);
				}
				catch (Exception $e) {
					$this->_errors[] = $e->getMessage();
				}
			}
			return $i;
		}
		
		private function add($args) {
			global $wpdb;
			$table = $wpdb->prefix.self::$_table_name;
			$args['position'] = $this->get_category_last_position($args['category']);
			$wpdb->insert($table, $args);
			if ($wpdb->insert_id === false)
				throw new Exception('Could not save content to database.');
			return $wpdb->insert_id;
		}
		
		private function update($args) {
			global $wpdb;
			$table = $wpdb->prefix.self::$_table_name;
			if (!isset($args['position']))
				$args['modified'] = date('Y-m-d H:i:s');
			$result = $wpdb->update($table, $args, array('id' => $args['id']));
			if ($result === false)
				throw new Exception("Could not update {$args['id']} into database.");
			return true;
		}
		
		public function view_all_posts() {
			global $wpdb;
			$table = $wpdb->prefix.self::$_table_name;
			$sql = "SELECT id, title, content, img, img_title, link, category, position, tags, created, modified FROM $table";
			if (isset($_POST['show_category']) && $_POST['show_category'] != '0')
				$sql .= " WHERE category LIKE '%{$_POST['show_category']}%' ORDER BY position ASC";
			elseif (isset($_POST['show_tag']) && $_POST['show_tag'] != '0')
				$sql .= " WHERE tags LIKE '%{$_POST['show_tag']}%' ORDER BY position ASC";
			else
				$sql .= " ORDER BY category, position ASC";
			$posts = $wpdb->get_results($sql,OBJECT);
			include_once 'php/admin_show.php';
		}
		
		public function view_new_post() {
			if (isset($_POST['legacy-post-submit'])) {
				$args = array(
					'title'		=> 	$_POST['title'],
					'content'	=> 	$_POST['content'],
					'img'		=> 	$_POST['img'],
					'img_title'	=> 	$_POST['img_title'],
					'link'		=>	$_POST['link'],
					'category' 	=>	implode(',', $_POST['post_category']),
					'tags'		=> 	implode(',', $_POST['tax_input']['post_tag']),
					'created'	=>	date('Y-m-d H:i:s'),
					'modified'	=> 	date('Y-m-d H:i:s')
					);
				try {
					if (isset($_GET['id'])) {
						$args['id'] = $_GET['id'];
						$result = $this->update($args);
					}
					else
						$result = $this->add($args);
				}
				catch (Exception $e) {
					$this->_errors[] = $e->getMessage();
				}
				
				if ($result !== false) {
					$this->_errors[] = 'Success!';
					}
			}
			include_once 'php/errors.php';
			if (isset($_POST['legacy-post-submit']) || isset($_POST['legacy-post-show-category-form'])) {
				$this->view_all_posts();
			}
			else {
				if (isset($this->_post_id))
					$data = self::get_post($this->_post_id);
				require_once('./includes/meta-boxes.php');
				include_once 'php/admin_post_new.php';
			}
		}

		static public function install() {
			global $wpdb;
			$table = $wpdb->prefix.self::$_table_name;
			$sql = "CREATE TABLE  $table (
			`id` MEDIUMINT( 9 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`title` VARCHAR( 255 ) NOT NULL ,
			`content` TEXT NOT NULL ,
			`img` VARCHAR( 255 ) NOT NULL ,
			`img_title` VARCHAR( 255 ) NOT NULL ,
			`link` VARCHAR( 255 ) NOT NULL ,
			`category` VARCHAR( 255 ) NOT NULL ,
			`position` SMALLINT UNSIGNED NOT NULL ,
			`tags` VARCHAR( 255 ) NOT NULL ,
			`created` TIMESTAMP NOT NULL ,
			`modified` TIMESTAMP NOT NULL
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
		static public function uninstall() {
			global $wpdb;
			$table = $wpdb->prefix.self::$_table_name;
			$wpdb->query("DROP TABLE $table");
		}
		
		static private function is_valid_user() {
			if (!current_user_can( 'manage_options'))
				wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		static private function get_post($id) {
			global $wpdb;
			$table = $wpdb->prefix.self::$_table_name;
			return $wpdb->get_row("SELECT * FROM $table WHERE id = '$id'", OBJECT);
		}
		
		static public function get_legacy_posts(){
			global $wpdb;
			$table = $wpdb->prefix.self::$_table_name;
			
			$sql = "SELECT id, title, content, img, img_title, link, category, position, created, modified FROM $table";
			$results = $wpdb->get_results($sql,OBJECT);
			return $results;
		}
	}
}

function legacypost_add_admin_panel() {
	$legacypost = new LegacyPost();
}

function legacypost_add_admin_panel_scripts() {
	wp_register_style('legacypost-style', plugins_url('/css/legacypost.css', __FILE__));
	wp_register_script('jquery-draggable', plugins_url('/js/jquery-ui-1.8.20.custom.min.js', __FILE__));	
	wp_register_script('legacypost-script', plugins_url('/js/legacypost.js', __FILE__));
}

function legacypost_plugin_admin_styles() {
	wp_enqueue_style('legacypost-style');
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-draggable');	
	wp_enqueue_script('legacypost-script');
	wp_localize_script('legacypost-script', 'LegacyPost', array('legacyNonce' => wp_create_nonce('legacypost-nonce')));
}


function update_position() {
	header( "Content-Type: application/json" );
	if (!wp_verify_nonce($_POST['nonce'], 'legacypost-nonce'))
		die ('error');
	if (current_user_can('manage_options')) {
		global $wpdb;
		$id = $_POST['post-id'];
		$position = $_POST['post-position'];
		$table = $wpdb->prefix.LegacyPost::$_table_name;
		$this_position = $wpdb->get_row("SELECT position FROM $table WHERE id = $id")->position;
		$other_id = $wpdb->get_row("SELECT id FROM $table WHERE position = $position")->id;
		$res_old = $wpdb->update($table, array('position' => $this_position), array('id' => $other_id));
		$res_new = $wpdb->update($table, array('position' => $position), array('id' => $id));
		if ($res_old === false)
			echo 'error old';
		elseif ($res_new === false)
			echo 'error new';
		else
			echo json_encode(array('old' => $other_id));
	}	
	die();
}

function delete_post() {
	header( "Content-Type: application/json" );
	if (!wp_verify_nonce($_POST['nonce'], 'legacypost-nonce'))
		die ('error');
	if (current_user_can('manage_options')) {
		global $wpdb;
		$id = $_POST['post-id'];
		$table = $wpdb->prefix.LegacyPost::$_table_name;
		$result = $wpdb->query("DELETE FROM $table WHERE id = '$id'");
		if ($result === false) {
			echo false;
		}
		echo true;
	}
	die();
}

// MAIN

if (class_exists("LegacyPost")) {

	// Install/Uninstall Hooks
	register_activation_hook(__FILE__, array('LegacyPost', 'install'));
	register_deactivation_hook(__FILE__, array('LegacyPost', 'uninstall'));
	
	// Admin Menu Action
	add_action('admin_enqueue_scripts', 'legacypost_add_admin_panel_scripts');
	add_action('admin_menu', 'legacypost_add_admin_panel');
	add_action('wp_ajax_update_position_submit', 'update_position');
	add_action('wp_ajax_delete_post_submit', 'delete_post');
}

