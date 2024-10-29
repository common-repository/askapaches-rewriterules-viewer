<?php
/**
* Plugin Name: AskApache RewriteRules Viewer
* Short Name: AA RewriteRules
* Description: Displays the Internal WordPress Rewrite Rules in Detailed Glory.
* Author: AskApache
* Version: 3.2
* Requires at least: 2.3
* Tested up to: 3.0-beta1
* Tags: askapache, rewriterules, permalinks, links, perma, rewrite, structure, permalink, function, category, search, property, endpoint, request, author, private, attachment, permastruct, replace, redirect, matches, generate, struct, comment, default, permalinks, comments, endpoints, queries, pagename, feedregex, tagname, attachments, trackback, verbose, rewritecode, postname, robots, queryreplace, permastructs, htaccess, feedquery, categories, subfeedquery, rewriterule, rewritereplace, feedname, trackbackregex, rewritecond, regexes, redirects, authors, withcomments, feedindex, trackbacks, trackbackquery, trackbackmatch, trackbackindex, taxonomy, postid, pageregex, pagequery, pagematch
* Contributors: AskApache, cduke250
* WordPress URI: http://wordpress.org/extend/plugins/askapaches-rewriterules-viewer/
* Author URI: http://www.askapache.com/
* Donate URI: http://www.askapache.com/donate/
* Plugin URI: http://www.askapache.com/htaccess/rewriterule-viewer-plugin.html
*
*
* AskApache RewriteRules Viewer - Displays the Internal WordPress Rewrite Rules
* Copyright (C) 2010	AskApache.com
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.	If not, see <http://www.gnu.org/licenses/>.
*/


!defined( 'ABSPATH' ) || !function_exists( 'add_options_page' ) || !function_exists( 'add_action' ) || !function_exists( 'wp_die' ) && die( 'death by askapache firing squad' );
!defined( 'COOKIEPATH' ) && define( 'COOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('home') . '/') );
!defined( 'SITECOOKIEPATH' ) && define( 'SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('siteurl') . '/') );
!defined( 'ADMIN_COOKIE_PATH' ) && define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin' );
!defined( 'PLUGINS_COOKIE_PATH' ) && define( 'PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL) );
!defined( 'WP_CONTENT_DIR' ) && define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
!defined( 'WP_PLUGIN_DIR' ) && define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
!defined( 'WP_CONTENT_URL' ) && define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content' );
!defined( 'WP_PLUGIN_URL' ) && define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );



if ( !in_array('AskApacheRewriteRuleViewer', (array)get_declared_classes() ) && !class_exists( 'AskApacheRewriteRuleViewer' ) ) :
/**
 * AskApacheRewriteRuleViewer
 *
 * @package 
 * @author webmaster@askapache.com
 * @copyright AskApache
 * @version 2010
 * @access public
 */
class AskApacheRewriteRuleViewer
{
	var $plugin;


	/**
	 * AskApacheRewriteRuleViewer::AskApacheRewriteRuleViewer()
	 */
	function AskApacheRewriteRuleViewer(){}


	function init()
	{
		$pb=preg_replace( '|^' . preg_quote(WP_PLUGIN_DIR, '|') . '/|', '', __FILE__ );
		$ph=str_replace('.php','','settings_page_'.basename(__FILE__));
		add_action( 'activate_' . $pb, array(&$this, 'activate') );
		add_action( 'deactivate_' . $pb, array(&$this, 'deactivate') );
		add_filter( 'plugin_action_links_' . $pb, array(&$this, 'plugin_action_links') );
		add_action( 'admin_menu', array(&$this, 'admin_menu') );
	}
	

	/**
	 * AskApacheRewriteRuleViewer::activate()
	 */
	function activate()
	{
		delete_option( "askapache_rewriterules_viewer_plugin" );
		$this->InitOptions();
	}



	/**
	 * AskApacheRewriteRuleViewer::deactivate()
	 */
	function deactivate()
	{
		delete_option( "askapache_rewriterules_viewer_plugin" );
	}



	/**
	 * AskApacheRewriteRuleViewer::LoadOptions()
	 */
	function LoadOptions()
	{
		
		$this->plugin = get_option( 'askapache_rewriterules_viewer_plugin' );
		if(!$this->plugin || !is_array($this->plugin)) $this->plugin=$this->get_plugin_data();
	}



	/**
	 * AskApacheRewriteRuleViewer::InitOptions()
	 */
	function InitOptions()
	{
		$this->plugin=$this->get_plugin_data();
		update_option( 'askapache_rewriterules_viewer_plugin', $this->plugin );
	}



	/**
	 * AskApacheRewriteRuleViewer::SaveOptions()
	 */
	function SaveOptions()
	{
		update_option( 'askapache_rewriterules_viewer_plugin', $this->plugin );
	}


	/**
	 * AskApacheRewriteRuleViewer::admin_menu()
	 */
	function admin_menu()
	{
		$this->LoadOptions();
		add_options_page( $this->plugin['Plugin Name'], $this->plugin['Short Name'], 'administrator', $this->plugin['page'], array(&$this, 'options_page') );
	}



	/**
	 * AskApacheRewriteRuleViewer::plugin_action_links()
	 * @param mixed $links
	 */
	function plugin_action_links( $links )
	{
		return array_merge( array('<a href="' . admin_url($this->plugin['action']) . '">Settings</a>'), $links );
	}




	
	
	/**
	 * AskApacheRewriteRuleViewer::options_page()
	 */
	function options_page()
	{
		global $is_apache, $wp_rewrite, $wp_query;
		$this->LoadOptions();
		if(!current_user_can('administrator'))die();
		?>
		<div class="wrap" style="max-width:1400px;">
		<h3><?php echo $this->plugin['Plugin Name'];?> Options - <a style="font-size:12px;" href="http://www.twitter.com/askapache">@AskApache</a> - <a style="font-size:12px;" href="http://www.askapache.com/feed/">News/Updates</a></h3>
				
		<form id="aarewriterules_viewer_main_settings" method="post" action="<?php echo $this->plugin['action']; ?>">
		<?php wp_original_referer_field( true, 'previous' ); wp_nonce_field( 'aarewriterules_viewer_form' ); ?>
    
<table class="form-table"><h3>Rewrite-Related Settings</h3>
<?php $this->handle_results(array('using_permalinks','using_index_permalinks','using_mod_rewrite_permalinks'));?>


<?php
$vars=array(
'use_trailing_slashes'=>'Whether to add trailing slashes.',
'use_verbose_rules'=>'Whether to write every mod_rewrite rule for WP. This is off by default',
'use_verbose_page_rules'=>'Whether to write every mod_rewrite rule for WP pages.',
'permalink_structure'=>'Default permalink structure for WP.',
'category_base'=>'Customized or default category permalink base ( askapache.com/xx/tagname ).',
'tag_base'=>'Customized or default tag permalink base ( askapache.com/xx/tagname ).',
'category_structure'=>'Permalink request structure for categories.',
'tag_structure'=>'Permalink request structure for tags.',
'author_base'=>'Permalink author request base ( askapache.com/author/authorname ).',
'author_structure'=>'Permalink request structure for author pages.',
'date_structure'=>'Permalink request structure for dates.',
'page_structure'=>'Permalink request structure for pages.',
'search_base'=>'Search permalink base ( askapache.com/search/query ).',
'search_structure'=>'Permalink request structure for searches.',
'comments_base'=>'Comments permalink base.',
'feed_base'=>'Feed permalink base.',
'comments_feed_structure'=>'Comments feed request structure permalink.',
'feed_structure'=>'Feed request structure permalink.',
'front'=>'Front URL path. If permalinks are turned off. The WP/index.php will be the front portion. If permalinks are turned on',
'root'=>'Root URL path to WP (without domain). The difference between front property is that WP might be located at askapache.com/WP/. The root is the WP/ portion.',
'index'=>'Permalink to the home page.',
'matches'=>'Request match string.',
'rules'=>'Rewrite rules to match against the request to find the redirect or query.',
'extra_rules'=>'Additional rules added external to the rewrite class.',
'extra_rules_top'=>'Additional rules that belong at the beginning to match first.',
'non_wp_rules'=>'Rules that don\'t redirect to WP\'s index.php. These rules are written to the mod_rewrite portion of the .htaccess.',
'extra_permastructs'=>'Extra permalink structures.',
'endpoints'=>'Endpoints permalinks',
'rewritecode'=>'Permalink structure search for preg_replace.',
'rewritereplace'=>'Preg_replace values for the search.',
'queryreplace'=>'Search for the query to look for replacing.',
'feeds'=>'Supported default feeds.',
);

		foreach($vars as $k=>$d){
			if(!isset($wp_rewrite->$k))continue;
			$v=$wp_rewrite->$k;
			if(is_bool($v))	echo '<tr valign="top"><th scope="row"><label><strong>'.$k.'</strong></label></th><td><input type="text" value="'.(($v===true) ? 'TRUE' : 'FALSE').'" class="small-text code" /></td></tr>';
			elseif(is_string($v))echo '<tr valign="top"><th scope="row"><label><strong>'.$k.'</strong></label></th><td><input type="text" value="'.$v.'" class="regular-text code" /></td></tr>';
			elseif(is_array($v) && sizeof($v)>0)
			{
				$vo='';	
				foreach($v as $vv) {
					if(is_array($vv)) foreach($vv as $vvv) $vo.="{$vvv}\n";
					else $vo.="{$vv}\n";
				}
				$rows=substr_count($vo."\n", "\n");	$rows=( $rows > 30 ) ? 30 : (($rows < 4) ? $rows : ($rows -2));
				echo '<tr valign="top"><th scope="row"><strong>'.$k.'</strong></th><td><fieldset><p><label>'.$d.'</label></p><p><textarea rows="'.$rows.'" cols="50" class="large-text code">'.$vo.'</textarea></p></fieldset></td></tr>';
			}
		}

		// robots.txt
		$robots_rewrite = array('robots\.txt$' => $wp_rewrite->index . '?robots=1');

		//Default Feed rules - These are require to allow for the direct access files to work with permalink structure starting with %category%
		$default_feeds = array(	'.*wp-atom.php$'	=>	$wp_rewrite->index . '?feed=atom',
								'.*wp-rdf.php$'		=>	$wp_rewrite->index . '?feed=rdf',
								'.*wp-rss.php$'		=>	$wp_rewrite->index . '?feed=rss',
								'.*wp-rss2.php$'	=>	$wp_rewrite->index . '?feed=rss2',
								'.*wp-feed.php$'	=>	$wp_rewrite->index . '?feed=feed',
								'.*wp-commentsrss2.php$'	=>	$wp_rewrite->index . '?feed=rss2&withcomments=1');

		// Post
		$post_rewrite = $wp_rewrite->generate_rewrite_rules($wp_rewrite->permalink_structure, EP_PERMALINK);
		$post_rewrite = apply_filters('post_rewrite_rules', $post_rewrite);

		// Date
		$date_rewrite = $wp_rewrite->generate_rewrite_rules($wp_rewrite->get_date_permastruct(), EP_DATE);
		$date_rewrite = apply_filters('date_rewrite_rules', $date_rewrite);

		// Root
		$root_rewrite = $wp_rewrite->generate_rewrite_rules($wp_rewrite->root . '/', EP_ROOT);
		$root_rewrite = apply_filters('root_rewrite_rules', $root_rewrite);

		// Comments
		$comments_rewrite = $wp_rewrite->generate_rewrite_rules($wp_rewrite->root . $wp_rewrite->comments_base, EP_COMMENTS, true, true, true, false);
		$comments_rewrite = apply_filters('comments_rewrite_rules', $comments_rewrite);

		// Search
		$search_structure = $wp_rewrite->get_search_permastruct();
		$search_rewrite = $wp_rewrite->generate_rewrite_rules($search_structure, EP_SEARCH);
		$search_rewrite = apply_filters('search_rewrite_rules', $search_rewrite);

		// Categories
		$category_rewrite = $wp_rewrite->generate_rewrite_rules($wp_rewrite->get_category_permastruct(), EP_CATEGORIES);
		$category_rewrite = apply_filters('category_rewrite_rules', $category_rewrite);

		// Tags
		$tag_rewrite = $wp_rewrite->generate_rewrite_rules($wp_rewrite->get_tag_permastruct(), EP_TAGS);
		$tag_rewrite = apply_filters('tag_rewrite_rules', $tag_rewrite);

		// Authors
		$author_rewrite = $wp_rewrite->generate_rewrite_rules($wp_rewrite->get_author_permastruct(), EP_AUTHORS);
		$author_rewrite = apply_filters('author_rewrite_rules', $author_rewrite);

		// Pages
		$page_rewrite = $wp_rewrite->page_rewrite_rules();
		$page_rewrite = apply_filters('page_rewrite_rules', $page_rewrite);



echo '<tr valign="top"><th scope="row"><strong>feeds</strong></th><td><fieldset><p><label>Supported default feeds.</label></p><p><textarea class="large-text code" cols="50" rows="30">';
	print_r(
			array(
			'extra_rules_top'=>print_r($wp_rewrite->extra_rules_top,1),
			'robots_rewrite'=>print_r($robots_rewrite,1),
			'default_feeds'=>print_r($default_feeds,1),
			'page_rewrite'=>print_r($page_rewrite,1),
			'root_rewrite'=>print_r($root_rewrite,1),
			'comments_rewrite'=>print_r($comments_rewrite,1),
			'search_rewrite'=>print_r($search_rewrite,1),
			'category_rewrite'=>print_r($category_rewrite,1),
			'tag_rewrite'=>print_r($tag_rewrite,1),
			'author_rewrite'=>print_r($author_rewrite,1),
			'date_rewrite'=>print_r($date_rewrite,1),
			'post_rewrite'=>print_r($post_rewrite,1),
			'extra_rules'=>print_r($wp_rewrite->extra_rules,1)
			)
	);
echo '</textarea></p></fieldset></td></tr>';
?>
</table>


<table class="form-table"><h3>Permastructs</h3>
<?php $this->handle_results(array('get_date_permastruct','get_year_permastruct','get_month_permastruct','get_day_permastruct','get_category_permastruct','get_tag_permastruct','get_author_permastruct','get_search_permastruct','get_page_permastruct','get_feed_permastruct','get_comment_feed_permastruct'));?></table>

<table class="form-table"><h3>Some Page Generated Rewrites</h3>
<?php $this->handle_results(array('page_uri_index','page_rewrite_rules'));?></table>

<table class="form-table"><h3>The Rewrite Rules</h3>
<?php $this->handle_results(array('wp_rewrite_rules','mod_rewrite_rules'));?></table>

</form>


		<div style="width:300px;float:left;">
		<h3>Articles from AskApache</h3>
		<ul>
		<li><a href="http://www.askapache.com/seo/seo-secrets.html">SEO Secrets of AskApache.com</a></li>
		<li><a href="http://www.askapache.com/seo/seo-advanced-pagerank-indexing.html">Controlling Pagerank and Indexing</a></li>
		<li><a href="http://www.askapache.com/htaccess/apache-htaccess.html">Ultimate <strong>.htaccess</strong> Tutorial</a></li>
		<li><a href="http://www.askapache.com/seo/updated-robotstxt-for-wordpress.html">Robots.txt Info for WordPress</a></li>
		</ul>
		<p><br class="clear" /></p>
		</div>
		<p><br class="clear" /></p>
		</div>
		<?php
	}


	function handle_results($incoming)
	{
		global $wp_rewrite;
		static $rewrite_methods=false;
		if(!$rewrite_methods) $rewrite_methods=get_class_methods( 'WP_Rewrite' );
		foreach((array)$incoming as $k) :
			if( in_array($k, $rewrite_methods) )$v=$wp_rewrite->{$k}();
			elseif( function_exists($k) ) $v=$k();
			else continue;

			if($k=='mod_rewrite_rules')$v=explode("\n",$v);
			if(is_bool($v))	echo '<tr valign="top"><th scope="row"><label><strong>'.$k.'</strong></label></th><td><input type="text" value="'.(($v===true) ? 'TRUE' : 'FALSE').'" class="small-text code" /></td></tr>';
			elseif(is_string($v))echo '<tr valign="top"><th scope="row"><label><strong>'.$k.'</strong></label></th><td><input type="text" value="'.$v.'" class="regular-text code" /></td></tr>';
			elseif(is_array($v) && sizeof($v)>0)
			{
				$vo='';	
				foreach($v as $vv) {
					if(is_array($vv)) foreach($vv as $vvv) $vo.="{$vvv}\n";
					else $vo.="{$vv}\n";
				}
				$rows=substr_count($vo."\n", "\n");	$rows=( $rows > 30 ) ? 30 : (($rows < 4) ? $rows : ($rows -2));
				echo '<tr valign="top"><th scope="row"><strong>'.$k.'</strong></th><td><fieldset><p><textarea rows="'.$rows.'" cols="50" class="large-text code">'.$vo.'</textarea></p></fieldset></td></tr>';
			}
			
		endforeach;
	}



	/**
	 * AskApacheRewriteRuleViewer::get_plugin_data()
	 * @param mixed $find
	 */
	function get_plugin_data( $find = array('Description', 'Author', 'Version', 'DB Version', 'Requires at least', 'Tested up to', 'WordPress', 'Plugin', 'Plugin Name', 'Short Name', 'Domain Path', 'Text Domain', '(?:[a-z]{2,25})? URI') )
	{
		$fp = fopen( __FILE__, 'r' );
		if ( !is_resource($fp) ) return false;
		$data = fread( $fp, 1000 );
		if ( is_resource($fp) ) fclose( $fp );

		$mtx = $plugin = array();
		preg_match_all( '/(' . join('|', $find) . ')\:[\s\t]*(.+)/i', $data, $mtx, PREG_SET_ORDER );
		foreach ( $mtx as $m ) $plugin[trim( $m[1] )] = str_replace( array("\r", "\n", "\t"), '', trim($m[2]) );
		$plugin['page'] = basename( __FILE__ );
		$plugin['pb'] = preg_replace( '|^' . preg_quote(WP_PLUGIN_DIR, '|') . '/|', '', __FILE__ );
		$plugin['Title'] = '<a href="' . $plugin['Plugin URI'] . '" title="' . __( 'Visit plugin homepage' ) . '">' . $plugin['Plugin Name'] . '</a>';
		$plugin['Author'] = '<a href="' . $plugin['Author URI'] . '" title="' . __( 'Visit author homepage' ) . '">' . $plugin['Author'] . '</a>';
		$plugin['hook'] = 'settings_page_' . rtrim( $plugin['page'], '.php' );
		$plugin['action'] = 'options-general.php?page=' . $plugin['page'];

		return $plugin;
	}

}
endif;


$AskApacheRewriteRuleViewer = new AskApacheRewriteRuleViewer();
add_action('init',array(&$AskApacheRewriteRuleViewer, 'init'));
?>