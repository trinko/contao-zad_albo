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
 * Table tl_module
 */

// Configuration
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] =
  array('tl_module_zad_albo', 'config');

// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes']['zad_albo_manager'] = '{title_legend},name,headline,type;{config_legend},zad_albo,perPage;{expert_legend:hide},cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['zad_albo_reader'] = '{title_legend},name,headline,type;{config_legend},zad_albo,perPage;{expert_legend:hide},cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['zad_albo_archivereader'] = '{title_legend},name,headline,type;{config_legend},zad_albo,perPage;{expert_legend:hide},cssID,space';

// Fields
$GLOBALS['TL_DCA']['tl_module']['fields']['zad_albo'] = array(
  'label'                       => &$GLOBALS['TL_LANG']['tl_module']['zad_albo'],
  'exclude'                     => true,
  'inputType'                   => 'select',
	'options_callback'            => array('tl_module_zad_albo', 'getAlbos'),
  'eval'                        => array('mandatory'=>true, 'submitOnChange'=>true, 'tl_class'=>'clr'),
	'sql'                         => "int(10) unsigned NOT NULL default '0'"
);


/**
 * Class tl_module_zad_albo
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @copyright Antonello Dessì 2015
 * @author    Antonello Dessì
 * @package   zad_albo
 */
class tl_module_zad_albo extends Backend {

	/**
	 * Dynamic fields configuration for the module
	 *
	 * @param \DataContainer $dc  The data container for the table.
	 */
	public function config($dc) {
		if ($_POST || (Input::get('act') != 'edit' && Input::get('act') != 'show')) {
      // not in edit mode
			return;
		}
		$module = ModuleModel::findByPk($dc->id);
		if ($module === null) {
      // record not found
			return;
		}
    if ($module->type == 'zad_albo_manager') {
      // Albo Manager: module configuration
      Message::addInfo($GLOBALS['TL_LANG']['tl_module']['wrn_zad_albo_js']);
    } elseif ($module->type == 'zad_albo_reader') {
      // Albo Reader: module configuration
    } elseif ($module->type == 'zad_albo_archivereader') {
      // Albo Archive Reader: module configuration
    }
  }

	/**
	 * Return all albos
	 *
	 * @param \DataContainer $dc  The data container for the table.
	 *
	 * @return array  A list with all albos
	 */
	public function getAlbos($dc) {
    $list = array();
		$albos = $this->Database->prepare("SELECT id,name FROM tl_zad_albo WHERE active=? ORDER BY name")
					                  ->execute('1');
    while ($albos->next()) {
      $list[$albos->id] = $albos->name;
    }
    return $list;
	}

}

