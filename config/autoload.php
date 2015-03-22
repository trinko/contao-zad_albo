<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package Zad_albo
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'zad_albo',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'zad_albo\ZadAlbo'                    => 'system/modules/zad_albo/classes/ZadAlbo.php',

	// Models
	'zad_albo\ZadAlboCategoryModel'       => 'system/modules/zad_albo/models/ZadAlboCategoryModel.php',
	'zad_albo\ZadAlboDocumentModel'       => 'system/modules/zad_albo/models/ZadAlboDocumentModel.php',
	'zad_albo\ZadAlboModel'               => 'system/modules/zad_albo/models/ZadAlboModel.php',

	// Modules
	'zad_albo\ModuleZadAlboArchiveReader' => 'system/modules/zad_albo/modules/ModuleZadAlboArchiveReader.php',
	'zad_albo\ModuleZadAlboManager'       => 'system/modules/zad_albo/modules/ModuleZadAlboManager.php',
	'zad_albo\ModuleZadAlboReader'        => 'system/modules/zad_albo/modules/ModuleZadAlboReader.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'zadaa_list'    => 'system/modules/zad_albo/templates',
	'zadaa_message' => 'system/modules/zad_albo/templates',
	'zadaa_show'    => 'system/modules/zad_albo/templates',
	'zadam_confirm' => 'system/modules/zad_albo/templates',
	'zadam_edit'    => 'system/modules/zad_albo/templates',
	'zadam_list'    => 'system/modules/zad_albo/templates',
	'zadam_message' => 'system/modules/zad_albo/templates',
	'zadam_show'    => 'system/modules/zad_albo/templates',
	'zadar_list'    => 'system/modules/zad_albo/templates',
	'zadar_message' => 'system/modules/zad_albo/templates',
	'zadar_show'    => 'system/modules/zad_albo/templates',
));
