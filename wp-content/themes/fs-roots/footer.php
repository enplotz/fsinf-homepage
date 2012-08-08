<?php $admin_url = admin_url( '', 'https' ); ?>
  </div><!-- /#wrap -->

  <?php roots_footer_before(); ?>
  <footer id="content-info" class="<?php echo WRAP_CLASSES; ?>" role="contentinfo">
    <?php roots_footer_inside(); ?>
    <?php dynamic_sidebar('roots-footer'); ?>
    <p class="copy"><small>&copy; <?php echo date('Y'); ?> Fachschaft Informatik, Universität Konstanz · Raum: E225 · Email: fachschaft (at) inf.uni-konstanz.de · Telefon: 07531-88-3538 · </small><span class="footer-admin"><a href="<?php echo $admin_url; ?>">admin</a></span></p>
  </footer>
  <?php roots_footer_after(); ?>

  <?php wp_footer(); ?>
  <?php roots_footer(); ?>

</body>
</html>