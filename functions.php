<?php

// ENTFERNEN UNGEWOLLTER FUNKTIONEN
function remove_thematic_actions() {
	remove_action('thematic_header','thematic_access',9);
	remove_action('thematic_header','thematic_blogdescription',5);
	remove_action('thematic_header','thematic_blogtitle',3); // There seems to be an overriding error in Themativ for _blogtitle
	remove_action('thematic_navigation_above', 'thematic_nav_above', 2);
}
add_action('init','remove_thematic_actions');

//	Favorite Icon
function childtheme_favicon() { ?>
    <link rel="shortcut icon" href="<?php echo bloginfo('stylesheet_directory') ?>/img/favicon.ico" />
    <!-- Wir nutzen vorrübergehend die Google Font-API und hosten selbst, sobald wir eine Schrift endgültig gewählt haben -->
    <link href='http://fonts.googleapis.com/css?family=PT+Serif+Caption' rel='stylesheet' type='text/css'>
<?php }
add_action('wp_head', 'childtheme_favicon');

//	Custom IE Style Sheet
function childtheme_iefix() { ?>
    <!--[if lte IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('stylesheet_directory') ?>/ie.css" />
    <![endif]-->
<?php }
add_action('wp_head', 'childtheme_iefix');

// Show comment count in post-meta after title
function childtheme_override_postheader_postmeta() {
	$postmeta = '<div class="entry-meta">';
	$postmeta .= thematic_postmeta_authorlink();
	$postmeta .= '<span class="meta-sep meta-sep-entry-date"> | </span>';
	$postmeta .= thematic_postmeta_entrydate() . " | ";
	$postmeta .= thematic_postfooter_postcomments();
	$postmeta .= "</div><!-- .entry-meta -->\n";
	return apply_filters('thematic_postheader_postmeta',$postmeta); 
}


function childtheme_override_blogtitle() { ?>
	<div id="blog-title"><a href="<?php echo bloginfo('url') ?>/" title="<?php echo bloginfo('name') ?>" rel="home"><img src="<?php echo bloginfo('stylesheet_directory') ?>/img/logo.png" alt="<?php echo bloginfo('name') ?>"></a></span></div>
<?php }

add_action('thematic_header','childtheme_override_blogtitle',3); // There seems to be an overriding error in Themativ for _blogtitle

// Page Title improvements

function childtheme_override_page_title() {
	global $post;
	
	$content = '';
	if (is_attachment()) {
			$content .= '<h2 class="page-title"><a href="';
			$content .= apply_filters('the_permalink',get_permalink($post->post_parent));
			$content .= '" rev="attachment"><span class="meta-nav">&laquo; </span>';
			$content .= get_the_title($post->post_parent);
			$content .= '</a></h2>';
	} elseif (is_author()) {
			$content .= '<h1 class="page-title author">';
			$author = get_the_author_meta( 'display_name' );
			$content .= __('Author Archives: ', 'thematic');
			$content .= '<span id="np_authorname">';
			$content .= $author;
			$content .= '</span></h1>';
	} elseif (is_category()) {
			$content .= '<h1 class="page-title">';
			$content .= __('Category Archives:', 'thematic');
			$content .= ' <span id="np_category-title">';
			$content .= single_cat_title('', FALSE);
			$content .= '</span></h1>' . "\n";
			$content .= '<div class="archive-meta">';
			if ( !(''== category_description()) ) : $content .= apply_filters('archive_meta', category_description()); endif;
			$content .= '</div>';
	} elseif (is_search()) {
			$content .= '<h1 class="page-title">';
			$content .= __('Search Results for:', 'thematic');
			$content .= ' <span id="search-terms">';
			$content .= esc_html(stripslashes($_GET['s']));
			$content .= '</span></h1>';
	} elseif (is_tag()) {
			$content .= '<h1 class="page-title">';
			$content .= __('Tag Archives:', 'thematic');
			$content .= ' <span id="np_tag-title">';
			$content .= __(thematic_tag_query());
			$content .= '</span></h1>';
	} elseif (is_tax()) {
		    global $taxonomy;
			$content .= '<h1 class="page-title">';
			$tax = get_taxonomy($taxonomy);
			$content .= $tax->labels->name . ' ';
			$content .= __('Archives:', 'thematic');
			$content .= ' <span id="np_term-title">';
			$content .= thematic_get_term_name();
			$content .= '</span></h1>';
	}	elseif (is_day()) {
			$content .= '<h1 class="page-title">';
			$content .= sprintf(__('Daily Archives: <span>%s</span>', 'thematic'), get_the_time(get_option('date_format')));
			$content .= '</h1>';
	} elseif (is_month()) {
			$content .= '<h1 class="page-title">';
			$content .= sprintf(__('Monthly Archives: <span>%s</span>', 'thematic'), get_the_time('F Y'));
			$content .= '</h1>';
	} elseif (is_year()) {
			$content .= '<h1 class="page-title">';
			$content .= sprintf(__('Yearly Archives: <span>%s</span>', 'thematic'), get_the_time('Y'));
			$content .= '</h1>';
	} elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
			$content .= '<h1 class="page-title">';
			$content .= __('Blog Archives', 'thematic');
			$content .= '</h1>';
	}
	$content .= "\n";
	echo apply_filters('thematic_page_title', $content);
}

// We want "more" in excerpts (i.e.: Archives, search)

function childtheme_override_content() {
	global $thematic_content_length;

	if ( strtolower($thematic_content_length) == 'full' ) {
		$post = get_the_content(more_text());
		$post = apply_filters('the_content', $post);
		$post = str_replace(']]>', ']]&gt;', $post);
	} elseif ( strtolower($thematic_content_length) == 'excerpt') {
		$post = '';
		$post .= get_the_excerpt() . ' <br /><a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">" . more_text(). "</a>";
		$post = apply_filters('the_excerpt',$post);
		if ( apply_filters( 'thematic_post_thumbs', TRUE) ) {
			$post_title = get_the_title();
			$size = apply_filters( 'thematic_post_thumb_size' , array(100,100) );
			$attr = apply_filters( 'thematic_post_thumb_attr', array('title'	=> 'Permalink to ' . $post_title) );
			if ( has_post_thumbnail() ) {
				$post = '<a class="entry-thumb" href="' . get_permalink() . '" title="Permalink to ' . get_the_title() . '" >' . get_the_post_thumbnail(get_the_ID(), $size, $attr) . '</a>' . $post;
				}
		}
	} elseif ( strtolower($thematic_content_length) == 'none') {
	} else {
		$post = get_the_content(more_text());
		$post = apply_filters('the_content', $post);
		$post = str_replace(']]>', ']]&gt;', $post);
	}
	echo apply_filters('thematic_post', $post);
}

// Custom, hard-coded Top Navigation
function childtheme_Netzpolnavi() { ?>
	<div id="top">
		<span id="np_navi">
			<a href="/" class="np_navpunkt">Home</a>
			<a href="/about-this-blog/" class="np_navpunkt">Über uns</a>
			<a href="/impressum/" class="np_navpunkt">Kontakt</a>
			<a href="/category/netzpolitik-podcast/" class="np_navpunkt">Podcast</a>
			<a href="/category/netzpolitiktv/" class="np_navpunkt">Netzpolitik TV</a>
			<a href="https://www.facebook.com/netzpolitik" class="np_navpunkt">Facebook</a>
			<a href="https://www.youtube.com/user/netzpolitik" class="np_navpunkt">Youtube</a>
			<a href="https://www.twitter.com/Netzpolitik" class="np_navpunkt">Twitter</a>
			<!-- <a href="/wiki/" class="np_navpunkt">Wiki</a> -->
			<a href="/feed/" class="np_navpunkt">RSS</a>
		</span>
		<div id="zeitlogo"></div>

	</div>
<?php }

add_action('thematic_header','childtheme_Netzpolnavi', 5); 

// ADDITIONS FOR ADs

// var ord is needed for ads later
function childtheme_ads_headerinitializer() { ?>
	<script type="text/javascript">
		var ord = Math.random() * 10000000000000000;
	</script>
<?php }

// so page can be pushed away by skyscraper
function childtheme_ads_pagewrapper_open() { ?>
<div id="pagewrapper">
<?php }
function childtheme_ads_pagewrapper_close() { ?>
	</div><!-- pagewrapper -->
<?php }

// skyscraper ad
function childtheme_ads_skyscraper120x600() { ?>
	<div class="ad" id="skyscraper">
	<script type="text/javascript"> 
	// <![CDATA[ 		
	document.write('<script src="http://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
	// ]]>
	</script>
	<noscript><a href="http://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="http://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=123456789?" width="120" height="600" style="border:none;" alt="" /></a>
	</noscript>
	</div><!-- ad / skyscraper -->
<?php }

// superbanner ad
function childtheme_ads_superbanner728x90() { ?>
	<div class="ad" id="superbanner">
	<script type="text/javascript"> 
	// <![CDATA[ 		
	document.write('<script src="http://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;dcopt=ist;tile=1;sz=728x90;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
	// ]]>
	</script>
	<noscript><a href="http://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=1;sz=728x90;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="http://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=1;sz=728x90;kw=netzpolitik;ord=123456789?" width="728" height="90" style="border:none;" alt="" /></a>
	</noscript>
	</div><!-- ad / superbanner -->
<?php }

// medium rectangle ad
function childtheme_ads_mediumrectangle300x250() { ?>
	<div class="ad" id="mediumrectangle">Anzeige<br />
	<script type="text/javascript"> 
	// <![CDATA[ 		
	document.write('<script src="http://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;tile=8;sz=300x250;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
	// ]]>
	</script>
	<noscript><a href="http://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=8;sz=300x250;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="http://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=8;sz=300x250;kw=netzpolitik;ord=123456789?" width="300" height="250" style="border:none;" alt="" /></a>
	</noscript>
	</div><!-- ad / mediumrectangle -->
<?php }

function childtheme_ads_ivwpixel() { ?><!--SZM VERSION="1.5"-->
	<!--Dieses Online-Angebot unterliegt nicht der IVW-Kontrolle!-->
	<script language="javascript" type="text/javascript">
	<!--
	var IVW="http://zeitonl.ivwbox.de/cgi-bin/ivw/CP/netzpolitik";
	document.write('<img src="' + IVW + '?r=' + escape(document.referrer) + '&d=' + (Math.random()*100000) + '" width="1" height="1" border="0" alt="szmtag"/>');
	//-->
	</script>
	<noscript>
	<img src="http://zeitonl.ivwbox.de/cgi-bin/ivw/CP/netzpolitik" width="1" height="1" border="0" alt="szmtag" />
	</noscript>
	<!--SZMFRABO VERSION="1.2"-->
<?php }
add_action('thematic_header','childtheme_ads_ivwpixel', 1); 

function childtheme_ads_SuBa() { ?>
	<div id="iqdTop" >
		<div class="center">
			<div class="ad" id="p1">
				<div id="place_1">
	 
				<script type="text/javascript"> 
		// <![CDATA[ 		
		document.write('<script src="http://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;dcopt=ist;tile=1;sz=728x90;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
		// ]]>
		</script>
		<noscript><a href="http://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=1;sz=728x90;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="http://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=1;sz=728x90;kw=netzpolitik;ord=123456789?" width="728" height="90" style="border:none;" alt="" /></a>
		</noscript>
		
				</div>
				<div id="p1_right">
					<div id="place_2">
	 
		<script type="text/javascript"> 
		// <![CDATA[ 		
		document.write('<script src="http://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
		// ]]>
		</script>
		<noscript><a href="http://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="http://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=123456789?" width="120" height="600" style="border:none;" alt="" /></a>
		</noscript>
	 
					</div> 
				</div> 
			</div>
		</div> 
	</div>
<?php }

function childtheme_cc_license() {?>
	
<?php }

function childtheme_blogdescription() {
	$blogdesc = '"blog-description">';
	// $blogdesc .= get_bloginfo('description');
	$akno = 'Wir nutzen <a href="http://www.wordpress.org">Wordpress</a>. Das Design ist ein <a href="http://themeshaper.com/thematic/">Thematic</a>-Kind von <a href="http://www.Linus-Neumann.de">Linus Neumann</a>.';
	$cc = 'Die von uns verfassten Inhalte stehen unter der Lizenz <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/de/">CC BY-NC-SA</a>.';
	echo "\t\t<div id=$blogdesc $cc<br/>$akno</div>\n\n";
}

add_action('thematic_aboveheader','childtheme_ads_pagewrapper_open',3);
add_action('thematic_belowfooter','childtheme_ads_pagewrapper_close',1);
add_action('thematic_belowfooter','childtheme_blogdescription',2);
add_action('thematic_before','childtheme_ads_SuBa',2);
add_action('wp_head','childtheme_ads_headerinitializer');
add_action('thematic_betweenmainasides','childtheme_ads_mediumrectangle300x250');


?>