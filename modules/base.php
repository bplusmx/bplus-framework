<?php

// Remueve la etiqueta del framework alkivia 0.8
remove_action('wp_head', '_ak_framework_meta_tags');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'shrsb_add_ogtags_head');
