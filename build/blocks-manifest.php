<?php
// This file is generated. Do not modify it manually.
return array(
	'icon' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/icon',
		'version' => '0.0.5',
		'title' => 'Icon',
		'category' => 'mty',
		'description' => 'Display SVG icons from library or upload custom SVGs.',
		'supports' => array(
			'reusable' => false,
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'color' => array(
				'__experimentalSkipSerialization' => true,
				'gradients' => true,
				'link' => false,
				'__experimentalDefaultControls' => array(
					'background' => true,
					'text' => true
				)
			),
			'__experimentalBorder' => array(
				'__experimentalSkipSerialization' => true,
				'color' => true,
				'radius' => true,
				'style' => true,
				'width' => true
			),
			'spacing' => array(
				'__experimentalSkipSerialization' => true,
				'margin' => true,
				'padding' => true,
				'__experimentalDefaultControls' => array(
					'margin' => false,
					'padding' => false
				)
			),
			'shadow' => array(
				'__experimentalSkipSerialization' => true
			)
		),
		'example' => array(
			
		),
		'attributes' => array(
			'listTitle' => array(
				'type' => 'string'
			),
			'listDesc' => array(
				'type' => 'string'
			),
			'resMode' => array(
				'type' => 'string',
				'default' => 'Desktop'
			),
			'blockStyle' => array(
				'type' => 'object'
			),
			'tagName' => array(
				'type' => 'string',
				'default' => 'div'
			),
			'iconName' => array(
				'type' => 'string',
				'default' => 'wordpress'
			),
			'customSvgCode' => array(
				'type' => 'string'
			),
			'iconType' => array(
				'type' => 'string',
				'default' => 'fill'
			),
			'strokeWidth' => array(
				'type' => 'number',
				'default' => 1.2
			),
			'sizes' => array(
				'type' => 'object',
				'default' => array(
					'Desktop' => 20
				)
			),
			'iconSize' => array(
				'type' => 'number',
				'default' => 20
			),
			'textColor' => array(
				'type' => 'string',
				'default' => 'mty-white'
			),
			'backgroundColor' => array(
				'type' => 'string',
				'default' => 'mty-goblin'
			),
			'width' => array(
				'type' => 'number'
			),
			'justifyContent' => array(
				'type' => 'string'
			),
			'href' => array(
				'type' => 'string'
			),
			'linkTarget' => array(
				'type' => 'string'
			),
			'linkRel' => array(
				'type' => 'string'
			),
			'showTitle' => array(
				'type' => 'boolean',
				'default' => false
			),
			'heading' => array(
				'type' => 'string'
			),
			'headingTag' => array(
				'type' => 'string',
				'default' => 'p'
			),
			'listGap' => array(
				'type' => 'string'
			),
			'titleColor' => array(
				'type' => 'string'
			),
			'titleSize' => array(
				'type' => 'string'
			),
			'titleFontFamily' => array(
				'type' => 'string'
			),
			'iconVerticalAlign' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'iconPosition' => array(
				'type' => 'string',
				'default' => 'before'
			),
			'keepAspectRatio' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'textdomain' => 'mty',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'rating' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/rating',
		'version' => '1.0.0',
		'title' => 'Rating',
		'category' => 'mty',
		'description' => 'SVG-based star rating system with customizable values.',
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'spacing' => array(
				'margin' => true,
				'padding' => true,
				'__experimentalDefaultControls' => array(
					'margin' => false,
					'padding' => false
				)
			)
		),
		'attributes' => array(
			'blockStyle' => array(
				'type' => 'object'
			),
			'totalRating' => array(
				'type' => 'number',
				'default' => 5
			),
			'enableRating' => array(
				'type' => 'boolean',
				'default' => false
			),
			'ratingText' => array(
				'type' => 'string',
				'default' => '4.5'
			),
			'nrPos' => array(
				'type' => 'string',
				'default' => 'nr_right'
			),
			'ratingSize' => array(
				'type' => 'number'
			),
			'ratingNsize' => array(
				'type' => 'number'
			),
			'ratingColor' => array(
				'type' => 'string'
			),
			'nuRatColor' => array(
				'type' => 'string'
			),
			'alignment' => array(
				'type' => 'string',
				'default' => ''
			)
		),
		'textdomain' => 'mty',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'social-share' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/social-share',
		'version' => '0.0.5',
		'title' => 'Social Share',
		'category' => 'mty',
		'icon' => 'share',
		'description' => 'Add social sharing buttons for the current post.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'spacing' => array(
				'__experimentalSkipSerialization' => true,
				'margin' => true,
				'padding' => true,
				'__experimentalDefaultControls' => array(
					'margin' => false,
					'padding' => false
				)
			)
		),
		'attributes' => array(
			'showCopyLink' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showLinkedIn' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showTwitter' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showFacebook' => array(
				'type' => 'boolean',
				'default' => true
			),
			'iconSize' => array(
				'type' => 'string',
				'default' => '24px'
			),
			'gap' => array(
				'type' => 'string',
				'default' => '12px'
			),
			'iconColor' => array(
				'type' => 'string'
			),
			'iconBgColor' => array(
				'type' => 'string'
			),
			'iconRadius' => array(
				'type' => 'string'
			),
			'iconPadding' => array(
				'type' => 'string'
			),
			'blockStyle' => array(
				'type' => 'object'
			),
			'justifyContent' => array(
				'type' => 'string'
			)
		),
		'textdomain' => 'mty',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js',
		'render' => 'file:./render.php'
	),
	'timeline' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/timeline',
		'version' => '0.4.0',
		'title' => 'Timeline',
		'category' => 'mty',
		'description' => 'A row of steps joined by one line, each marked by a square dot, with your own blocks below. The highlighted step can colour its dot only, or its dot and line.',
		'allowedBlocks' => array(
			'mty/timeline-step'
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'spacing' => array(
				'margin' => true,
				'padding' => true
			)
		),
		'attributes' => array(
			'steps' => array(
				'type' => 'number',
				'default' => 4
			),
			'columns' => array(
				'type' => 'number',
				'default' => 4
			),
			'color' => array(
				'type' => 'string',
				'default' => ''
			),
			'activeColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'highlightLine' => array(
				'type' => 'boolean',
				'default' => false
			),
			'activeLineColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'gap' => array(
				'type' => 'number',
				'default' => 40
			),
			'contentGap' => array(
				'type' => 'number',
				'default' => 40
			),
			'highlightOnHover' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'example' => array(
			
		),
		'textdomain' => 'mty',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'timeline-step' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/timeline-step',
		'version' => '0.2.0',
		'title' => 'Timeline Step',
		'category' => 'mty',
		'description' => 'One step of a Timeline: the dot and the line to the next step, with any blocks below.',
		'parent' => array(
			'mty/timeline'
		),
		'supports' => array(
			'html' => false,
			'reusable' => false
		),
		'attributes' => array(
			'isActive' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'textdomain' => 'mty',
		'editorScript' => 'file:./index.js'
	),
	'group-full-height' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/group-full-height-extension',
		'version' => '0.0.5',
		'title' => 'Group Force Full Height Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'group-global-hover' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/group-global-hover-extension',
		'version' => '0.0.5',
		'title' => 'Group Global Hover Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'group-overlay-bg' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/group-overlay-bg-extension',
		'version' => '0.0.5',
		'title' => 'Group Overlay Background Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'highlight' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/highlight-extension',
		'version' => '0.1.0',
		'title' => 'Highlight Format Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'hover-color' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/hover-color-extension',
		'version' => '0.0.5',
		'title' => 'Hover Color Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'iconic-button' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/iconic-button-extension',
		'title' => 'Iconic Button Extension',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'image-figure-frame' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/image-figure-frame-extension',
		'version' => '0.1.0',
		'title' => 'Image Figure Frame Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'kadence-featured-bg' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/kadence-featured-bg-extension',
		'version' => '0.0.5',
		'title' => 'Kadence Featured Image Background Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'kadence-step-dot' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/kadence-step-dot-extension',
		'version' => '0.1.1',
		'title' => 'Kadence Column Step Dot Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'pointer' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/pointer-extension',
		'version' => '0.1.0',
		'title' => 'Pointer Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'team' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/team-extension',
		'version' => '0.1.0',
		'title' => 'Team Extension',
		'editorScript' => 'file:./index.js'
	),
	'text-max-width' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'mty/text-max-width-extension',
		'version' => '0.0.5',
		'title' => 'Text Max Width Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	)
);
