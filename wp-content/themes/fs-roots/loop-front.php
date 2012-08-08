<?php /* Start loop */ ?>
<?php while (have_posts()) : the_post(); ?>
  <?php roots_post_before(); ?>
  <article class="news-post">
    <?php roots_post_inside_before(); ?>
      <header>
    	<div class="page-header">
      		<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
      		<div class="post-meta pull-right">
      		<?php roots_entry_meta(); ?>
      	</div>
      	</div>
      </header>
      <?php the_content(); ?>
      <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
    <?php roots_post_inside_after(); ?>
   </article>
  <?php roots_post_after(); ?>
<?php endwhile; /* End loop */ ?>