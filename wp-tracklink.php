<?php
/*
Plugin Name: WP Tracklink
Plugin URI: https://ispire.me/wp-tracklink
Description: Tracks referrer links plus how often they have clicked and displays them on your Wordpress widget sidebar. This will increase your page ranking due to backlink and faster crawler indexing. Best pageranked and most clicked links gets an higher position in sidebar. Disabling specific links is possible via the settings tab. Visit official site at https://ispire.me/wp-tracklink for more infos.
Version: 0.4.9
Author: Julian Sternberg
Author URI: https://ispire.me
License: GPLv2

Copyright 2016 Julian Sternberg (jules@ispire.me)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
$tl_db_version = "1.6";

// Prepare Database on activation/deactivation

register_activation_hook(__FILE__, 'tl_db_install');
register_activation_hook(__FILE__, 'tl_db_data');

function tl_db_install()
{
	global $wpdb, $tl_db_version;
	$tl_db_table = $wpdb->prefix . "tracklink";
	$installed_ver = get_option('tl_db_version');
	if ($installed_ver != $tl_db_version || $wpdb->get_var("show tables like '%tracklink'") != $tl_db_table)
	{
		$sql = "CREATE TABLE " . $tl_db_table . " (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `time` datetime NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `link` VARCHAR(700) DEFAULT '' NOT NULL,
            `clicks` mediumint(11) DEFAULT '1' NOT NULL,
            `pr` mediumint(1) DEFAULT '0' NOT NULL,
            `active` mediumint(1) DEFAULT '1' NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY title (title)
            );";
		require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta($sql);
		update_option("tl_db_version", $tl_db_version);
	}
}

function tl_update_db_check()
{
	global $tl_db_version;
	$installed_ver = get_option('tl_db_version');
	if ($installed_ver != $tl_db_version)
	{
		tl_db_install();
	}
}

add_action('plugins_loaded', 'tl_update_db_check');

function tl_db_data()
{
	global $wpdb;
	$tl_db_table = $wpdb->prefix . "tracklink";
	$plugin_title = "ispire.me";
	$plugin_link = "https://ispire.me/wp-tracklink";
	$plugin_clicks = "999999";
	$rows_affected = $wpdb->insert($tl_db_table, array(
		'time' => current_time('mysql') ,
		'title' => $plugin_title,
		'link' => $plugin_link,
		'clicks' => $plugin_clicks
	));
}

// Main tasks

class wptracklink

{
	public

	function __construct()
	{
		if (is_admin())
		{
			add_action('admin_menu', array(
				$this,
				'add_plugin_page'
			));
			add_action('admin_init', array(
				$this,
				'page_init'
			));
		}
	}

	public

	function add_plugin_page()
	{

		// This page will be under "Settings"

		add_options_page('Tracklink Settings', 'Tracklink Settings', 'manage_options', 'tracklink-settings', array(
			$this,
			'create_admin_page'
		));
	}

	public

	function create_admin_page()
	{
?>
        <div class="wrap">
        <?php
		screen_icon();
?>
        <h2>Tracklink Settings</h2>
        <form method="post" action="options.php">
        <?php

		// This print out all hidden setting fields

		settings_fields('test_option_group');
		do_settings_sections('tracklink-settings');
?>
        <?php
		submit_button();
?>
        </form>
        <?php
		if (!isset($_GET['tab']) || $_GET['tab'] != "1")
		{
			$linktitle = "Disable this Link";
			$linkdescr = "disable";
			$linkstate = 0;
			$activestate = 1;
			$activetab = "nav-tab-active";
			$inactivetab = "";
			$rdr_admlink = "admin.php?page=tracklink-settings&tab=active";
		}
		elseif (isset($_GET['tab']) && $_GET['tab'] == "1")
		{
			$linktitle = "Enable this Link";
			$linkdescr = "enable";
			$linkstate = 1;
			$activestate = 0;
			$activetab = "";
			$inactivetab = "nav-tab-active";
		} ?>

        <a href="?page=tracklink-settings&tab=0" class="nav-tab <?php
		echo $activetab; ?>">Active Tracked Links</a>
        <a href="?page=tracklink-settings&tab=1" class="nav-tab <?php
		echo $inactivetab; ?>">Inactive Tracked Links</a>

        <table class="wp-list-table widefat fixed posts">
        <thead>
        <tr>
        <th><?php
		_e('ID', 'tbl_tracklink'); ?></th>
        <th><?php
		_e('Last Active', 'tbl_tracklink'); ?></th>
        <th><?php
		_e('Title', 'tbl_tracklink'); ?></th>
        <th><?php
		_e('Link', 'tbl_tracklink'); ?></th>
        <th><?php
		_e('Clicks', 'tbl_tracklink'); ?></th>
        <th><?php
		_e('Pagerank', 'tbl_tracklink'); ?></th>
        <th><?php
		_e('Action', 'tbl_tracklink'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
		global $wpdb;
		$table_name = $wpdb->prefix . "tracklink";
		$maxlink_count_admin = esc_sql(get_option("tl_settings"));
		if ($maxlink_count_admin == "")
		{
			$maxlink_count_admin = 10;
		}

		$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE active=$activestate ORDER BY pr ASC, clicks DESC LIMIT 0 , $maxlink_count_admin");
		foreach($rows as $rows)
		{
?>
            <td><?php
			echo $rows->id; ?></td>
            <td><?php
			echo $rows->time; ?></td>
            <td><?php
			echo $rows->title; ?></td>
            <td><?php
			echo $rows->link; ?></td>
            <td><?php
			echo $rows->clicks; ?></td>
            <td><?php
			echo $rows->pr; ?></td>
            <td><p><a title="<?php
			echo $linktitle; ?>" href="?page=tracklink-settings&id=<?php
			echo $rows->id; ?>&enable=<?php
			echo $linkstate; ?>&tab=<?php
			echo $linkstate
?>"><?php
			echo $linkdescr; ?></a></p></td>
            </tr>
        <?php
		}

?>
        </tbody>
        </table>
        </div>
        <?php
		if (isset($_GET["enable"]))
		{
			$changestate = esc_sql($_GET["enable"]);
			$getid = esc_sql($_GET["id"]);
			$thetime = gmdate('Y-m-d H:i:s', (time() + (get_option('gmt_offset') * 3600)));
			$active = esc_sql($active);
			$id = esc_sql($id);
			$wpdb->update($table_name, array(
				'time' => $thetime,
				'active' => $changestate
			) , array(
				'ID' => $getid
			) , array(
				'%s',
				'%d'
			) , array(
				'%d'
			));
			wp_redirect(admin_url('admin.php?page=tracklink-settings&tab=' . $linkstate));
		}
	}

	public

	function page_init()
	{
		register_setting('test_option_group', 'array_key', array(
			$this,
			'check_ID'
		));
		add_settings_section('setting_section_id', 'Settings', array(
			$this,
			'print_section_info'
		) , 'tracklink-settings');
		add_settings_field('max_links', 'Show Max Links on table(default: 10)', array(
			$this,
			'create_an_id_field'
		) , 'tracklink-settings', 'setting_section_id');
	}

	public

	function check_ID($input)
	{
		if (is_numeric($input['max_links']))
		{
			$mid = $input['max_links'];
			if (get_option('tl_settings') === FALSE)
			{
				add_option('tl_settings', $mid);
			}
			else
			{
				update_option('tl_settings', $mid);
			}
		}
		else
		{
			$mid = '';
		}

		return $mid;
	}

	public

	function print_section_info()
	{
		print 'Enter your settings below:';
	}

	public

	function create_an_id_field()
	{
?><input type="text" id="input_whatever_unique_id_I_want" name="array_key[max_links]" value="<?php
		echo get_option('tl_settings');
?>" /><?php
	}
}
ob_start(); // avoid "headers already sent"
$wc_enet = new wptracklink();
ob_flush();

function check_pr($url)
{
	require_once ('lib/checkpr.php');

	return getpr($url);
}

function parse_link($url)
{
	$parsed = parse_url($url);
	$hostname = $parsed['host'];
	return $hostname;
}

function set_trackedlinks()
{
	$ref_link = $_SERVER["HTTP_REFERER"];
	$hosttitle = parse_link($ref_link);
	$blogurl = parse_link(get_bloginfo('url'));
	$searchvisits = get_option("tl_settings");
	if (isset($ref_link))
	{
		if (preg_match("/($blogurl)|(altavista\.com)|(aol\.com)|(baidu\.com)|(cuil\.com)|(ecosia\.org)|(excite\.com)|(go\.com)|(hotbot\.com)|(lycos\.com)|(gigablast\.com)|(galaxy\.com)|(yahoo\.com)|(sweetim\.com)|(icq\.com)|(yandex\.*)|(msn\.*)|(live\.*)|(alltheweb\.com)|(ask\.com)|(search\.*)|(google\.*)|(facebook\.*)|(wisenut\.com)|(mypoints\.com)|(linkedin\.*)|(optimizely\.com)|(t\.co)|(bit\.ly)|(bing\.*)|(duckduckgo\.com)|(360\.cn)|(nigma\.ru)/i", $ref_link) || preg_match("/bot|crawl|spider|slurp/i", $_SERVER['HTTP_USER_AGENT']))
		{

			// bypass sql query if source is a search engine, crawler or social network

		}
		else
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "tracklink";
			$thetime = gmdate('Y-m-d H:i:s', (time() + (get_option('gmt_offset') * 3600)));
			$hosttitle = esc_sql($hosttitle);
			$ref_link = esc_sql($ref_link);
			$pagerank = esc_sql(check_pr($hosttitle));
			$sql = "UPDATE " . $table_name . " SET clicks=clicks+1, time='$thetime' WHERE title='$hosttitle'";
			$sql_result = $wpdb->query($sql);

			// lookup backlinking only if no db entry exists

			if ($sql_result < 1)
			{
				$checkurl = (array)wp_remote_get($ref_link);

				// check if referer reachable and contains content linking

				if ((200 == $checkurl['response']['code']) && (strpos($checkurl['body'], $blogurl)))
				{

					// site reachable and keyword found

					if ($hosttitle != $blogurl && $ref_link != "")
					{
						if (is_numeric($pagerank))
						{
							$sql = "INSERT INTO " . $table_name . " (time,title,link,clicks,pr) VALUES ('$thetime', '$hosttitle','$ref_link',1,'$pagerank')  ON duplicate KEY UPDATE link='$ref_link', clicks=clicks+1, time='$thetime'";
							$wpdb->query($sql);
						}
					}
				}
			}
		}
	}
}

class wptracklink_widget extends WP_Widget

{
	function wptracklink_widget()
	{
		parent::WP_Widget(false, $name = 'WP Tracklink Widget');
	}

	function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$maxlink_count = esc_sql($instance['maxlink_count']);
		global $wpdb;
		if ($maxlink_count == "")
		{
			$maxlink_count = 10;
		}

		if ($title == "")
		{
			$title = "Active Backlinks";
		}

		$table_name = $wpdb->prefix . "tracklink";
		$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE active=1 ORDER BY pr ASC, clicks DESC LIMIT 0 , $maxlink_count ");

		// check if there are some old >30 days entries to delete from table

		$maxlink_count++;
		$wpdb->query("DELETE FROM $table_name WHERE time<=CURRENT_DATE - INTERVAL 30 DAY AND id NOT IN (SELECT id FROM (SELECT id FROM $table_name WHERE active=1 ORDER BY clicks DESC LIMIT 0 , $maxlink_count) t)");

		// check if pagerank within last 32 days have changed

		$prows = $wpdb->get_results("SELECT * FROM $table_name WHERE time<=CURRENT_DATE - INTERVAL 32 DAY");
		foreach($prows as $prows)
		{
			$thetimerow = gmdate('Y-m-d H:i:s', (time() + (get_option('gmt_offset') * 3600)));
			$prid = $prows->id;
			$prurl = $prows->title;
			$pagerank = check_pr($prurl);
			if ($pagerank == "")
			{
				$pagerank = 0;
			}

			$wpdb->query("UPDATE $table_name SET pr=$pagerank, time='$thetimerow' where id=$prid");
		}

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
?>
    <ul>
    <?php
		foreach($rows as $rows)
		{
?>
        <li><a rel="nofollow" href="<?php
			echo htmlentities($rows->link); ?>"><?php
			echo $rows->title;
?></a></li>
    <?php
		}

?>
    </ul>

    <?php
		echo $after_widget;
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['maxlink_count'] = strip_tags($new_instance['maxlink_count']);
		return $instance;
	}

	function form($instance)
	{
		$title = esc_attr($instance['title']);
		$maxlink_count = esc_attr($instance['maxlink_count']);
?>
         <p>
          <label for="<?php
		echo $this->get_field_id('title'); ?>"><?php
		_e('Title:'); ?></label>
          <input class="widefat" id="<?php
		echo $this->get_field_id('title'); ?>" name="<?php
		echo $this->get_field_name('title'); ?>" type="text" value="<?php
		echo $title; ?>" />
        </p>
                <p>
          <label for="<?php
		echo $this->get_field_id('maxlink_count'); ?>"><?php
		_e('Max Links on widget(default: 10)'); ?></label>
          <input class="widefat" id="<?php
		echo $this->get_field_id('maxlink_count'); ?>" name="<?php
		echo $this->get_field_name('maxlink_count'); ?>" type="text" value="<?php
		echo $maxlink_count; ?>" />
        </p>
        <?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("wptracklink_widget");'));
add_action("get_header", "set_trackedlinks");
