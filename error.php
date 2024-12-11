<?php

/**
 * 404 Entry Error
 *
 * Used for the 404 entry errors.
 */

?>

<article id="entry-not-found" class="entry-error">

    <?php if( !is_search() ) :?>

        <header class="entry-header" style="margin-top: 3rem">

            <h1 class="entry-title"><?php esc_html_e( 'Error 404', 'talkcondo' ); ?></h1>
            
        </header>

    <?php endif; ?>

    <div class="clearfix">

        <?php AE_Structure::error( array( 'page', 'post', 'project' ) ); ?>

    </div>

</article>
