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

    <div ng-app="app">

        <header class="site-header">
            <div class="container">
                <h1><a href="<?= esc_url(home_url()); ?>"><?php bloginfo('name'); ?></a></h1>
            </div>
        </header>

        <main class="site-main">
            <div class="container clearfix">
                <div ng-view></div>
            </div>
        </main>

        <footer class="site-footer">
            <div class="container">
                &copy; Copyright <?= date('Y'); ?>
            </div>
        </footer>

    </div>

    <?php wp_footer(); ?>

</body>
</html>