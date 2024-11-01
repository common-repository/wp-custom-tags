<?php
/**
 * @package wpcustomtags
 */
/*
Plugin Name: WP Custom Tags
Plugin URI: https://appsdevpk.com/
Description: Create and use custom riot tags in your site
Version: 0.1
Author: appsdevpk
Author URI: https://appsdevpk.com
License: GPLv2 or later
Text Domain: wpcustomtags
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

require_once dirname( __FILE__ ) . '/cmb2/init.php';

add_action( 'init', 'wpc_TagsInit' );
add_action( 'wp_enqueue_scripts', 'wpc_EnqueueScript');
add_action( 'admin_enqueue_scripts', 'wpc_AdminScripts' );
add_action( 'cmb2_admin_init', 'wpc_registerMetaBoxes' );
add_action( 'wp_footer', 'wpc_RiotTagsScripts' );
add_filter( 'query_vars', 'wpc_QueryVar');
add_action('template_redirect', 'wpc_TemplateRedirect');

function wpc_TagsInit() {
	register_post_type( 'wpctag',
		array(
			'labels' => array(
				'name' => __( 'WPCTags' ),
				'singular_name' => __( 'WPCTag' )
			),
			'public' => true,
			'has_archive' => true,
			'supports'=>array('title')
		)
	);
}
function wpc_registerMetaBoxes(){
	$prefix = 'wpctags_';
	$cmbrt = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'WPC Tag', 'cmb2' ),
		'object_types'  => array( 'wpctag' )
	) );
	$cmbrt->add_field( array(
		'name' => 'Tag Code',
		'id' => $prefix.'tag_code',
		'type' => 'textarea_code',
		'attributes' => array(
			'data-codeeditor' => json_encode( array(
				'codemirror' => array(
					'mode' => 'htmlmixed'
				),
			))
		)
	) );
	$cmbrt->add_field( array(
		'name' => 'Tag CSS',
		'id' => $prefix.'tag_css',
		'type' => 'textarea_code',
		'attributes' => array(
			'data-codeeditor' => json_encode( array(
				'codemirror' => array(
					'mode' => 'css'
				),
			))
		)
	) );
	$cmbrt->add_field( array(
		'name' => 'Tag Script',
		'id' => $prefix.'tag_script',
		'type' => 'textarea_code',
		'attributes' => array(
			'data-codeeditor' => json_encode( array(
				'codemirror' => array(
					'mode' => 'javascript'
				),
			))
		)
	) );
	$cmbrt->add_field( array(
		'name' => 'Server Code',
		'description'=>'Server side logic will be here, to call this script from your code use [serverlink] in your js code, other variable available is [homeurl]',
		'id' => $prefix.'tag_servercode',
		'type' => 'textarea_code',
		'attributes' => array(
			'data-codeeditor' => json_encode( array(
				'codemirror' => array(
					'mode' => 'application/x-httpd-php-open'
				),
			))
		)
	) );
}
function wpc_RiotTagsScripts() {
	$scriptUri = home_url().'/?wpcScript=1';
	?>
	<script src="<?php echo $scriptUri; ?>" type="riot/tag"></script>
	<script>
		riot.mount('*');
	</script>
	<?php
}

function wpc_EnqueueScript() {
	wp_enqueue_script( 'wpcRiotScript', plugins_url( 'js/riot+compiler.min.js', __FILE__ ), array());
}

function wpc_AdminScripts() {
	wp_enqueue_script( 'wpcHtmlMixedJs', plugins_url( 'js/htmlmixed.js', __FILE__ ), array('codemirror'));
	wp_enqueue_script( 'wpcCLikeJs', plugins_url( 'js/clike.js', __FILE__ ), array('codemirror'));
	wp_enqueue_script( 'wpcPhpJS', plugins_url( 'js/php.js', __FILE__ ), array('codemirror'));
	wp_enqueue_script( 'wpcActiveLineJS', plugins_url( 'js/active-line.js', __FILE__ ), array('codemirror'));
	wp_enqueue_script( 'wpcMatchBracketsJS', plugins_url( 'js/matchbrackets.js', __FILE__ ), array('codemirror'));
	wp_enqueue_script( 'wpcPhpParserJS', plugins_url( 'js/php-parser.min.js', __FILE__ ), array('codemirror'));
	wp_enqueue_script( 'wpcPhpLintJS', plugins_url( 'js/php-lint.js', __FILE__ ), array('codemirror'));
	
	wp_enqueue_script( 'wpcPHPModeJs', plugins_url( 'js/phpmode.js', __FILE__ ), array('codemirror'));
}

function wpc_QueryVar($vars){
    $vars[] = "wpcScript";
	$vars[] = "wpcServerScript";
    return $vars;
}

function wpc_TemplateRedirect($template) {
    global $wp_query, $wpdb;
	$prefix = 'wpctags_';
    if(!isset( $wp_query->query['wpcScript'] ) && !isset( $wp_query->query['wpcServerScript'] )){
        return $template;
	}
	if(isset($wp_query->query['wpcServerScript'])){
		$serverCode = get_post_meta($wp_query->query['wpcServerScript'],'wpctags_tag_servercode',true);
		eval($serverCode);
		exit();
	}
	if(isset($wp_query->query['wpcScript'])){
        $args = array(
			'post_type'=>'wpctag',
			'posts_per_page'=>-1
		);
		$tags = get_posts($args);
		if($tags){
			foreach($tags as $tag){
				$tagName = str_ireplace(" ","",$tag->post_title);
				$tagMeta = get_post_custom($tag->ID);
				if(isset($tagMeta[$prefix.'tag_code'])){
					$tagCode = $tagMeta[$prefix.'tag_code'][0];
					$tagCSS = "";
					$tagScript = "";
					if(isset($tagMeta[$prefix.'tag_css'])){
						$tagCSS = $tagMeta[$prefix.'tag_css'][0];
					}
					if(isset($tagMeta[$prefix.'tag_script'])){
						$serverlink = home_url().'/?wpcServerScript='.$tag->ID;
						$tagScript = str_ireplace('[serverlink]',$serverlink,$tagMeta[$prefix.'tag_script'][0]);
						$tagScript = str_ireplace('[homeurl]',home_url(),$tagScript);
					}
					
					$rtag = '<'.$tagName.'>'."\n".$tagCode;
					if($tagCSS!=''){
						$rtag .= '<style> '.$tagCSS.' </style>';
					}
					if($tagScript!=''){
						$rtag .= '<script> '.$tagScript.' </script>';
					}
					$rtag .= "\n".'</'.$tagName.'>'."\n";
					echo $rtag;
				}
			}
		}
        exit;
    }

    return $template;
}

add_shortcode('wpcEmbedTag','wpcEmbedTag');
function wpcEmbedTag($args,$content=""){
	$tag = $args['tag'];
	$paramList = array();
	foreach($args as $k=>$v){
		if($k=='tag'){
			continue;
		}
		$paramList[] = $k.'="'.$v.'"';
	}
	$paramList = implode(" ",$paramList);
	ob_start();
	echo '<'.$tag.' '.$paramList.'>'.$content.'</'.$tag.'>';
	return ob_get_clean();
}