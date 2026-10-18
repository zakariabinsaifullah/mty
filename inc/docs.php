<?php
/**
 * MTY — theme documentation page (Appearance → MTY).
 *
 * A guide to what the theme adds (MTY blocks, editor extensions, block styles,
 * team members, posts grid shortcode, contact panel), written for whoever is
 * building pages rather than for a developer. Keep it in step with the blocks,
 * src/extensions, inc/block-styles.php, inc/team.php and inc/shortcode.php.
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

if ( ! function_exists( 'mty_docs_info_table' ) ) :
	/**
	 * Outputs a two-column "name → what it does" table.
	 *
	 * @param array $rows    Each row: array( name, description ). The description
	 *                       may contain inline HTML (<code>, <a>, <strong>).
	 * @param array $headers The two column headings.
	 */
	function mty_docs_info_table( $rows, $headers ) {
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
						<td><strong><?php echo esc_html( $row[0] ); ?></strong></td>
						<td><?php echo wp_kses_post( $row[1] ); ?></td>
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
			<?php esc_html_e( 'A quick guide to what this theme adds: the MTY blocks, the extra settings on core blocks, the block styles, team member pages, the posts grid shortcode and the contact panel.', 'mty' ); ?>
		</p>

		<!-- ── MTY blocks ────────────────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2><?php esc_html_e( 'MTY blocks', 'mty' ); ?></h2>
			<p>
				<?php esc_html_e( 'Find these in the block inserter under the MTY category.', 'mty' ); ?>
			</p>

			<?php
			mty_docs_info_table(
				array(
					array(
						__( 'Icon', 'mty' ),
						__( 'An SVG icon, by default 20px and white in a 45×45px green box, optionally with a title beside it. Paste SVG code or pick an icon from the library; icons you paste can be saved under <strong>My Icons</strong> and reused anywhere on the site.', 'mty' ),
					),
					array(
						__( 'Timeline', 'mty' ),
						__( 'A row of numbered steps joined by one line, each marked by a square dot, with any blocks (number, heading, text) below each dot. Set the number of steps, columns, gaps and colours in the sidebar, and turn on <strong>Highlight step</strong> on the step to highlight. <strong>Highlight line</strong> makes that step’s line a thicker progress bar; <strong>Highlight on hover</strong> moves the highlight to the step under the mouse. On tablet and mobile the steps stack as a plain list.', 'mty' ),
					),
					array(
						__( 'Rating', 'mty' ),
						__( 'A star rating, optionally with the rating number beside it.', 'mty' ),
					),
					array(
						__( 'Social Share', 'mty' ),
						__( 'Sharing buttons for the current post, for single post templates.', 'mty' ),
					),
				),
				array( __( 'Block', 'mty' ), __( 'What it does', 'mty' ) )
			);
			?>
		</div>

		<!-- ── Extra block settings ──────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2><?php esc_html_e( 'Extra block settings', 'mty' ); ?></h2>
			<p>
				<?php esc_html_e( 'The theme adds these options to core and Kadence blocks. Select the block and look in its sidebar (or toolbar, for Highlight).', 'mty' ); ?>
			</p>

			<?php
			mty_docs_info_table(
				array(
					array(
						__( 'Highlight', 'mty' ),
						__( 'Text toolbar, the “A” button next to Bold and Italic. Makes the selected words semi-bold and green, as in “Meet <strong>James.</strong>”. Another MTY colour can be picked in its popover. Use it for coloured words in headings instead of colouring them by hand.', 'mty' ),
					),
					array(
						__( 'Figure Frame', 'mty' ),
						__( 'Image and Post Featured Image → Settings. The framed look from the design: a faint border, a dark overlay, white corner ticks and a “FIG. 02” label. Type the number into <strong>FIG value</strong> (leave it empty for no label) and switch off the parts the design leaves out. On Post Featured Image, <strong>Post title as label</strong> shows “[HEADSHOT: NAME]”, and a post without an image shows an empty framed box.', 'mty' ),
					),
					array(
						__( 'Pointer', 'mty' ),
						__( 'Group and Kadence Section → Settings. A thin white line from the block’s edge ending in a dot, pointing at the image beside it. Add it to the text side, not the image. Choose the direction, length, vertical position and size (Large: 18px dot with a green ring; Small: 10px white dot). Shown on desktop only.', 'mty' ),
					),
					array(
						__( 'Icon Settings', 'mty' ),
						__( 'Button. Buttons already end in a → arrow; turn on <strong>Add Icon to Button</strong> and paste SVG code only when the design shows a different icon, which then replaces the arrow.', 'mty' ),
					),
					array(
						__( 'Hover Color', 'mty' ),
						__( 'Any block with colour settings. Text, background and border colours on hover, and how fast they change.', 'mty' ),
					),
					array(
						__( 'Text Max Width', 'mty' ),
						__( 'Heading and Paragraph. Limits how wide the text runs, so long lines wrap where the design wraps them.', 'mty' ),
					),
					array(
						__( 'Full Height, Overlay Background, Global Hover', 'mty' ),
						__( 'Group. Stretch the group to its column’s height, lay a colour or gradient over its background, and change the colours of everything inside when the whole group is hovered (for example, a card).', 'mty' ),
					),
					array(
						__( 'Featured Image Background', 'mty' ),
						__( 'Kadence Row Layout. Uses the current post’s featured image as the row’s background.', 'mty' ),
					),
				),
				array( __( 'Setting', 'mty' ), __( 'Where and what it does', 'mty' ) )
			);
			?>
		</div>

		<!-- ── Block styles ──────────────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2><?php esc_html_e( 'Block styles', 'mty' ); ?></h2>
			<p>
				<?php esc_html_e( 'Pick these under Styles in the block’s sidebar.', 'mty' ); ?>
			</p>

			<?php
			mty_docs_info_table(
				array(
					array(
						__( 'Button', 'mty' ),
						__( '<strong>Default</strong>: green, uppercase, square, with a → arrow. <strong>Outline</strong>: the same with a transparent background and a thin border, for dark or image backgrounds. <strong>Alternative</strong>: light green background with dark green text. <strong>Link</strong>: a text link with the arrow.', 'mty' ),
					),
					array(
						__( 'List', 'mty' ),
						__( '<strong>Mega Links</strong> and <strong>Mega Links: Large</strong>, for the link lists in the mega menu panels.', 'mty' ),
					),
					array(
						__( 'Post Template', 'mty' ),
						__( '<strong>Card Grid</strong>, for Query Loop card grids: three columns of equal-height cards, a lone last card fills the row, one column on tablet and mobile.', 'mty' ),
					),
					array(
						__( 'Group', 'mty' ),
						__( '<strong>Plain Links</strong> (links inside aren’t underlined, e.g. an “On this page” list) and <strong>Wrap Mobile</strong> (a row that wraps on mobile).', 'mty' ),
					),
					array(
						__( 'Accordion', 'mty' ),
						__( '<strong>Numbered</strong>: numbers each item.', 'mty' ),
					),
				),
				array( __( 'Block', 'mty' ), __( 'Styles', 'mty' ) )
			);
			?>
		</div>

		<!-- ── Team members ──────────────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2>
				<?php
				printf(
					'<a href="%s">%s</a>',
					esc_url( admin_url( 'edit.php?post_type=mty_team' ) ),
					esc_html__( 'Team members', 'mty' )
				);
				?>
			</h2>
			<p>
				<?php esc_html_e( 'Each attorney has their own page at /team/name/, built in the block editor like any page.', 'mty' ); ?>
			</p>

			<h3><?php esc_html_e( 'Adding a team member', 'mty' ); ?></h3>
			<ol class="mty-docs-list mty-docs-list--ordered">
				<li><?php esc_html_e( 'Go to Team → Add Team Member. Choose the Team Member Page layout when it is offered.', 'mty' ); ?></li>
				<li><?php esc_html_e( 'Title: the full name. Excerpt: the short bio shown on team cards. Featured image: the headshot. Page Attributes → Order: their place in the team list.', 'mty' ); ?></li>
				<li><?php esc_html_e( 'Fill in the Team details panel in the sidebar. The page’s name, intro line and facts fill themselves in from it, so don’t retype them.', 'mty' ); ?></li>
				<li><?php esc_html_e( 'Edit, remove or duplicate the middle sections to match their page in the design, and renumber the “01 / 07” labels.', 'mty' ); ?></li>
			</ol>

			<?php
			mty_docs_info_table(
				array(
					array( __( 'Short name', 'mty' ), __( 'First name used in links, e.g. “Read <strong>James’s</strong> bio”.', 'mty' ) ),
					array( __( 'Highlighted name', 'mty' ), __( 'The part of the name shown in green, e.g. “Laura, Jr.”. Leave empty to highlight the last word.', 'mty' ) ),
					array( __( 'Position, Office', 'mty' ), __( 'Shown above the name, e.g. “Founder, Fairhope, Alabama”.', 'mty' ) ),
					array( __( 'Role, Licensed in, Cases, Practicing since', 'mty' ), __( 'The facts listed under the bio hero.', 'mty' ) ),
				),
				array( __( 'Team detail', 'mty' ), __( 'Where it shows', 'mty' ) )
			);
			?>

			<div class="mty-docs-note">
				<p>
					<?php esc_html_e( 'To show a detail in any block, connect the block to the “Team member” source (block bindings) rather than typing it. This works on the member’s page and inside a Query Loop of team members.', 'mty' ); ?>
				</p>
			</div>
		</div>

		<!-- ── Posts grid ─────────────────────────────────────────────────── -->
		<div class="mty-docs-section">
			<h2><?php esc_html_e( 'Posts grid', 'mty' ); ?> <code>[mty_posts_grid]</code></h2>

			<p>
				<?php esc_html_e( 'A filterable, paginated grid of posts. Clicking a category or a page number swaps the cards in place, without reloading the page. Add it to a page with a Shortcode block.', 'mty' ); ?>
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
				'[mty_posts_grid title="Latest articles." per_page="9"]',
				__( 'Nine posts per page with a different heading.', 'mty' )
			);

			mty_docs_code(
				'[mty_posts_grid categories="personal-injury,car-accidents"]',
				__( 'Only two categories, by slug (use your own category slugs or IDs).', 'mty' )
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
