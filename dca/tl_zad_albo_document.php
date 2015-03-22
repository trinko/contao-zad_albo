<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   zad_albo
 * @author    Antonello Dessì
 * @license   LGPL
 * @copyright Antonello Dessì 2015
 */


/**
 * Table tl_zad_albo_document
 */
$GLOBALS['TL_DCA']['tl_zad_albo_document'] = array(
	// Configuration
	'config' => array(
		'dataContainer'                 => 'Table',
		'sql' => array(
      'keys'                        => array('id'=>'primary', 'pid'=>'index')
    )
	),
	// Fields
	'fields' => array(
		'id' => array(
			'sql'                         => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
		'document' => array(
			'sql'                         => "binary(16) NULL"
		),
		'documentName' => array(
			'sql'                         => "varchar(255) NOT NULL default ''"
		),
		'attach' => array(
			'sql'                         => "blob NULL"
		),
		'attachNames' => array(
			'sql'                         => "blob NULL"
		),
		'number' => array(
			'sql'                         => "varchar(255) NOT NULL default ''"
		),
		'subject' => array(
			'sql'                         => "varchar(255) NOT NULL default ''"
		),
		'referenceNumber' => array(
			'sql'                         => "varchar(255) NOT NULL default ''"
		),
		'referenceDate' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
		'startDate' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
		'endDate' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
		'unpublishDate' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
		'state' => array(
			'sql'                         => "varchar(16) NOT NULL default ''"
		),
		'sentBy' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
		'canceled' => array(
			'sql'                         => "char(1) NOT NULL default ''"
		)
	)
);


