<?php

namespace WPLibs\Form\Core\Utils;

class Post_Finder {
	/**
	 * Run needed hooks.
	 *
	 * @since  0.3.0
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'admin_footer', [ $this, 'admin_footer' ] );
		add_action( 'customize_controls_print_footer_scripts', [ $this, 'admin_footer' ] );
		add_action( 'wp_ajax_pf_search_posts', [ $this, 'search_posts' ] );
	}

	/**
	 * Loads post-finder text domain.
	 *
	 * @since  0.3.0
	 * @uses   load_plugin_textdomain
	 *
	 * @access public
	 * @action plugins_loaded
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'post-finder', false, basename( POST_FINDER_PATH ) . '/languages' );
	}

	/**
	 * Enable our scripts and stylesheets
	 *
	 * @since  0.1.0
	 *
	 * @access public
	 * @action admin_enqueue_scripts
	 * @return void
	 */
	public function scripts() {
		$baseurl  = trailingslashit( POST_FINDER_URL );
		$post_fix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script(
			'post-finder',
			"{$baseurl}assets/js/post-finder{$post_fix}.js",
			[
				'jquery',
				'jquery-ui-draggable',
				'jquery-ui-sortable',
				'underscore',
			],
			POST_FINDER_VERSION,
			true
		);

		wp_localize_script(
			'post-finder',
			'POST_FINDER_CONFIG',
			[
				'adminurl'           => admin_url(),
				'nothing_found'      => esc_html__( 'Nothing Found', 'post_finder' ),
				'max_number_allowed' => esc_html__( 'Sorry, maximum number of items added.', 'post_finder' ),
				'already_added'      => esc_html__( 'Sorry, that item has already been added.', 'post_finder' ),
				'next'               => esc_html__( 'Next', 'post_finder' ),
			]
		);

		wp_enqueue_style(
			'post-finder',
			"{$baseurl}assets/css/post-finder{$post_fix}.css",
			[],
			POST_FINDER_VERSION
		);
	}

	/**
	 * Output our nonce and JS templates on all admin pages
	 *
	 * @since  0.1.0
	 *
	 * @access public
	 * @action admin_footer
	 * @action customize_controls_print_footer_scripts
	 * @return void
	 */
	public function admin_footer() {
		wp_nonce_field( 'post_finder', 'post_finder_nonce' );
		$this->_render_js_templates();
	}

	/**
	 * A variant of wp_kses() that's safe for escaping Underscores templates.
	 *
	 * This acts as a wrapper around wp_kses(), first replacing common Underscores template tags with
	 * attribute-safe strings, then restoring the Underscores template tags when wp_kses() has run
	 * through all of $string.
	 *
	 * @since  0.1.0
	 *
	 * @access public
	 * @param string $string            Content to run through kses.
	 * @param array  $allowed_html      Allowed HTML elements.
	 * @param array  $allowed_protocols Allowed protocol in links.
	 * @return string The Underscores-ready template.
	 */
	public function underscores_safe_kses( $string, $allowed_html, $allowed_protocols = [] ) {
		// Escape Underscores
		$string = str_replace( '<%= ', '__UNDERSCORES_OPEN_ECHO_TAG__', $string );
		$string = str_replace( '<% ', '__UNDERSCORES_OPEN_TAG__', $string );
		$string = str_replace( ' %>', '__UNDERSCORES_CLOSE_TAG__', $string );

		$string = wp_kses( $string, $allowed_html, $allowed_protocols );

		// Restore Underscores
		$string = str_replace( '__UNDERSCORES_OPEN_ECHO_TAG__', '<%= ', $string );
		$string = str_replace( '__UNDERSCORES_OPEN_TAG__', '<% ', $string );
		$string = str_replace( '__UNDERSCORES_CLOSE_TAG__', ' %>', $string );

		return $string;
	}

	/**
	 * Output JS templates for use.
	 *
	 * @since  0.1.0
	 *
	 * @access private
	 * @return void
	 */
	private function _render_js_templates() {
		$main_template =
			'<li data-id="<%= id %>">
				<input type="text" size="3" maxlength="3" max="3" value="<%= pos %>">
				<span><%= title %></span>
				<nav>
					<a href="<%= editUrl %>" class="edit" target="_blank" title="' . esc_attr__( 'Edit', 'post-finder' ) . '">' . esc_html__( 'Edit',
				'post-finder' ) . '</a>
					<a href="<%= permalink %>" class="view" target="_blank" title="' . esc_attr__( 'View', 'post-finder' ) . '">' . esc_html__( 'View',
				'post-finder' ) . '</a>
					<a href="#" class="delete" title="' . esc_attr__( 'Remove', 'post-finder' ) . '">' . esc_html__( 'Remove', 'post-finder' ) . '</a>
				</nav>
			</li>';

		$item_template =
			'<li data-id="<%= ID %>" data-permalink="<%= permalink %>">
				<a href="#" class="add">' . esc_html__( 'Add', 'post-finder' ) . '</a>
				<span><%= post_title %></span>
			</li>';

		/**
		 * Filters the main item template.
		 *
		 * @since 0.1.0
		 *
		 * @param string $main_template Main JS template.
		 */
		$main_template = apply_filters( 'post_finder_main_template', $main_template );

		/**
		 * Filters the single item template.
		 *
		 * @since 0.1.0
		 *
		 * @param string $item_template Single item JS template.
		 */
		$item_template = apply_filters( 'post_finder_item_template', $item_template );

		$allowed_html = [
			'li'    => [
				'data-id'        => true,
				'data-permalink' => true,
			],
			'input' => [
				'type'      => true,
				'size'      => true,
				'maxlength' => true,
				'max'       => true,
				'value'     => true,
			],
			'a'     => [
				'href'   => true,
				'class'  => true,
				'target' => true,
				'title'  => true,
			],
			'nav'   => [],
			'span'  => [],
		];

		/**
		 * Filters the allowed HTML.
		 *
		 * @since 0.1.0
		 *
		 * @param array $allowed_html Array of allowed HTML.
		 */
		$allowed_html = apply_filters( 'post_finder_allowed_html', $allowed_html );

		?>
		<script type="text/html" id="tmpl-post-finder-main">
			<?php
			// @codingStandardsIgnoreStart
			// Ignoring because this output is filtered in underscores_safe_kses()
			echo $this->underscores_safe_kses( $main_template, $allowed_html );
			// @codingStandardsIgnoreEnd ?>
		</script>

		<script type="text/html" id="tmpl-post-finder-item">
			<?php
			// @codingStandardsIgnoreStart
			// Ignoring because this output is filtered in underscores_safe_kses()
			echo $this->underscores_safe_kses( $item_template, $allowed_html );
			// @codingStandardsIgnoreEnd ?>
		</script><?php
	}

	/**
	 * Build the post finder input that lets the user find and order posts.
	 *
	 * @since  0.1.0
	 *
	 * @static
	 * @access public
	 * @param string $name    Name of input
	 * @param string $value   Expecting comma separated post ids
	 * @param array  $options Field options
	 * @return void
	 */
	public static function render( $name, $value, $options = [] ) {
		$options = wp_parse_args( $options, [
			'show_numbers'   => true, // display # next to post
			'show_recent'    => true, // display the Recent Post input
			'limit'          => 10,
			'include_script' => true, // Should the <script> tags to init post finder be included or not
		] );

		/**
		 * Filters the post finder render options.
		 *
		 * @since 0.1.0
		 *
		 * @param array $options Current render options.
		 */
		$options = apply_filters( 'post_finder_render_options', $options );

		// check to see if we have query args
		$args = isset( $options['args'] ) ? $options['args'] : [];

		// setup some defaults
		$args = wp_parse_args( $args, [
			'post_type'        => 'post',
			'posts_per_page'   => 10,
			'post_status'      => 'publish',
			'suppress_filters' => false,
		] );

		// now that we have a post type, figure out the proper labels
		if ( is_array( $args['post_type'] ) ) {
			$select_message  = __( 'Select a Recent Item', 'post-finder' );
			$choose_message  = __( 'Choose an Item', 'post-finder' );
			$search_message  = __( 'Search for an Item', 'post-finder' );
			$noadded_message = __( 'No Items Added', 'post-finder' );
		} elseif ( ( $post_type = get_post_type_object( $args['post_type'] ) ) ) {
			$select_message  = sprintf( _x( 'Select a Recent %s', 'Select a Recent {Type}', 'post-finder' ), $post_type->labels->singular_name );
			$choose_message  = sprintf( _x( 'Choose %s', 'Choose {Post}', 'post-finder' ), $post_type->labels->singular_name );
			$search_message  = sprintf( _x( 'Search for %s', 'Search for {Post}', 'post-finder' ), $post_type->labels->singular_name );
			$noadded_message = sprintf( _x( 'No %s Added', 'No {Posts} Added', 'post-finder' ), $post_type->labels->name );
		} else {
			$select_message  = __( 'Select a Recent Post', 'post-finder' );
			$choose_message  = __( 'Choose a Post', 'post-finder' );
			$search_message  = __( 'Search for a Post', 'post-finder' );
			$noadded_message = __( 'No Posts Added', 'post-finder' );
		}

		// get current selected posts if we have a value
		if ( ! empty( $value ) && is_string( $value ) ) {
			$post_ids = array_map( 'intval', explode( ',', $value ) );

			$posts = new WP_Query( [
				'post_type'        => $args['post_type'],
				'post_status'      => $args['post_status'],
				'post__in'         => $post_ids,
				'orderby'          => 'post__in',
				'suppress_filters' => false,
				'posts_per_page'   => count( $post_ids ),
			] );

			$posts = $posts->have_posts() ? $posts->posts : [];
			wp_reset_postdata();
		} else {
			$posts = [];
		}

		// if we have some ids already, make sure they aren't included in the recent posts
		if ( ! empty( $post_ids ) ) {
			$args['post__not_in'] = $post_ids;
		}

		// Get recent posts, if that option is enabled
		if ( $options['show_recent'] ) {
			/**
			 * Filters the recent post args.
			 *
			 * @since 0.1.0
			 *
			 * @param array $args Current args.
			 */
			$recent_posts_args = apply_filters( 'post_finder_' . $name . '_recent_post_args', $args );

			$recent_posts = new WP_Query( $recent_posts_args );
			$recent_posts = $recent_posts->have_posts() ? $recent_posts->posts : [];

			wp_reset_postdata();
		} else {
			$recent_posts = '';
		}

		$class = 'post-finder';

		if ( ! $options['show_numbers'] ) {
			$class .= ' no-numbers';
		}

		?>
		<div class="<?php echo esc_attr( $class ); ?>" data-limit="<?php echo intval( $options['limit'] ); ?>"
		     data-args='<?php echo wp_json_encode( $args ); ?>'>

			<?php if ( $recent_posts ) : ?>

				<h4>
					<label for="post-finder-recent">
						<?php echo esc_html( $select_message ); ?>
					</label>
				</h4>
				<select name="post-finder-recent" id="post-finder-recent">
					<option value="0">
						<?php echo esc_html( $choose_message ); ?>
					</option>

					<?php foreach ( $recent_posts as $post ) : ?>
						<option value="<?php echo intval( $post->ID ); ?>" data-permalink="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>">
							<?php
							/**
							 * Filters the post title.
							 *
							 * @since 0.1.0
							 *
							 * @param string  $post_title Current post title.
							 * @param WP_Post $post       Current post object
							 */
							echo esc_html( apply_filters( 'post_finder_item_label', $post->post_title, $post ) ); ?>
						</option>
					<?php endforeach; ?>

				</select>

			<?php endif; ?>

			<div class="search">
				<h4>
					<?php echo esc_html( $search_message ); ?>
				</h4>
				<input type="text" placeholder="<?php esc_attr_e( 'Enter a term or phrase', 'post-finder' ); ?>">
				<button class="button"><?php esc_html_e( 'Search', 'post-finder' ); ?></button>
				<div class="loader"></div>
				<ul class="results"></ul>
			</div><!-- ./search -->

			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">

			<ul class="list">
				<?php
				if ( ! empty( $posts ) ) {
					$i = 1;

					foreach ( $posts as $post ) {
						printf(
							'<li data-id="%1$s">' .
							'<input type="text" size="3" maxlength="3" max="3" value="%2$s">' .
							'<span>%3$s</span>' .
							'<nav>' .
							'<a href="%4$s" class="edit" target="_blank" title="%5$s">%5$s</a>' .
							'<a href="%6$s" class="view" target="_blank" title="%7$s">%7$s</a>' .
							'<a href="#" class="delete" title="%8$s">%8$s</a>' .
							'</nav>' .
							'</li>',
							/* 1 */
							intval( $post->ID ),
							/* 2 */
							intval( $i ),
							/* 3 */
							esc_html( apply_filters( 'post_finder_item_label', $post->post_title, $post ) ),
							/* 4 */
							esc_url( get_edit_post_link( $post->ID ) ),
							/* 5 */
							esc_html__( 'Edit', 'post-finder' ),
							/* 6 */
							esc_url( get_permalink( $post->ID ) ),
							/* 7 */
							esc_html__( 'View', 'post-finder' ),
							/* 8 */
							esc_html__( 'Remove', 'post-finder' )
						);

						$i++;
					}
				} else {
					echo '<p class="notice">' . esc_html( $noadded_message ) . '</p>';
				}
				?>
			</ul>
		</div><!-- /.post-finder -->

		<?php if ( $options['include_script'] ) : ?>

			<script type="text/javascript">
				var pfPerPage = <?php echo absint( $args['posts_per_page'] ); ?>;
				jQuery(document).ready(function ($) {
					$('.post-finder').postFinder()
				})
			</script>

		<?php else : ?>

			<script type="text/javascript">
				var pfPerPage = <?php echo absint( $args['posts_per_page'] ); ?>;
			</script>

		<?php endif;
	}

	/**
	 * Ajax callback to search for posts.
	 *
	 * @since  0.1.0
	 *
	 * @access public
	 * @action wp_ajax_pf_search_posts
	 * @return void
	 */
	public function search_posts() {
		check_ajax_referer( 'post_finder' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// possible vars we'll accept
		$vars = [
			's',
			'page',
			'post_parent',
			'post_status',
		];

		$args = [];

		// clean the basic vars
		foreach ( $vars as $var ) {
			if ( isset( $_POST[ $var ] ) ) {
				if ( is_array( $_POST[ $var ] ) ) {
					$args[ $var ] = array_map( 'sanitize_text_field', $_POST[ $var ] );
				} else {
					$args[ $var ] = sanitize_text_field( $_POST[ $var ] );
				}
			}
		}

		// Make sure our page value is above 1
		if ( isset( $_POST['page'] ) ) {
			unset( $args['page'] );
			$paged = absint( $_POST['page'] );

			if ( $paged > 1 ) {
				$args['paged'] = $paged;
			}
		}

		// Make sure posts_per_page is within a range
		if ( isset( $_POST['posts_per_page'] ) ) {
			$num = intval( $_POST['posts_per_page'] );

			if ( $num <= 0 ) {
				$num = 10;
			} elseif ( $num > 100 ) {
				$num = 100;
			}

			$args['posts_per_page'] = $num;
		}

		// handle post type validation differently
		if ( isset( $_POST['post_type'] ) ) {
			$post_types = get_post_types( [ 'public' => true ] );

			if ( is_array( $_POST['post_type'] ) ) {
				foreach ( $_POST['post_type'] as $type ) {
					if ( in_array( $type, $post_types ) ) {
						$args['post_type'][] = $type;
					}
				}
			} else {
				if ( in_array( $_POST['post_type'], $post_types ) ) {
					$args['post_type'] = $_POST['post_type'];
				}
			}
		}

		// Sanitize tax_queries
		if ( isset( $_POST['tax_query'] ) ) {
			foreach ( $_POST['tax_query'] as $current_tax_query ) {
				$args['tax_query'][] = array_map( 'sanitize_text_field', $current_tax_query );
			}
		}

		$args['suppress_filters'] = false;

		/**
		 * Filters the search args.
		 *
		 * @since 0.1.0
		 *
		 * @param array $args Current search args.
		 */
		$posts = new WP_Query( apply_filters( 'post_finder_search_args', $args ) );
		$posts = $posts->have_posts() ? $posts->posts : [];

		// Get the permalink so that View/Edit links work
		foreach ( $posts as $key => $post ) {
			$posts[ $key ]->permalink = get_permalink( $post->ID );
		}

		/**
		 * Filters the search results.
		 *
		 * @since 0.1.0
		 *
		 * @param array $posts Found posts.
		 */
		$posts = apply_filters( 'post_finder_search_results', $posts );

		wp_reset_postdata();

		header( 'Content-type: text/json' );
		die( wp_json_encode( [ 'posts' => $posts ] ) );
	}
}
