<?php
	$capabilities = array(
		
		'format/buttons:changeimagecontaineralignment' => array(
			'captype' => 'write',
			'contextlevel' => CONTEXT_COURSE,
			'archetypes' => array(
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
			)
		),
		
		'format/buttons:changeimagecontainernavigation' => array(
			'captype' => 'write',
			'contextlevel' => CONTEXT_COURSE,
			'archetypes' => array(
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
			)
		),
		
		'format/buttons:changeimagecontainersize' => array(
			'captype' => 'write',
			'contextlevel' => CONTEXT_COURSE,
			'archetypes' => array(
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
			)
		),
		
		'format/buttons:changeimageresizemethod' => array(
			'captype' => 'write',
			'contextlevel' => CONTEXT_COURSE,
			'archetypes' => array(
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
			)
		),
		
		'format/buttons:changeimagecontainerstyle' => array(
			'captype' => 'write',
			'contextlevel' => CONTEXT_COURSE,
			'archetypes' => array(
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
			)
		),
		
		'format/buttons:changesectiontitleoptions' => array(
			'captype' => 'write',
			'contextlevel' => CONTEXT_COURSE,
			'archetypes' => array(
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
			)
		)
	);