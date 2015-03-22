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
 * Namespace
 */
namespace zad_albo;


/**
 * Class ZadAlboCategoryModel
 *
 * @copyright  Antonello Dessì 2015
 * @author     Antonello Dessì
 * @package    zad_albo
 */
class ZadAlboCategoryModel extends \Model {

	/**
	 * Name of the table
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_zad_albo_category';


  /**
	 * Get the list of all categories
	 *
	 * @param int $alboId  The albo ID
	 *
	 * @return \Model\Collection  A collection of category models
	 */
	public static function listCategories($alboId) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT t.id,t.name '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.pid='.$alboId.' '.
      'ORDER BY t.sorting ASC';
    // execute query
    $res = $db->execute($sql);
		return \Model\Collection::createFromDbResult($res, static::$strTable);
  }

  /**
	 * Get category by ID
	 *
	 * @param int $alboId  The albo ID
	 * @param int $catId  The category ID
	 *
	 * @return \Model\Collection|Null  A category model or null if category does not exist
	 */
	public static function getCategory($alboId, $catId) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT t.* '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.pid='.$alboId.' AND id='.$catId;
    // execute query
    $res = $db->execute($sql);
		if ($res->numRows < 1) {
			return null;
		} else {
  		return \Model\Collection::createFromDbResult($res, static::$strTable);
    }
  }

}

