<?php
    // calling the theme options
    global $options, $blog_id;
    foreach ($options as $value) {
        if (get_option( $value['id'] ) === FALSE) {
            $$value['id'] = $value['std'];
        } else {
        	if (THEMATIC_MB)
			{
            	$$value['id'] = get_blog_option($blog_id, $value['id'] );
			}
			else
			{
            	$$value['id'] = get_option( $value['id'] );
  			}
        }
    }

    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

?>

		<div id="container">

			<?php thematic_abovecontent(); ?>

			<div id="content">

    	        <?php

    	        the_post();

    	        // do not displays the page title
    	        // thematic_page_title();

    	        // create the navigation above the content
    	        thematic_navigation_above();

?>

    	            <div id="author-info" class="vcard">
    	                <h1 class="page-title author">Alle Artikel von <span id="np_authorname"><?php echo $authordata->first_name; ?> <?php echo $authordata->last_name; ?></span> bei <?php bloginfo('name') ?></h1>

    	                <?php

    	                // display the author's avatar
    	                // thematic_author_info_avatar();
    	            	// using the author Image plugin instead
    	            	// http://downloads.wordpress.org/plugin/sem-author-image.zip
    	            	the_author_image();

    	                ?>

    	                <div class="author-bio note">
    	                    <?php
    	                    if ( !(''== $authordata->user_description) ) : echo apply_filters('archive_meta', $authordata->user_description); endif; ?>
    	                </div>
    				<!-- Wir nutzen diese Emailadressen nicht.
    				<span id="author-email">
    	                <a class="email" title="<?php echo antispambot($authordata->user_email); ?>" href="mailto:<?php echo antispambot($authordata->user_email); ?>"><?php echo "Email an "; ?><span class="fn n"><span class="given-name"><?php echo $authordata->first_name; ?></span> <span class="family-name"><?php echo $authordata->last_name; ?></span></span></a>
    	            </span><br />
    	            -->
    	            <span id="author-url">
    	                <a href="<?php the_author_url(); ?>" rel="author"><?php the_author_url(); ?></a>
    	            </span>
				</div><!-- #author-info -->
    	        <?php

    	        // action hook creating the author loop
    	        thematic_authorloop();

    	        // create the navigation below the content
				thematic_navigation_below(); ?>

			</div><!-- #content -->

			<?php thematic_belowcontent(); ?>

		</div><!-- #container -->

<?php

    // action hook for placing content below #container
    thematic_belowcontainer();

    // calling the standard sidebar
    thematic_sidebar();

    // calling footer.php
    get_footer();

?>
