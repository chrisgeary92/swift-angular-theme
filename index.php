<!DOCTYPE html>
<html <?php language_attributes(); ?> ng-app="app">
<head>

    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <base href="<?= trailingslashit(esc_url(home_url())); ?>">

    <?php wp_head(); ?>

</head>
<body>

    <div id="app">
        <div ng-view></div>
    </div>
    
    <?php wp_footer(); ?>
    
</body>
</html>