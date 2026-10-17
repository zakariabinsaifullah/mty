<?php
/**
 * MTY — theme documentation page (Appearance → MTY).
 *
 * A guide to what the theme adds (posts grid shortcode, estimate calculator,
 * MTY blocks, contact panel), written for whoever is building pages rather
 * than for a developer. Keep it in step with inc/shortcode.php and the blocks.
 *
 * @package MTY
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Add menu page under Appearance ────────────────────────────────────────────

add_action( 'admin_menu', 'mty_docs_add_menu' );

function mty_docs_add_menu() {
	add_theme_page(
		__( 'MTY', 'mty' ),
		__( 'MTY', 'mty' ),
		'manage_options',
		'mty-docs',
		'mty_docs_render_page'
	);
}

// ── Enqueue admin assets on the docs page ─────────────────────────────────────

add_action( 'admin_enqueue_scripts', 'mty_docs_admin_assets' );

function mty_docs_admin_assets( $hook ) {
	if ( 'appearance_page_mty-docs' !== $hook ) {
		return;
	}

	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'mty-docs',
		get_theme_file_uri( 'assets/css/admin-docs.css' ),
		array(),
		$version
	);

	// Same generic `.mty-copy-button` handler the Form Settings page uses.
	wp_enqueue_script(
		'mty-form-settings',
		get_theme_file_uri( 'assets/js/form-settings.js' ),
		array(),
		$version,
		true
	);
}

// ── Render helpers ────────────────────────────────────────────────────────────

if ( ! function_exists( 'mty_docs_code' ) ) :
	/**
	 * Outputs a copyable code sample.
	 *
	 * @param string $code  The snippet.
	 * @param string $label Optional caption shown above the snippet.
	 */
	function mty_docs_code( $code, $label = '' ) {
		?>
		<div class="mty-docs-code">
			<?php if ( $label ) : ?>
				<p class="mty-docs-code__label"><?php echo esc_html( $label ); ?></p>
			<?php endif; ?>
			<div class="mty-docs-code__row">
				<code><?php echo esc_html( $code ); ?></code>
				<button
					type="button"
					class="button mty-copy-button"
					data-copy="<?php echo esc_attr( $code ); ?>"
					data-label="<?php esc_attr_e( 'Copy', 'mty' ); ?>"
					data-copied="<?php esc_attr_e( '✓ Copied!', 'mty' ); ?>"
				><?php esc_html_e( 'Copy', 'mty' ); ?></button>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'mty_docs_attr_table' ) ) :
	/**
	 * Outputs an attribute (or setting) reference table.
	 *
	 * @param array $rows    Each row: array( name, default, description ).
	 * @param array $headers Optional column headings; when given, the names are
	 *                       shown as plain text rather than code (for settings).
	 */
	function mty_docs_attr_table( $rows, $headers = array() ) {
		$is_code = empty( $headers );
		$headers = $is_code ? array( __( 'Attribute', 'mty' ), __( 'Default', 'mty' ), __( 'What it does', 'mty' ) ) : $headers;
		?>
		<table class="widefat striped mty-docs-table">
			<thead>
				<tr>
					<?php foreach ( $headers as $header ) : ?>
						<th scope="col"><?php echo esc_html( $header ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<td>
							<?php if ( $is_code ) : ?>
								<code><?php echo esc_html( $row[0] ); ?></code>
							<?php else : ?>
								<strong><?php echo esc_html( $row[0] ); ?></strong>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( '' === $row[1] ) : ?>
								<span class="mty-docs-empty"><?php esc_html_e( 'empty', 'mty' ); ?></span>
							<?php else : ?>
								<code><?php echo esc_html( $row[1] ); ?></code>
							<?php endif; ?>
						</td>
						<td><?php echo wp_kses_post( $row[2] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
endif;

// ── Render docs page ──────────────────────────────────────────────────────────

function mty_docs_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap mty-docs">
		<h1><?php esc_html_e( 'MTY', 'mty' ); ?></h1>
		<p class="mty-docs-intro">
			<?php esc_html_e( 'A quick guide to what this theme adds: the posts grid shortcode, the estimate calculator, the MTY blocks and the contact panel.', 'mty' ); ?>
		</p>

		<!-- ── Posts grid ─────────────────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2><?php esc_html_e( 'Posts grid', 'mty' ); ?> <code>[mty_posts_grid]</code></h2>

			<p>
				<?php esc_html_e( 'A filterable, paginated grid of posts, used on the Resources page. Clicking a category or a page number swaps the cards in place, without reloading the page. Add it to a page with a Shortcode block.', 'mty' ); ?>
			</p>

			<?php mty_docs_code( '[mty_posts_grid]', __( 'Simplest version: six posts, all categories, heading “All Insights.”', 'mty' ) ); ?>

			<h3><?php esc_html_e( 'Attributes', 'mty' ); ?></h3>

			<?php
			mty_docs_attr_table(
				array(
					array(
						'title',
						'All Insights.',
						__( 'Heading shown at the top left, opposite the category links, as an <code>h2</code>. Pass <code>title=""</code> to leave it out.', 'mty' ),
					),
					array(
						'per_page',
						'6',
						__( 'How many posts each page of the grid shows. Anything above 50 is capped at 50.', 'mty' ),
					),
					array(
						'categories',
						'',
						__( 'Comma-separated category IDs or slugs. Limits both the posts shown and the category links offered. Leave empty to use every category that has posts.', 'mty' ),
					),
					array(
						'post_type',
						'post',
						__( 'Which post type to list. Falls back to <code>post</code> if the name is not a registered post type.', 'mty' ),
					),
				)
			);
			?>

			<h3><?php esc_html_e( 'Examples', 'mty' ); ?></h3>

			<?php
			mty_docs_code(
				'[mty_posts_grid title="Start with these." per_page="9" categories="6,7,8,9,10,11"]',
				__( 'The Resources page: nine posts per page from six categories.', 'mty' )
			);

			mty_docs_code(
				'[mty_posts_grid categories="tax-planning,valuation"]',
				__( 'Only two categories, by slug.', 'mty' )
			);

			mty_docs_code(
				'[mty_posts_grid title=""]',
				__( 'No heading, category links only.', 'mty' )
			);
			?>

			<h3><?php esc_html_e( 'What it shows', 'mty' ); ?></h3>
			<ul class="mty-docs-list">
				<li><?php esc_html_e( 'Each card: featured image (left out if the post has none), category, title and a “Read More” button.', 'mty' ); ?></li>
				<li><?php esc_html_e( 'Three columns on desktop, one on tablet and mobile. On small screens the category links scroll sideways.', 'mty' ); ?></li>
				<li><?php esc_html_e( 'Under the cards: a count (“Showing 9 of 16 articles”) with the page numbers opposite it.', 'mty' ); ?></li>
			</ul>
		</div>

		<!-- ── Estimate calculator ────────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2><?php esc_html_e( 'Estimate calculator', 'mty' ); ?> <code><?php esc_html_e( 'Estimate Calculator block', 'mty' ); ?></code></h2>

			<p>
				<?php esc_html_e( 'Visitors switch services on or off, set their annual revenue and pick a frequency, and see a monthly estimate straight away. Used on the Pricing & Calculator page.', 'mty' ); ?>
			</p>

			<h3><?php esc_html_e( 'Changing the prices', 'mty' ); ?></h3>
			<ol class="mty-docs-list mty-docs-list--ordered">
				<li><?php esc_html_e( 'Open the page in the editor and click the calculator.', 'mty' ); ?></li>
				<li><?php esc_html_e( 'In the sidebar, under Pricing, click “Edit pricing & services”.', 'mty' ); ?></li>
				<li><?php esc_html_e( 'Make your changes; the calculator behind the window updates as you type. Click Done, then save the page.', 'mty' ); ?></li>
			</ol>

			<?php
			mty_docs_attr_table(
				array(
					array(
						__( 'Services', 'mty' ),
						'Accounting, Bookkeeping, Fractional CFO',
						__( 'Add, remove, rename and reorder services. Price each one by revenue band (a price table), as a base price plus an amount per slice of revenue, or as a flat monthly fee. Set a minimum and maximum price, and whether visitors can switch it off.', 'mty' ),
					),
					array(
						__( 'Frequency options', 'mty' ),
						'Monthly, Quarterly',
						__( 'Per service. Each option charges a share of the monthly price (100% = full price). With only one option, no choice is shown.', 'mty' ),
					),
					array(
						__( 'Revenue slider', 'mty' ),
						'$0 – $2,000,000',
						__( 'Lowest and highest values, step size and starting value; one slider for all services or one per service.', 'mty' ),
					),
					array(
						__( 'Estimate', 'mty' ),
						'$, nearest $50',
						__( 'Currency symbol, rounding, a discount for taking several services, and what shows when the slider is at its highest (a message such as “Let’s talk”, or a normal price).', 'mty' ),
					),
				),
				array( __( 'Setting', 'mty' ), __( 'Starts as', 'mty' ), __( 'What you can change', 'mty' ) )
			);
			?>

			<div class="mty-docs-note">
				<p>
					<?php esc_html_e( 'Prices are shared: every Estimate Calculator on the site uses the same price list. Only administrators can change it.', 'mty' ); ?>
				</p>
			</div>

			<h3><?php esc_html_e( 'Wording and layout', 'mty' ); ?></h3>
			<p>
				<?php esc_html_e( 'The block’s sidebar also holds the Steps (the three step names, or hide them), the Labels (slider and frequency labels, “Your Estimate”, “Per Month”, the asterisk) and the estimate panel’s colour. The Contact Us button, divider and text boxes inside the panel are ordinary blocks: click them to edit.', 'mty' ); ?>
			</p>

			<h3><?php esc_html_e( 'Where estimates arrive', 'mty' ); ?></h3>
			<p>
				<?php esc_html_e( 'When a visitor clicks a button in the estimate panel and then sends the contact form, their estimate and choices are saved with the form entry:', 'mty' ); ?>
			</p>
			<ul class="mty-docs-list">
				<li><?php esc_html_e( 'as a note on the entry, under Forms → Entries;', 'mty' ); ?></li>
				<li><?php esc_html_e( 'as an “Estimate” column you can switch on in the entries list;', 'mty' ); ?></li>
				<li><?php esc_html_e( 'in notification emails: automatically where the email lists all fields, or anywhere you add the merge tag below.', 'mty' ); ?></li>
			</ul>
			<?php mty_docs_code( '{mty_estimate}', __( 'Merge tag for Gravity Forms notifications and confirmations.', 'mty' ) ); ?>
		</div>

		<!-- ── MTY blocks ────────────────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2><?php esc_html_e( 'MTY blocks', 'mty' ); ?></h2>
			<p>
				<?php esc_html_e( 'Find these in the block inserter under the MTY category.', 'mty' ); ?>
			</p>

			<table class="widefat striped mty-docs-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Block', 'mty' ); ?></th>
						<th scope="col"><?php esc_html_e( 'What it does', 'mty' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Icon', 'mty' ); ?></td>
						<td><?php esc_html_e( 'An SVG icon in an orange box, optionally with a title beside it. Icons you paste can be saved under My Icons for reuse.', 'mty' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Hover Card', 'mty' ); ?></td>
						<td><?php esc_html_e( 'An image tile with an icon and heading; on hover (or tap) it turns orange and its description slides in.', 'mty' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Stacking Cards', 'mty' ); ?></td>
						<td><?php esc_html_e( 'Cards that pin and stack on top of each other as the page scrolls, on desktop. Put one Row Layout inside per card.', 'mty' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Testimonials Marquee', 'mty' ); ?></td>
						<td>
							<?php
							printf(
								/* translators: %s: link to the Testimonials list. */
								esc_html__( 'An endlessly scrolling row of testimonials. Add and edit them under %s: the title is the name, the content is the quote, the featured image is the photo.', 'mty' ),
								'<a href="' . esc_url( admin_url( 'edit.php?post_type=mty_testimonial' ) ) . '">' . esc_html__( 'Testimonials', 'mty' ) . '</a>'
							);
							?>
						</td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Estimate Calculator', 'mty' ); ?></td>
						<td><?php esc_html_e( 'The pricing calculator described above.', 'mty' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Social Share', 'mty' ); ?></td>
						<td><?php esc_html_e( 'Sharing buttons for the current post, for single post templates.', 'mty' ); ?></td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- ── Contact panel ──────────────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2><?php esc_html_e( 'Contact panel', 'mty' ); ?> <code>#mty-contact</code></h2>
			<p>
				<?php
				printf(
					/* translators: %s: link to the Form Settings page. */
					esc_html__( 'The slide-in contact form is set up under %s (phone number, title, description and the form shortcode). Any button or link pointing at #mty-contact opens it.', 'mty' ),
					'<a href="' . esc_url( admin_url( 'themes.php?page=mty-form' ) ) . '">' . esc_html__( 'Appearance → Form', 'mty' ) . '</a>'
				);
				?>
			</p>
		</div>
	</div>
	<?php
}
