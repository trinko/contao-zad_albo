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
 * Table tl_zad_albo_category
 */
$GLOBALS['TL_DCA']['tl_zad_albo_category'] = array(
	// Configuration
	'config' => array(
		'dataContainer'                 => 'Table',
    'ptable'                        => 'tl_zad_albo',
		'enableVersioning'              => true,
		'sql' => array(
      'keys'                        => array('id'=>'primary', 'pid'=>'index')
    )
	),
	// Listing
	'list' => array(
		'sorting' => array(
			'mode'                        => 4,
			'fields'                      => array('sorting'),
			'headerFields'                => array('name','manager','active'),
			'panelLayout'                 => 'search,limit',
			'child_record_callback'       => array('tl_zad_albo_category', 'listCategories')
		),
		'global_operations' => array(
			'all' => array(
				'label'                     => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                      => 'act=select',
				'class'                     => 'header_edit_all',
				'attributes'                => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array(
			'edit' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['edit'],
				'href'                      => 'act=edit',
				'icon'                      => 'edit.gif'
			),
			'copy' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['copy'],
				'href'                      => 'act=copy',
				'icon'                      => 'copy.gif'
			),
			'cut' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['cut'],
				'href'                      => 'act=paste&amp;mode=cut',
				'icon'                      => 'cut.gif'
			),
			'delete' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['delete'],
				'href'                      => 'act=delete',
				'icon'                      => 'delete.gif',
				'attributes'                => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['show'],
				'href'                      => 'act=show',
				'icon'                      => 'show.gif'
			)
		)
	),
	// Palettes
	'palettes' => array(
		'__selector__'                  => array(),
		'default'                       => '{settings_legend},name,enableAttach,enablePdf,endDate,unpublishDate,showRefDate,showRefNumber;'
	),
	// Subpalettes
	'subpalettes' => array(
	),
	// Fields
	'fields' => array(
		'id' => array(
			'sql'                         => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array(
			'foreignKey'                  => 'tl_zad_albo.name',
			'sql'                         => "int(10) unsigned NOT NULL default '0'",
			'relation'                    => array('type'=>'belongsTo', 'load'=>'eager')
		),
    'sorting' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
    'tstamp' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
  	'name' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['name'],
			'search'                      => true,
			'exclude'                     => true,
			'inputType'                   => 'text',
			'eval'                        => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'long'),
			'sql'                         => "varchar(255) NOT NULL default ''"
		),
		'enableAttach' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['enableAttach'],
			'exclude'                     => true,
			'inputType'                   => 'checkbox',
			'eval'                        => array('tl_class'=>'clr w50'),
			'sql'                         => "char(1) NOT NULL default ''"
		),
		'enablePdf' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['enablePdf'],
			'exclude'                     => true,
			'inputType'                   => 'checkbox',
			'eval'                        => array('tl_class'=>'w50'),
			'sql'                         => "char(1) NOT NULL default ''"
		),
		'endDate' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['endDate'],
			'exclude'                     => true,
			'inputType'                   => 'select',
		  'default'                     => 'ed_0',
		  'options'                     => array('ed_0', 'ed_1', 'ed_2', 'ed_3', 'ed_6', 'ed_12', 'ed_24', 'ed_36'),
			'reference'                   => &$GLOBALS['TL_LANG']['tl_zad_albo_category'],
			'eval'                        => array('mandatory'=>true, 'tl_class'=>'clr w50'),
			'sql'                         => "varchar(16) NOT NULL default ''"
		),
		'unpublishDate' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['unpublishDate'],
			'exclude'                     => true,
			'inputType'                   => 'select',
		  'default'                     => 'ud_0',
		  'options'                     => array('ud_0', 'ud_1', 'ud_2', 'ud_3', 'ud_4', 'ud_5'),
			'reference'                   => &$GLOBALS['TL_LANG']['tl_zad_albo_category'],
			'eval'                        => array('mandatory'=>true, 'tl_class'=>'w50'),
			'sql'                         => "varchar(16) NOT NULL default ''"
		),
    'showRefDate' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['showRefDate'],
			'exclude'                     => true,
			'inputType'                   => 'checkbox',
			'eval'                        => array('tl_class'=>'clr w50'),
			'sql'                         => "char(1) NOT NULL default ''"
		),
		'showRefNumber' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo_category']['showRefNumber'],
			'exclude'                     => true,
			'inputType'                   => 'checkbox',
			'eval'                        => array('tl_class'=>'w50'),
			'sql'                         => "char(1) NOT NULL default ''"
		)
	)
);


/**
 * Class tl_zad_albo_category
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @copyright Antonello Dessì 2015
 * @author    Antonello Dessì
 * @package   zad_albo
 */
class tl_zad_albo_category extends Backend {

	/**
	 * List categories
	 *
	 * @param array $row  The table row
	 *
	 * @return string  Html text for a style
	 */
	public function listCategories($row) {
    // return string to show
    return
      '<div style="float:left;">' . $row['name'] . "</div>\n";
	}

}

