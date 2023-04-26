<?php
WLD_Cache::maybe_cache_template_part( 'templates/footer' );

wp_footer();

get_template_part( 'templates/cookie-notice' );

echo '</body></html>';
