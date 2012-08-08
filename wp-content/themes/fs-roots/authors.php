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

        <?php

			// Get the authors from the database ordered by user nicename
			global $wpdb;
			$query = "SELECT ID, user_nicename from $wpdb->users ORDER BY user_nicename";
			$author_ids = $wpdb->get_results($query);

			// Loop through each author
			foreach($author_ids as $author) :

			// Get user data
			$curauth = get_userdata($author->ID);

			// If user level is above 0 or login name is "admin", display profile
      // All above Contributor (excl.)
			if($curauth->user_level > 1 && !($curauth->user_login == 'admin')) :

			// Get link to author page
			$user_link = get_author_posts_url($curauth->ID);

			// Set default avatar (values = default, wavatar, identicon, monsterid)
			$avatar = 'wavatar';
		?>

		<article class="authors-page">

			<a href="<?php echo $user_link; ?>" title="<?php echo $curauth->display_name; ?>">
				<?php echo get_avatar($curauth->user_email, '96', $avatar); ?>
			</a>

			<header>
        	<h2>
        		<a href="<?php echo $user_link; ?>" title="<?php echo $curauth->display_name; ?>"><?php echo $curauth->display_name; ?></a>
        	</h2>
      		</header>
      		<div class="entry-content">
        		<p>
					<?php echo $curauth->description; ?>
				</p>	
      		</div>
		</article>

		<?php endif; ?>

	<?php endforeach; ?>

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