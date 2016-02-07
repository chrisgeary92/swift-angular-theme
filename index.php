<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <base href="<?= trailingslashit(esc_url(home_url())); ?>">

    <?php wp_head(); ?>

</head>
<body>

    <div ng-app="app" class="container">

        <header class="site-header text-center">
            <h1><a href="<?= esc_url(home_url()); ?>"><?php bloginfo('name'); ?></a></h1>
            <p><?php bloginfo('description'); ?></p>
        </header>

        <main class="site-main clearfix">
            <div ng-view></div>
        </main>

        <footer class="site-footer">
            &copy; Copyright <?= date('Y'); ?>
        </footer>

    </div>

    <?php wp_footer(); ?>

</body>
</html>