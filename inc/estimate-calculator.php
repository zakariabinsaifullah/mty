<?php
/**
 * Estimate Calculator — pricing storage, server-side pricing and the form hand-off.
 *
 * The pricing lives in one option, `mty_estimate_pricing` (JSON), so every
 * Estimate Calculator block on the site shares it. It's edited from the block's
 * "Pricing" panel in the editor, which saves it through the REST settings
 * endpoint. Starter values come from assets/data/estimate-pricing.json.
 *
 * The functions here mirror src/blocks/estimate-calculator/pricing.js (used by
 * the editor preview and the front end); keep the two in step.
 *
 * @package MTY
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Pricing config ─────────────────────────────────────────────────────────────

/**
 * Starter pricing from assets/data/estimate-pricing.json.
 *
 * @return array
 */
function mty_estimate_default_pricing() {
	static $defaults = null;

	if ( null === $defaults ) {
		$json     = file_get_contents( get_theme_file_path( 'assets/data/estimate-pricing.json' ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$defaults = json_decode( (string) $json, true );
		$defaults = is_array( $defaults ) ? $defaults : array();
	}

	return $defaults;
}

/**
 * Fills in anything missing or malformed and casts every value to its type.
 * Mirrors normalize() in pricing.js.
 *
 * @param mixed $raw Config array or its JSON.
 * @return array
 */
function mty_estimate_normalize( $raw ) {
	$cfg = is_string( $raw ) ? json_decode( $raw, true ) : $raw;

	if ( ! is_array( $cfg ) ) {
		$cfg = mty_estimate_default_pricing();
	}

	$defaults = mty_estimate_default_pricing();
	$num      = static function ( $value, $fallback = 0 ) {
		return is_numeric( $value ) ? (float) $value : (float) $fallback;
	};
	$text     = static function ( $value, $fallback = '' ) {
		return sanitize_text_field( is_scalar( $value ) ? (string) $value : $fallback );
	};

	$rev  = array_merge( $defaults['revenue'] ?? array(), is_array( $cfg['revenue'] ?? null ) ? $cfg['revenue'] : array() );
	$min  = $num( $rev['min'] ?? 0 );
	$max  = max( $num( $rev['max'] ?? 2000000, 2000000 ), $min + 1 );
	$step = max( $num( $rev['step'] ?? 1, 1 ), 1 );

	$default_frequencies = $defaults['services'][0]['frequencies'] ?? array( array( 'label' => 'Monthly', 'percent' => 100 ) );

	$services = array();
	$seen     = array();

	foreach ( ( is_array( $cfg['services'] ?? null ) ? $cfg['services'] : array() ) as $i => $s ) {
		if ( ! is_array( $s ) ) {
			continue;
		}

		$frequencies = array();
		foreach ( ( ! empty( $s['frequencies'] ) && is_array( $s['frequencies'] ) ? $s['frequencies'] : $default_frequencies ) as $f ) {
			$frequencies[] = array(
				'label'   => $text( $f['label'] ?? '' ),
				'percent' => $num( $f['percent'] ?? 100, 100 ),
			);
		}

		$tiers = array();
		foreach ( ( is_array( $s['tiers'] ?? null ) ? $s['tiers'] : array() ) as $t ) {
			$tiers[] = array(
				'upTo'  => $num( $t['upTo'] ?? 0 ),
				'price' => $num( $t['price'] ?? 0 ),
			);
		}

		$id = sanitize_title( $text( $s['id'] ?? '' ) ? $s['id'] : ( $s['name'] ?? '' ) );
		$id = $id ? $id : 'service-' . ( $i + 1 );

		$seen[ $id ] = ( $seen[ $id ] ?? 0 ) + 1;
		if ( $seen[ $id ] > 1 ) {
			$id .= '-' . $seen[ $id ];
		}

		$services[] = array(
			'id'          => $id,
			'name'        => $text( $s['name'] ?? '' ),
			'defaultOn'   => false !== ( $s['defaultOn'] ?? true ),
			'showToggle'  => false !== ( $s['showToggle'] ?? true ),
			'method'      => in_array( $s['method'] ?? '', array( 'tiers', 'formula', 'flat' ), true ) ? $s['method'] : 'tiers',
			'tiers'       => $tiers,
			'base'        => $num( $s['base'] ?? 0 ),
			'rate'        => $num( $s['rate'] ?? 0 ),
			'per'         => max( $num( $s['per'] ?? 100000, 100000 ), 1 ),
			'flat'        => $num( $s['flat'] ?? 0 ),
			'minPrice'    => $num( $s['minPrice'] ?? 0 ),
			'maxPrice'    => $num( $s['maxPrice'] ?? 0 ),
			'frequencies' => $frequencies,
		);
	}

	$bundle = is_array( $cfg['bundle'] ?? null ) ? $cfg['bundle'] : array();

	return array(
		'currency'    => $text( $cfg['currency'] ?? '$', '$' ),
		'rounding'    => max( $num( $cfg['rounding'] ?? 0 ), 0 ),
		'revenue'     => array(
			'min'     => $min,
			'max'     => $max,
			'step'    => $step,
			'default' => min( max( $num( $rev['default'] ?? $min, $min ), $min ), $max ),
			'shared'  => ! empty( $rev['shared'] ),
		),
		'overMax'     => 'calculate' === ( $cfg['overMax'] ?? '' ) ? 'calculate' : 'contact',
		'overMaxText' => $text( $cfg['overMaxText'] ?? ( $defaults['overMaxText'] ?? '' ) ),
		'bundle'      => array(
			'minServices' => max( $num( $bundle['minServices'] ?? 2, 2 ), 2 ),
			'percent'     => min( max( $num( $bundle['percent'] ?? 0 ), 0 ), 100 ),
		),
		'services'    => $services,
	);
}

/**
 * The site's pricing, normalized.
 *
 * @return array
 */
function mty_estimate_get_pricing() {
	return mty_estimate_normalize( get_option( 'mty_estimate_pricing', '' ) );
}

/**
 * Sanitizes the option on save: whatever comes in is normalized and stored as JSON.
 *
 * @param mixed $value Incoming value.
 * @return string
 */
function mty_estimate_sanitize_pricing( $value ) {
	return wp_json_encode( mty_estimate_normalize( $value ) );
}

add_action( 'init', 'mty_estimate_register_setting' );

/**
 * Registers the pricing option, exposed to the REST settings endpoint so the
 * block editor can read and save it (administrators only, as with any setting).
 */
function mty_estimate_register_setting() {
	register_setting(
		'mty_estimate',
		'mty_estimate_pricing',
		array(
			'type'              => 'string',
			'description'       => __( 'Estimate Calculator pricing (JSON).', 'mty' ),
			'sanitize_callback' => 'mty_estimate_sanitize_pricing',
			'show_in_rest'      => true,
			'default'           => '',
		)
	);
}

add_action( 'enqueue_block_editor_assets', 'mty_estimate_editor_data' );

/**
 * Gives the editor the current pricing, so the block preview works for users
 * who can't read site settings (only administrators can edit the pricing).
 */
function mty_estimate_editor_data() {
	wp_add_inline_script(
		'wp-blocks',
		'window.mtyEstimatePricing = ' . wp_json_encode( mty_estimate_get_pricing() ) . ';',
		'after'
	);
}

// ── Pricing (server render) ────────────────────────────────────────────────────

/**
 * Formats money: 2500 → "$2,500". Mirrors formatMoney() in pricing.js.
 *
 * @param float  $value    Amount.
 * @param string $currency Currency symbol.
 * @return string
 */
function mty_estimate_money( $value, $currency = '$' ) {
	$rounded = (int) round( $value );
	return ( $rounded < 0 ? '-' : '' ) . $currency . number_format( abs( $rounded ) );
}

/**
 * Monthly price of one service at a revenue. Mirrors servicePrice() in pricing.js.
 *
 * @param array $service Normalized service.
 * @param float $revenue Annual revenue.
 * @return float
 */
function mty_estimate_service_price( $service, $revenue ) {
	if ( 'flat' === $service['method'] ) {
		$price = $service['flat'];
	} elseif ( 'formula' === $service['method'] ) {
		$price = $service['base'] + ( max( $revenue, 0 ) / $service['per'] ) * $service['rate'];
	} else {
		$tiers = $service['tiers'];
		usort(
			$tiers,
			static function ( $a, $b ) {
				return ( $a['upTo'] ? $a['upTo'] : INF ) <=> ( $b['upTo'] ? $b['upTo'] : INF );
			}
		);

		$tier = null;
		foreach ( $tiers as $t ) {
			if ( ! $t['upTo'] || $revenue <= $t['upTo'] ) {
				$tier = $t;
				break;
			}
		}
		$tier  = $tier ? $tier : end( $tiers );
		$price = $tier ? $tier['price'] : 0;
	}

	if ( $service['minPrice'] > 0 ) {
		$price = max( $price, $service['minPrice'] );
	}
	if ( $service['maxPrice'] > 0 ) {
		$price = min( $price, $service['maxPrice'] );
	}

	return (float) $price;
}

/**
 * The estimate with every service at its defaults. Mirrors calculate() in
 * pricing.js for initialState().
 *
 * @param array $cfg Normalized config.
 * @return array { total, overMax, empty }
 */
function mty_estimate_initial_result( $cfg ) {
	$revenue  = $cfg['revenue']['default'];
	$total    = 0;
	$count    = 0;
	$over_max = false;

	foreach ( $cfg['services'] as $service ) {
		if ( $service['showToggle'] && ! $service['defaultOn'] ) {
			continue;
		}

		++$count;
		$total += mty_estimate_service_price( $service, $revenue ) * $service['frequencies'][0]['percent'] / 100;

		if ( 'contact' === $cfg['overMax'] && $revenue >= $cfg['revenue']['max'] && 'flat' !== $service['method'] ) {
			$over_max = true;
		}
	}

	if ( $cfg['bundle']['percent'] > 0 && $count >= $cfg['bundle']['minServices'] ) {
		$total *= 1 - $cfg['bundle']['percent'] / 100;
	}

	if ( $cfg['rounding'] > 0 ) {
		$total = round( $total / $cfg['rounding'] ) * $cfg['rounding'];
	}

	return array(
		'total'   => $total,
		'overMax' => $over_max,
		'empty'   => 0 === $count,
	);
}

// ── Gravity Forms hand-off ─────────────────────────────────────────────────────
//
// When a visitor clicks a button inside the calculator's estimate panel, view.js
// adds a hidden `mty_estimate` input (a plain-text summary of their estimate)
// to the Gravity Forms forms on the page. When the form is submitted, the
// summary is saved on the entry (as a note and an "Estimate" column) and can be
// added to notification emails with the {mty_estimate} merge tag; notifications
// that use {all_fields} get it automatically.

/**
 * The submitted estimate summary, if any.
 *
 * @return string
 */
function mty_estimate_posted_summary() {
	if ( empty( $_POST['mty_estimate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Gravity Forms verifies the submission.
		return '';
	}

	$summary = sanitize_textarea_field( wp_unslash( $_POST['mty_estimate'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	return function_exists( 'mb_substr' ) ? mb_substr( $summary, 0, 2000 ) : substr( $summary, 0, 2000 );
}

add_filter( 'gform_entry_meta', 'mty_estimate_entry_meta', 10, 2 );

/**
 * Adds "Estimate" as an entry column (Forms → Entries → column settings).
 *
 * @param array $entry_meta Entry meta.
 * @return array
 */
function mty_estimate_entry_meta( $entry_meta ) {
	$entry_meta['mty_estimate'] = array(
		'label'             => __( 'Estimate', 'mty' ),
		'is_numeric'        => false,
		'is_default_column' => false,
		'update_entry_meta_callback' => 'mty_estimate_entry_meta_value',
	);

	return $entry_meta;
}

/**
 * Saves the posted summary as the entry's "Estimate" meta.
 *
 * @param string $key   Meta key.
 * @param array  $entry Entry.
 * @param array  $form  Form.
 * @return string
 */
function mty_estimate_entry_meta_value( $key, $entry, $form ) {
	return mty_estimate_posted_summary();
}

add_action( 'gform_entry_created', 'mty_estimate_add_entry_note', 10, 2 );

/**
 * Adds the estimate as a note, so it shows on the entry's detail screen.
 *
 * @param array $entry Entry.
 * @param array $form  Form.
 */
function mty_estimate_add_entry_note( $entry, $form ) {
	$summary = mty_estimate_posted_summary();

	if ( $summary && class_exists( 'GFAPI' ) ) {
		GFAPI::add_note( $entry['id'], 0, __( 'Estimate Calculator', 'mty' ), $summary );
	}
}

add_filter( 'gform_notification', 'mty_estimate_notification', 10, 3 );

/**
 * Adds the estimate to notifications that list every field ({all_fields}).
 *
 * @param array $notification Notification.
 * @param array $form         Form.
 * @param array $entry        Entry.
 * @return array
 */
function mty_estimate_notification( $notification, $form, $entry ) {
	$message = $notification['message'] ?? '';

	if ( false === strpos( $message, '{all_fields' ) || false !== strpos( $message, '{mty_estimate}' ) ) {
		return $notification;
	}

	if ( '' === mty_estimate_entry_summary( $entry ) ) {
		return $notification;
	}

	$heading = __( 'Estimate Calculator', 'mty' );
	$is_text = 'text' === ( $notification['message_format'] ?? 'html' );

	$notification['message'] = $message . ( $is_text
		? "\n\n" . $heading . "\n{mty_estimate}"
		: '<p><strong>' . esc_html( $heading ) . '</strong><br>{mty_estimate}</p>' );

	return $notification;
}

/**
 * The estimate saved on an entry (falls back to the current submission).
 *
 * @param array $entry Entry.
 * @return string
 */
function mty_estimate_entry_summary( $entry ) {
	$summary = '';

	if ( ! empty( $entry['id'] ) && function_exists( 'gform_get_meta' ) ) {
		$summary = (string) gform_get_meta( $entry['id'], 'mty_estimate' );
	}

	return $summary ? $summary : mty_estimate_posted_summary();
}

add_filter( 'gform_replace_merge_tags', 'mty_estimate_merge_tags', 10, 7 );

/**
 * Replaces the {mty_estimate} merge tag.
 *
 * @param string $text       Text with merge tags.
 * @param array  $form       Form.
 * @param array  $entry      Entry.
 * @param bool   $url_encode Whether to URL-encode.
 * @param bool   $esc_html   Whether to escape HTML.
 * @param bool   $nl2br      Whether to convert newlines.
 * @param string $format     html or text.
 * @return string
 */
function mty_estimate_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
	if ( false === strpos( $text, '{mty_estimate}' ) ) {
		return $text;
	}

	$summary = mty_estimate_entry_summary( is_array( $entry ) ? $entry : array() );
	$value   = 'html' === $format ? nl2br( esc_html( $summary ) ) : $summary;
	$value   = $url_encode ? rawurlencode( $value ) : $value;

	return str_replace( '{mty_estimate}', $value, $text );
}

add_filter( 'gform_custom_merge_tags', 'mty_estimate_custom_merge_tag', 10, 4 );

/**
 * Lists {mty_estimate} in the merge tag picker.
 *
 * @param array $merge_tags Custom merge tags.
 * @return array
 */
function mty_estimate_custom_merge_tag( $merge_tags ) {
	$merge_tags[] = array(
		'label' => __( 'Estimate Calculator', 'mty' ),
		'tag'   => '{mty_estimate}',
	);

	return $merge_tags;
}
