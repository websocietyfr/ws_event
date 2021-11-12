<?php
/**
 * The template for displaying ws_events single post
 */

get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<?php
			// Start the Loop.
			while ( have_posts() ) :
				the_post();
				?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1><?php the_title(); ?></h1>
                    <div class="event-details">
                        <p>
                            <?php _e( 'Date de l\'événement : ', 'ws_event_content' ); ?><br>
                            <?php _e('Du ', 'ws_event_content'); ?><?php echo get_post_meta( get_the_ID(), "start_date", true ); ?><?php _e(' au ', 'ws_event_content'); ?><?php echo get_post_meta( get_the_ID(), "end_date", true ); ?>
                        </p>
                        <p>
                            <?php _e( 'Lieu de l\'événemnt : ', 'ws_event_content' ); ?><br>
                            <?php echo get_post_meta( get_the_ID(), "address", true ); ?>
                        </p>
                        <?php if(get_post_meta( get_the_ID(), "linkRegistry", true )): ?>
                        <p>
                            <?php _e( 'Lien vers la page d\'inscription pour les partenaires : ', 'ws_event_content' ); ?><br>
                            <a href="<?php echo get_post_meta( get_the_ID(), "linkRegistry", true ); ?>">Page d'inscription</a>
                        </p>
                        <?php endif; ?>
                        <?php if(get_post_meta( get_the_ID(), "linkDetailDoc", true )): ?>
                        <p>
                            <?php _e( 'Fiche des informations pratique pour les participants : ', 'ws_event_content' ); ?><br>
                            <a href="<?php echo get_post_meta( get_the_ID(), "linkDetailDoc", true ); ?>">Fiche pratique</a>
                        </p>
                        <?php endif; ?>
                    </div><!-- .event-details -->
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div><!-- .entry-content -->

                    <?php if ( (bool) get_the_author_meta( 'description' ) ) : ?>
                    <div class="author-bio">
                        <h2 class="author-title">
                            <span class="author-heading">
                                <?php
                                printf( __( 'Publié par %s', 'ws_event_content' ), esc_html( get_the_author() ) );
                                ?>
                            </span>
                        </h2>
                        <p class="author-description">
                            <?php the_author_meta( 'description' ); ?>
                            <a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
                                <?php _e( 'Voir les autres publications de l\'auteur', 'ws_event_content' ); ?>
                            </a>
                        </p><!-- .author-description -->
                    </div><!-- .author-bio -->
                    <?php endif; ?>
                </article><!-- #post-<?php the_ID(); ?> -->
                <?php
			endwhile; // End the loop.
			?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();
