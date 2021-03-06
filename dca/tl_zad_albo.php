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
 * Table tl_zad_albo
 */
$GLOBALS['TL_DCA']['tl_zad_albo'] = array(
	// Configuration
	'config' => array(
		'dataContainer'                 => 'Table',
    'ctable'                        => array('tl_zad_albo_category'),
		'enableVersioning'              => true,
		'sql' => array(
      'keys'                        => array('id' => 'primary')
    )
	),
	// Listing
	'list' => array(
		'sorting' => array(
			'mode'                        => 1,
			'fields'                      => array('name'),
			'flag'                        => 1,
			'panelLayout'                 => 'search,limit'
		),
		'label' => array(
			'fields'                      => array('name'),
			'format'                      => '%s'
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
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo']['edit'],
				'href'                      => 'table=tl_zad_albo_category',
				'icon'                      => 'edit.gif'
			),
			'editheader' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo']['editheader'],
				'href'                      => 'act=edit',
				'icon'                      => 'header.gif'
			),
			'copy' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo']['copy'],
				'href'                      => 'act=copy',
				'icon'                      => 'copy.gif'
			),
			'delete' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo']['delete'],
				'href'                      => 'act=delete',
				'icon'                      => 'delete.gif',
				'attributes'                => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo']['toggle'],
				'icon'                      => 'visible.gif',
				'attributes'                => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'           => array('tl_zad_albo', 'toggleIcon')
			),
			'show' => array(
				'label'                     => &$GLOBALS['TL_LANG']['tl_zad_albo']['show'],
				'href'                      => 'act=show',
				'icon'                      => 'show.gif'
			)
		)
	),
	// Palettes
	'palettes' => array(
		'__selector__'                  => array(),
		'default'                       => '{settings_legend},name,manager,groups;{documents_legend},dir,fileTypes;'
	),
	// Subpalettes
	'subpalettes' => array(
	),
	// Fields
	'fields' => array(
		'id' => array(
			'sql'                         => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array(
			'sql'                         => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo']['name'],
			'search'                      => true,
			'exclude'                     => true,
			'inputType'                   => 'text',
			'eval'                        => array('mandatory'=>true, 'unique'=>true, 'maxlength'=>255, 'tl_class'=>'long'),
			'sql'                         => "varchar(255) NOT NULL default ''"
		),
		'manager' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo']['manager'],
			'exclude'                     => true,
			'inputType'                   => 'select',
			'foreignKey'                  => 'tl_member_group.name',
			'eval'                        => array('mandatory'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
			'sql'                         => "int(10) unsigned NOT NULL default '0'",
			'relation'                    => array('type'=>'hasOne', 'load'=>'eager')
		),
		'groups' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo']['groups'],
			'exclude'                     => true,
			'inputType'                   => 'checkbox',
			'foreignKey'                  => 'tl_member_group.name',
			'eval'                        => array('multiple'=>true, 'tl_class'=>'w50'),
			'sql'                         => "blob NULL",
			'relation'                    => array('type'=>'hasMany', 'load'=>'eager')
		),
		'dir' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo']['dir'],
			'exclude'                     => true,
			'search'                      => true,
			'inputType'                   => 'fileTree',
			'eval'                        => array('mandatory'=>true, 'fieldType'=>'radio', 'files'=>false, 'tl_class'=>'w50'),
			'sql'                         => "binary(16) NULL"
		),
		'fileTypes' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo']['fileTypes'],
			'exclude'                     => true,
			'inputType'                   => 'text',
      'default'                     => 'odt,ods,odp,pdf,rtf,csv,doc,docx,xls,xlsx,ppt,pptx,pps,ppsx,html,htm,txt,zip,rar,7z',
			'eval'                        => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                         => "varchar(255) NOT NULL default ''"
		),
		'active' => array(
			'label'                       => &$GLOBALS['TL_LANG']['tl_zad_albo']['active'],
			'exclude'                     => true,
			'inputType'                   => 'checkbox',
			'eval'                        => array('doNotCopy'=>true),
			'sql'                         => "char(1) NOT NULL default ''"
		),
		'lastNumber' => array(
			'exclude'                     => true,
			'eval'                        => array('doNotCopy'=>true, 'hideInput'=>true, 'doNotShow'=>true),
			'sql'                         => "varchar(255) NOT NULL default ''"
		)
	)
);


/**
 * Class tl_zad_albo
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @copyright Antonello Dessì 2015
 * @author    Antonello Dessì
 * @package   zad_albo
 */
class tl_zad_albo extends Backend {

	/**
	 * Return the "toggle" button
	 *
	 * @param array $row  The table row
	 * @param string $href  Url for the button
	 * @param string $label  Label text for the button
	 * @param string $title  Title text for the button
	 * @param string $icon  Icon name for the button
	 * @param string $attributes  Other attributes for the button
	 *
	 * @return string  Html text for the button
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes) {
		if (strlen(Input::get('tid'))) {
			$this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1));
			$this->redirect($this->getReferer());
		}
		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['active'] ? '' : 1);
		if (!$row['active']) {
			$icon = 'invisible.gif';
		}
		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}

	/**
	 * Toggle visibility mode
	 *
	 * @param integer $id  Identifier of the record
	 * @param boolean $visible  True if archive is visible, False otherwise.
	 */
	public function toggleVisibility($id, $visible) {
		// update the database
		$this->Database->prepare("UPDATE tl_zad_albo SET active=? WHERE id=?")
					         ->execute(($visible ? '1' : ''), $id);
	}

}

