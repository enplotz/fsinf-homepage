<?php
/*
Template Name: Authors
*/
get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>


      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'page'); ?>
        <?php roots_loop_after(); ?>

        <!-- Authors Details here -->
        <ul class="unstyled">
<?php

  // Get the authors from the database ordered by user nicename
  global $wpdb;
  $query = "SELECT ID, user_nicename from $wpdb->users ORDER BY user_nicename";
  $author_ids = $wpdb->get_results($query);

  // Loop through each author
  foreach($author_ids as $author) :

    // Get user data
    $curauth = get_userdata($author->ID);
    $cur_capabilities = $curauth->wp_capabilities;

    $is_fs_member = (
      array_key_exists('fs_manager', $cur_capabilities)
      ||
      array_key_exists('administrator', $cur_capabilities)
      ||
      array_key_exists('fs_mitglied', $cur_capabilities)
      ||
      array_key_exists('editor', $cur_capabilities)
      );

    // display only above defined user roles
		if($is_fs_member && !($curauth->user_login == 'admin')) :

		// Get link to author page
		$user_link = get_author_posts_url($curauth->ID);
		// Set default avatar (values = default, wavatar, identicon, monsterid)
		$avatar = 'wavatar';
?>

		<li class="authors-page" id="<?= $curauth->user_login ?>">
			<header>
          <a href="<?php echo $user_link; ?>" title="<?php echo $curauth->display_name; ?>">
            <?php echo get_avatar($curauth->user_email, '96', $avatar); ?>
          </a>
        	<h2>
        		<a href="<?= the_permalink().'#'.slugify($curauth->display_name)?>" title="<?php echo $curauth->display_name; ?>"><?php echo $curauth->display_name; ?></a>
        	</h2>
      		</header>
      		<div class="entry-content">

            <h3>
             <?php echo $curauth->job_in_fs; ?>
            </h3>

            <table class="table table-striped">
              <tbody>

              <!-- Semester -->
              <?php
                // there must be a more elegant way to do this... :(
                $cur_semester = $curauth->semester;
                if (!is_null_or_empty_string($cur_semester)) :
              ?>
                <tr>
                  <th class="table-key span2">Semester:</th>
                  <td><?php echo $cur_semester; ?></td>
                </tr>
              <?php endif;?>

              <!-- Bio -->
              <?php
                $cur_bio = $curauth->description;
                if (!is_null_or_empty_string($cur_bio)) :
              ?>
                <tr>
                  <th class="table-key span2">Bio:</th>
                  <td><?php echo $cur_bio; ?></td>
                </tr>
              <?php endif;?>

              <!-- Website -->
              <?php
                $cur_site = $curauth->user_url;
                if (!is_null_or_empty_string($cur_site)) :
              ?>
                <tr>
                  <th class="table-key span2">Website:</th>
                  <td><a href="<?php echo $cur_site; ?>"><?php echo $cur_site; ?></a></td>
                </tr>
              <?php endif;?>

              <!-- Jabber -->
              <?php
                $cur_jabber = $curauth->jabber;
                if (!is_null_or_empty_string($cur_jabber)) :
              ?>
                <!-- <tr>
                  <th class="table-key span2">Jabber:</th>
                  <td><?php //echo $cur_jabber; ?></td>
                </tr> -->
              <?php endif;?>
              </tbody>
            </table>


<?php
//   if(constant("WP_DEBUG")){
//     if( $all_meta_for_user = get_user_meta( $curauth->ID ) )
//       array_map( function( $a ){ return $a[0]; }, get_user_meta( $curauth->ID ) );

//     print_r( $all_meta_for_user );
//   }
?>
      		</div>
		</li>

		<?php endif; ?>

	<?php endforeach; ?>
</ul>
        <!-- END Authors Details -->

      </div><!-- /#main -->

    <?php roots_main_after(); ?>
    <?php roots_sidebar_before(); ?>
      <aside id="sidebar" class="<?php echo SIDEBAR_CLASSES; ?>" role="complementary">
      <?php roots_sidebar_inside_before(); ?>
        <?php get_sidebar(); ?>
      <?php roots_sidebar_inside_after(); ?>
      </aside><!-- /#sidebar -->
    <?php roots_sidebar_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>