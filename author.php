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
		        
    	        // create the navigation above the content
    	        thematic_navigation_above();
	
    	        /* if display author bio is selected */ 
    	        if($thm_authorinfo == 'true') { 
    	        $pageNumber = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	        ?>
    	        
    	            <div id="author-info" class="vcard">
    	                <h2 class="entry-title"><?php echo $authordata->first_name; ?> <?php echo $authordata->last_name; ?></h2> 
    				
    	                <?php 
    	            
    	                // display the author's avatar
    	                thematic_author_info_avatar();
    	            
    	                ?>
    	            
    	                <div class="author-bio note">
    	                    <?php
    	                
    	                    if ( !(''== $authordata->user_description) ) : echo apply_filters('archive_meta', $authordata->user_description); endif; ?>
    	                Dies ist Seite <?php echo $pageNumber; ?> von allen Artikeln, die <?php echo $authordata->first_name; ?> bei Netzpolitik.org geschrieben hat.    	                
    	                </div>

				</div><!-- #author-info -->
    	        <?php 
    	        }
				
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