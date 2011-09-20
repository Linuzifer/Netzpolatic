<?php

// Stuff we don't want
function remove_thematic_actions() {
	remove_action('thematic_header','thematic_access',9);
	remove_action('thematic_header','thematic_blogdescription',5);
	remove_action('thematic_header','thematic_blogtitle',3); // There seems to be an overriding error in Themativ for _blogtitle
	remove_action('thematic_navigation_above', 'thematic_nav_above', 2);
}
add_action('init','remove_thematic_actions');

//	FavIcon
function childtheme_favicon() { ?>
    <link rel="shortcut icon" href="/wp-content/themes/Netzpolatic/img/favicon.ico" />
    <!-- Wir nutzen vorrübergehend die Google Font-API und hosten selbst, sobald wir eine Schrift endgültig gewählt haben -->
    <link href='https://fonts.googleapis.com/css?family=PT+Serif+Caption' rel='stylesheet' type='text/css' />
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

// Logos for Facebook and GooglePlus
function childtheme_facebook_meta() { ?>
    <!-- Hey Facebook and Google plus! This is our logo: -->
     <link rel="image_src" href="https://www.netzpolitik.org/wp-content/themes/Netzpolatic/img/SNlogo.png" />
<?php } 
add_action('wp_head','childtheme_facebook_meta');

// Change BlogTitle Display
function childtheme_override_blogtitle() { ?>
	<div id="blog-title"><a href="<?php echo bloginfo('url') ?>/" title="<?php echo bloginfo('name') ?>" rel="home"><img src="<?php echo bloginfo('stylesheet_directory') ?>/img/logo-left.png" alt="<?php echo bloginfo('name') ?>" width="418" height="49"></a></div>
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
			$content .= '<div class="archiv-infobox">';
			$content .= '<h1 class="page-title">';
			$content .= __('Category Archives:', 'thematic');
			$content .= ' <span id="np_category-title">';
			$content .= single_cat_title('', FALSE);
			$content .= '</span></h1>' . "\n";
			$content .= '<div class="archive-meta">';
			if ( !(''== category_description()) ) : $content .= apply_filters('archive_meta', category_description()); endif;
			$content .= '</div>';
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
		<ul id="np_navi">
			<li><a href="/" class="np_navpunkt">Home</a></li>
			<li><a href="/about-this-blog/" class="np_navpunkt">Über uns</a></li>
			<li><a href="/impressum/" class="np_navpunkt">Kontakt</a></li>
			<li><a href="/category/netzpolitik-podcast/" class="np_navpunkt">Podcast</a></li>
			<li><a href="/category/netzpolitiktv/" class="np_navpunkt">Netzpolitik TV</a></li>
			<li><a href="https://www.facebook.com/netzpolitik" class="np_navpunkt">Facebook</a></li>
			<li><a href="https://www.youtube.com/user/netzpolitik" class="np_navpunkt">Youtube</a></li>
			<li><a href="https://www.twitter.com/Netzpolitik" class="np_navpunkt">Twitter</a></li>
			<li><a href="/feed/" class="np_navpunkt">RSS</a></li>
		</ul>
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
	document.write('<script src="https://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
	// ]]>
	</script>
	<noscript><a href="https://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="https://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=123456789?" width="120" height="600" style="border:none;" alt="" /></a>
	</noscript>
	</div><!-- ad / skyscraper -->
<?php }

// superbanner ad
function childtheme_ads_superbanner728x90() { ?>
	<div class="ad" id="superbanner">
	<script type="text/javascript"> 
	// <![CDATA[ 		
	document.write('<script src="https://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;dcopt=ist;tile=1;sz=728x90;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
	// ]]>
	</script>
	<noscript><a href="https://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=1;sz=728x90;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="https://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=1;sz=728x90;kw=netzpolitik;ord=123456789?" width="728" height="90" style="border:none;" alt="" /></a>
	</noscript>
	</div><!-- ad / superbanner -->
<?php }

// medium rectangle ad
function childtheme_ads_mediumrectangle300x250() { ?>
	<div class="ad" id="mediumrectangle">Anzeige<br />
	<script type="text/javascript"> 
	// <![CDATA[ 		
	document.write('<script src="https://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;tile=8;sz=300x250;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
	// ]]>
	</script>
	<noscript><a href="https://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=8;sz=300x250;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="https://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=8;sz=300x250;kw=netzpolitik;ord=123456789?" width="300" height="250" style="border:none;" alt="" /></a>
	</noscript>
	</div><!-- ad / mediumrectangle -->
<?php }

// Switch IVW-Pixel-id for certain cases
function childtheme_ads_ivwpixel() {
switch(get_the_ID())
	{
    case '751';
		$ivwurl="https://zeitonl.ivwbox.de/cgi-bin/ivw/CP/netzpolitik/ueber";
		break;
    case '752';
		$ivwurl="https://zeitonl.ivwbox.de/cgi-bin/ivw/CP/netzpolitik/impressum";
		break;
    case '7852';
        $ivwurl="https://zeitonl.ivwbox.de/cgi-bin/ivw/CP/netzpolitik/werbung";
    	break;
    default;
        $ivwurl="https://zeitonl.ivwbox.de/cgi-bin/ivw/CP/netzpolitik";
    break;
}
?><!-- SZM VERSION="1.5" -->
	<!-- Dieses Online-Angebot unterliegt nicht der IVW-Kontrolle! -->
	<script language="javascript" type="text/javascript">
		<!--
		var IVW="<?php echo $ivwurl;?>";
		document.write('<img src="' + IVW + '?r=' + escape(document.referrer) + '&d=' + (Math.random()*100000) + '" width="1" height="1" border="0" alt="szmtag"/>');
		//-->
	</script>
	<noscript>
		<img src="https://zeitonl.ivwbox.de/cgi-bin/ivw/CP/netzpolitik" width="1" height="1" border="0" alt="szmtag" />
	</noscript>
	<!-- SZMFRABO VERSION="1.2" -->
	<script type="text/javascript"> <!-- var szmvars="zeitonl//CP//netzpolitik"; // --> </script>
 	<script src="https://zeitonl.ivwbox.de/2004/01/survey.js" type="text/javascript"></script>
	<!--/SZMFRABO-->
<?php }
add_action('thematic_header','childtheme_ads_ivwpixel', 1); 

// Superbanner
function childtheme_ads_SuBa() { ?>
	<div id="iqdTop" >
		<div class="center">
			<div class="ad" id="p1">
				<div id="place_1">
					<script type="text/javascript"> 
						// <![CDATA[ 		
						document.write('<script src="https://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;dcopt=ist;tile=1;sz=728x90;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
					// ]]>
					</script>
					<noscript><a href="https://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=1;sz=728x90;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="https://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=1;sz=728x90;kw=netzpolitik;ord=123456789?" width="728" height="90" style="border:none;" alt="" /></a>
					</noscript>
		
				</div>
				<div id="p1_right">
					<div id="place_2">
						<script type="text/javascript"> 
							// <![CDATA[ 		
							document.write('<script src="https://ad.de.doubleclick.net/adj/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=' + ord + '?" type="text/javascript"><\/script>');
							// ]]>
						</script>
						<noscript><a href="https://ad.de.doubleclick.net/jump/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=123456789?" rel="nofollow"><img src="https://ad.de.doubleclick.net/ad/netzpolitik.org/rotation;tile=2;sz=120x600;kw=netzpolitik;ord=123456789?" width="120" height="600" style="border:none;" alt="" /></a>
						</noscript>
					</div> 
				</div> 
			</div>
		</div> 
	</div>
	<script type="text/javascript">
		place_1 = document.getElementById("place_1");
		img = place_1.childNodes[3].firstChild;
		if((img.height + img.width) != 2) {
			// proper banner detected
			document.getElementById("wrapper").style.marginTop = '90px';
		} else {
			// no banner detected
		}
	</script>
<?php }

function childtheme_cc_license() {?>
	
<?php }

function childtheme_blogdescription() {
	$blogdesc = '"blog-description">';
	// $blogdesc .= get_bloginfo('description');
	$akno = 'Netzpolitik.org nutzt <a href="https://www.wordpress.org">Wordpress</a>. Das Design ist ein <a href="http://themeshaper.com/thematic/">Thematic</a>-Kind von <a href="http://www.Linus-Neumann.de">Linus Neumann</a>.';
	$cc = 'Die von uns verfassten Inhalte stehen unter der Lizenz <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/de/">CC BY-NC-SA</a>.';
	echo "\t\t<div id=$blogdesc $cc<br/>$akno</div>\n\n";
}

add_action('thematic_aboveheader','childtheme_ads_pagewrapper_open',3);
add_action('thematic_belowfooter','childtheme_ads_SuBa',1);
add_action('thematic_belowfooter','childtheme_ads_pagewrapper_close',2);
add_action('thematic_belowfooter','childtheme_blogdescription',3);
add_action('wp_head','childtheme_ads_headerinitializer');
add_action('thematic_betweenmainasides','childtheme_ads_mediumrectangle300x250');

// SEARCH ENGINE OPTIMIZATION

/* Random Posts on 404's */
function childtheme_override_404_content() { ?>
   			<?php thematic_postheader(); ?>
   			
				<div class="entry-content">
					<p><?php _e('Apologies, but we were unable to find what you were looking for. Perhaps  searching will help.', 'thematic') ?></p>
				</div><!-- .entry-content -->
				
				<form id="error404-searchform" method="get" action="<?php bloginfo('url') ?>/">
					<div>
						<input id="error404-s" name="s" type="text" value="<?php echo esc_html(stripslashes(get_query_var('s'))) ?>" size="40" />
						<input id="error404-searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Find', 'thematic') ?>" />
					</div>
				</form>
<?php } 

/* Schema.org */
/* Schema.org author on single posts */
function childtheme_override_postmeta_authorlink() {
	    global $authordata;
	
	    $authorlink = '<span class="meta-prep meta-prep-author">' . __('By ', 'thematic') . '</span>';
	    if (is_single()) {
	    $authorlink .= '<span class="author vcard" itemprop="author">'. '<a rel="author" class="url fn n" href="';
	    } else {
	    $authorlink .= '<span class="author vcard">'. '<a class="url fn n" href="';
	    }
	    $authorlink .= get_author_posts_url($authordata->ID, $authordata->user_nicename);
	    $authorlink .= '" title="' . __('View all posts by ', 'thematic') . get_the_author_meta( 'display_name' ) . '">';
	    $authorlink .= get_the_author_meta( 'display_name' );
	    $authorlink .= '</a></span>';
	    
	    return apply_filters('thematic_post_meta_authorlink', $authorlink);

}

/* Schema.org headline on single posts */
function childtheme_override_postheader_posttitle() {
	    if (is_single() || is_page()) {
	        $posttitle = '<h1 itemprop="headline" class="entry-title">' . get_the_title() . "</h1>\n";
	    } elseif (is_404()) {    
	        $posttitle = '<h1 class="entry-title">' . __('Not Found', 'thematic') . "</h1>\n";
	    } else {
	        $posttitle = '<h2 class="entry-title"><a href="';
	        $posttitle .= apply_filters('the_permalink', get_permalink());
	        $posttitle .= '" title="';
	        $posttitle .= __('Permalink to ', 'thematic') . the_title_attribute('echo=0');
	        $posttitle .= '" rel="bookmark">';
	        $posttitle .= get_the_title();   
	        $posttitle .= "</a></h2>\n";
	    }
	    
	    return apply_filters('thematic_postheader_posttitle',$posttitle); 
	
}

/* Schema.org datePublished on single posts */
function childtheme_override_postmeta_entrydate() {
	    $entrydate = '<span class="meta-prep meta-prep-entry-date">' . __('Published: ', 'thematic') . '</span>';
	    if (is_single()) {
	    $entrydate .= '<span class="entry-date"><time itemprop="datePublished" datetime="' . get_the_time('Y-m-d') . 'T' . get_the_time('G:i') . '">' . get_the_time('d.m.Y') . ' um ' . get_the_time('G:i') . 'h</time></span>';
	    } else {
	    $entrydate .= '<span class="entry-date">' . get_the_time('d.m.Y') . ' um ' . get_the_time('G:i') . 'h' . '</span>';
	    }
	    
	    return apply_filters('thematic_post_meta_entrydate', $entrydate);

}

/* Schema.org articleBody on single posts */
function childtheme_override_single_post() {
				thematic_abovepost(); ?>
			
				<div id="post-<?php the_ID();
					echo '" ';
					if (!(THEMATIC_COMPATIBLE_POST_CLASS)) {
						post_class();
						echo '>';
					} else {
						echo 'class="';
						thematic_post_class();
						echo '">';
					}
     				thematic_postheader(); ?>
					<div class="entry-content" itemprop="articleBody">
<?php thematic_content(); ?>

						<?php wp_link_pages('before=<div class="page-link">' .__('Pages:', 'thematic') . '&after=</div>') ?>
					</div><!-- .entry-content -->
					<?php thematic_postfooter(); ?>
				</div><!-- #post -->
		<?php

			thematic_belowpost();

}

/* Schema.org articleSection (Category) on single posts */
function childtheme_override_postfooter_postcategory() {
	    $postcategory = '<span class="cat-links">';
	    if (is_single()) {
	        $postcategory .= __('This entry was posted in ', 'thematic') . '<span itemprop="articleSection">' . get_the_category_list('</span>, <span itemprop="articleSection">');
	        $postcategory .= '</span></span>';
	    } elseif ( is_category() && $cats_meow = thematic_cats_meow(', ') ) { /* Returns categories other than the one queried */
	        $postcategory .= __('Also posted in ', 'thematic') . $cats_meow;
	        $postcategory .= '</span> <span class="meta-sep meta-sep-tag-links">|</span>';
	    } else {
	        $postcategory .= __('Posted in ', 'thematic') . get_the_category_list(', ');
	        $postcategory .= '</span> <span class="meta-sep meta-sep-tag-links">|</span>';
	    }
	    return apply_filters('thematic_postfooter_postcategory',$postcategory); 
}

/* Schema.org Keywords (Tags) on single posts */
function childtheme_override_postfooter_posttags() {

	    if (is_single()) {
	        $tagtext = __(' and tagged', 'thematic');
	        $posttags = get_the_tag_list("<span class=\"tag-links\"> $tagtext <span itemprop=\"keywords\">",', ','</span></span>');
	    } elseif ( is_tag() && $tag_ur_it = thematic_tag_ur_it(', ') ) { /* Returns tags other than the one queried */
	        $posttags = '<span class="tag-links">' . __(' Also tagged ', 'thematic') . $tag_ur_it . '</span> <span class="meta-sep meta-sep-comments-link">|</span>';
	    } else {
	        $tagtext = __('Tagged', 'thematic');
	        $posttags = get_the_tag_list("<span class=\"tag-links\"> $tagtext ",', ','</span> <span class="meta-sep meta-sep-comments-link">|</span>');
	    }
	    return apply_filters('thematic_postfooter_posttags',$posttags); 
	
}

/* Schema.org publisher on single posts */
function childtheme_override_postfooter_postconnect() {
	    $postconnect = __('. Bookmark the ', 'thematic') . '<a href="' . apply_filters('the_permalink', get_permalink()) . '" title="' . __('Permalink to ', 'thematic') . the_title_attribute('echo=0') . '">';
	    $postconnect .= __('permalink', 'thematic') . '</a>.';
	    if ((comments_open()) && (pings_open())) { /* Comments are open */
	        $postconnect .= ' <a class="comment-link" href="#respond" title ="' . __('Post a comment', 'thematic') . '">' . __('Post a comment', 'thematic') . '</a>';
	        $postconnect .= __(' or leave a trackback: ', 'thematic');
	        $postconnect .= '<a class="trackback-link" href="' . get_trackback_url() . '" title ="' . __('Trackback URL for your post', 'thematic') . '" rel="trackback">' . __('Trackback URL', 'thematic') . '</a>.';
	    } elseif (!(comments_open()) && (pings_open())) { /* Only trackbacks are open */
	        $postconnect .= __(' Comments are closed, but you can leave a trackback: ', 'thematic');
	        $postconnect .= '<a class="trackback-link" href="' . get_trackback_url() . '" title ="' . __('Trackback URL for your post', 'thematic') . '" rel="trackback">' . __('Trackback URL', 'thematic') . '</a>.';
	    } elseif ((comments_open()) && !(pings_open())) { /* Only comments open */
	        $postconnect .= __(' Trackbacks are closed, but you can ', 'thematic');
	        $postconnect .= '<a class="comment-link" href="#respond" title ="' . __('Post a comment', 'thematic') . '">' . __('post a comment', 'thematic') . '</a>.';
	    } elseif (!(comments_open()) && !(pings_open())) { /* Comments and trackbacks closed */
	        $postconnect .= __(' Both comments and trackbacks are currently closed.', 'thematic');
	    }
	    if (is_single()) {
	    $postconnect .= ' Dieser Beitrag steht unter der Lizenz <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/de/">CC BY-NC-SA</a>: ' . get_the_author_meta( 'display_name' ) . ', <span itemprop="publisher">Netzpolitik.org</span>.';
		}
	    // Display edit link on single posts
	    if (current_user_can('edit_posts')) {
	        $postconnect .= ' ' . thematic_postfooter_posteditlink();
	    }
	    return apply_filters('thematic_postfooter_postconnect',$postconnect); 

}

/* We can do better canonical links */
remove_action('wp_head', 'rel_canonical');
function childtheme_rel_canonical() {
	if ( !is_singular() )
		return;

	global $wp_the_query;
	if ( !$id = $wp_the_query->get_queried_object_id() )
		return;

	$link = get_permalink( $id );
	// modify Link if we are using SSL
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
		$link = str_replace("https://", "http://", $link);
	}
	echo "<link rel='canonical' href='$link' />\n";
}
add_action( 'wp_head','childtheme_rel_canonical',1); // remember to turn off canonical links in wpSEO and stuff like that

// SOCIAL- AND VIRALIZING PLUGINS
// We rely on:
// http://www.jeremyarntz.com/development/plugins/wordpress/google-plus-one/
// http://wordpress.org/extend/plugins/flattr/
// http://0xtc.com/plugins/wp-tweet-button
// http://www.ethitter.com/plugins/simple-facebook-share-button/

function buttons() { ?>
	<div class="fuckingbuttons">
	<div class="facebooklikebutton"><a href="http://www.facebook.com/sharer.php?u=<?php the_permalink() ?>&amp;t=<?php the_title(); ?>" target="_blank"><img src="<?php echo bloginfo('stylesheet_directory') ?>/img/like.png"></a></div>
	<?
	if (function_exists('the_flattr_permalink')) { ?> <div class="flattrbutton"><? the_flattr_permalink(); ?></div><? }
	if (function_exists('tweetbutton')) { echo tweetbutton(); }
	if (function_exists('google_plus_one')) { google_plus_one(); }
	?></div><?
}

function childtheme_override_postfooter() {
    
    global $id, $post;
    
    if ($post->post_type == 'page' && current_user_can('edit_posts')) { /* For logged-in "page" search results */
        $postfooter = '<div class="entry-utility">' . buttons() . thematic_postfooter_posteditlink();
        $postfooter .= "</div><!-- .entry-utility -->\n";    
    } elseif ($post->post_type == 'page') { /* For logged-out "page" search results */
        $postfooter = buttons();
    } else {
        if (is_single()) {
            $postfooter = '<div class="entry-utility">' . buttons() . thematic_postfooter_postcategory() . thematic_postfooter_posttags() . thematic_postfooter_postconnect();
        } else {
            $postfooter = '<div class="entry-utility">' . buttons() . thematic_postfooter_postcategory() . thematic_postfooter_posttags() . thematic_postfooter_postcomments();
        }
        $postfooter .= "</div><!-- .entry-utility -->\n";    
    }
    
    // Put it on the screen
    echo apply_filters( 'thematic_postfooter', $postfooter ); // Filter to override default post footer
}

?>
