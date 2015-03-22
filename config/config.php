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
 * BACK END MODULES
 */
$GLOBALS['BE_MOD']['content']['zad_albo'] = array(
	'tables'		   =>	array('tl_zad_albo', 'tl_zad_albo_category'),
  'icon'			   =>	'system/modules/zad_albo/assets/icon.gif'
);


/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['zad_albo']['zad_albo_manager'] = 'ModuleZadAlboManager';
$GLOBALS['FE_MOD']['zad_albo']['zad_albo_reader'] = 'ModuleZadAlboReader';
$GLOBALS['FE_MOD']['zad_albo']['zad_albo_archivereader'] = 'ModuleZadAlboArchiveReader';


/**
 * CRON JOBS
 */
$GLOBALS['TL_CRON']['daily'][] = array('ZadAlbo', 'cronJobDaily');

