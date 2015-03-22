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
 * Class ZadAlboDocumentModel
 *
 * @copyright  Antonello Dessì 2015
 * @author     Antonello Dessì
 * @package    zad_albo
 */
class ZadAlboDocumentModel extends \Model {

	/**
	 * Name of the table
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_zad_albo_document';


  /**
	 * Count documents in a category, including only DRAFT/PUBLISHED documents
	 *
	 * @param int $catId  The category ID
	 *
	 * @return int  The number of documents
	 */
	public static function countDocuments($catId) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT COUNT(*) AS cnt '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.pid='.$catId.' AND t.state IN (\'DRAFT\',\'PUBLISHED\')';
    // execute query
    $res = $db->execute($sql);
		return $res->cnt;
  }

  /**
	 * Get all documents in a category, including only DRAFT/PUBLISHED documents
	 *
	 * @param int $catId  The category ID
	 * @param int $offset  Offset number of items, or null if not used
	 * @param int $limit  Max number of items, or null if not used
	 *
	 * @return \Model\Collection  A collection of document models
	 */
	public static function findDocuments($catId, $offset, $limit) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT t.* '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.pid='.$catId.' AND t.state IN (\'DRAFT\',\'PUBLISHED\') '.
      'ORDER BY t.state ASC,t.tstamp DESC';
    // limits
    if ($limit > 0) {
      $sql .= ' LIMIT '.$offset.','.$limit;
    }
    // execute query
    $res = $db->execute($sql);
		return \Model\Collection::createFromDbResult($res, static::$strTable);
  }

  /**
	 * Get a document by his own ID
	 *
	 * @param int $catId  The category ID (to check the ownership)
	 * @param int $docId  The document ID
	 * @param string $state  The document state (null for any state)
	 *
	 * @return \Model\Collection|Null  A document model or null if document not exists
	 */
	public static function getDocument($catId, $docId, $state=null) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT t.* '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.pid='.$catId.' AND t.id='.$docId;
    // check state
    if ($state) {
      $sql .= ' AND t.state=\''.$state.'\'';
    }
    // execute query
    $res = $db->execute($sql);
		if ($res->numRows < 1) {
			return null;
		} else {
  		return \Model\Collection::createFromDbResult($res, static::$strTable);
    }
  }

	/**
	 * Find document by file UUID
	 *
	 * @param int $catId  The category ID
	 * @param string $uuid  UUID of the file
	 * @param string $state  The document state (null for any state)
	 *
	 * @return \Model\Collection|Null  A document model or null if document not exists
	 */
	public static function getDocumentByFile($catId, $uuid, $state=null) {
		$db = \Database::getInstance();
    $hex = bin2hex(\String::uuidToBin($uuid));
    // set query
    $sql = 'SELECT t.* '.
           'FROM '.static::$strTable.' AS t '.
           'WHERE t.pid='.$catId.' '.
           'AND (t.document=unhex(\''.$hex.'\') OR t.attach LIKE concat(\'%;s:16:"\',unhex(\''.$hex.'\'),\'";%\')) ';
    // check state
    if ($state) {
      $sql .= ' AND t.state=\''.$state.'\'';
    }
    // execute query
    $res = $db->query($sql);
		if ($res->numRows < 1) {
			return null;
		} else {
		  return \Model\Collection::createFromDbResult($res, static::$strTable);
    }
	}

  /**
	 * Count published documents in one category or more, filtered by a list of words
	 *
	 * @param int/array $catId  The category ID, or an array of category IDs
	 * @param array $terms  List of words to search in subject
	 *
	 * @return int  The number of published documents
	 */
	public static function countPublishedDocuments($catId, $terms) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT COUNT(*) AS cnt '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.state=\'PUBLISHED\'';
    if (is_array($catId)) {
      // a list of categories
      $sql .= ' AND t.pid IN (' . implode(',', $catId) . ')';
    } else {
      // only one category
      $sql .= ' AND t.pid=' . intval($catId);
    }
    if (!empty($terms) && count($terms) > 0) {
      // filter by a list of words
      $sql .= ' AND t.subject LIKE \'%' . implode('%', $terms) . '%\'';
    }
    // execute query
    $res = $db->query($sql);
		return $res->cnt;
  }

  /**
	 * Get all published documents in one category or more, filtered by a list of words
	 *
	 * @param int/array $catId  The category ID, or an array of category IDs
	 * @param array $terms  List of words to search in subject
	 * @param int $offset  Offset number of items, or null if not used
	 * @param int $limit  Max number of items, or null if not used
	 *
	 * @return \Model\Collection  A collection of document models
	 */
	public static function findPublishedDocuments($catId, $terms, $offset, $limit) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT t.* '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.state =\'PUBLISHED\'';
    if (is_array($catId)) {
      // a list of categories
      $sql .= ' AND t.pid IN (' . implode(',', $catId) . ')';
    } else {
      // only one category
      $sql .= ' AND t.pid=' . intval($catId);
    }
    if (!empty($terms) && count($terms) > 0) {
      // filter by a list of words
      $sql .= ' AND t.subject LIKE \'%' . implode('%', $terms) . '%\'';
    }
    $sql .= ' ORDER BY CONCAT(RIGHT(t.number,4),LPAD(LEFT(t.number,LENGTH(t.number)-5),10,\'0\')) DESC';
    // limits
    if ($limit > 0) {
      $sql .= ' LIMIT '.$offset.','.$limit;
    }
    // execute query
    $res = $db->query($sql);
		return \Model\Collection::createFromDbResult($res, static::$strTable);
  }

  /**
	 * Get a published document by his own ID
	 *
	 * @param int $catId  The category ID
	 * @param int $docId  The document ID
	 *
	 * @return \Model\Collection|Null  A document model or null if document not exists
	 */
	public static function getPublishedDocument($catId, $docId) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT c.*,t.* '.
      'FROM '.static::$strTable.' AS t, tl_zad_albo_category AS c '.
      'WHERE t.pid=c.id AND c.id='.$catId.' AND t.id='.$docId.' '.
      'AND t.state=\'PUBLISHED\' AND t.canceled=\'\'';
    // execute query
    $res = $db->execute($sql);
		if ($res->numRows < 1) {
			return null;
		} else {
  		return \Model\Collection::createFromDbResult($res, static::$strTable);
    }
  }

	/**
	 * Find a published document by file UUID
	 *
	 * @param int $catId  The category ID
	 * @param string $uuid  UUID of the file
	 *
	 * @return \Model\Collection|Null  A document model or null if document not exists
	 */
	public static function getPublishedDocumentByFile($catId, $uuid) {
		$db = \Database::getInstance();
    $hex = bin2hex(\String::uuidToBin($uuid));
    // set query
    $sql = 'SELECT c.enableAttach,t.* '.
           'FROM '.static::$strTable.' AS t, tl_zad_albo_category AS c '.
           'WHERE t.pid=c.id AND c.id='.$catId.' '.
           'AND (t.document=unhex(\''.$hex.'\') OR t.attach LIKE concat(\'%;s:16:"\',unhex(\''.$hex.'\'),\'";%\')) ';
           'AND t.state=\'PUBLISHED\' AND t.canceled=\'\'';
    // execute query
    $res = $db->query($sql);
		if ($res->numRows < 1) {
			return null;
		} else {
		  return \Model\Collection::createFromDbResult($res, static::$strTable);
    }
	}

  /**
	 * Count archived documents in one category or more, filtered by a list of words
	 *
	 * @param int/array $catId  The category ID, or an array of category IDs
	 * @param array $terms  List of words to search in subject
	 *
	 * @return int  The number of archived documents
	 */
	public static function countArchivedDocuments($catId, $terms) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT COUNT(*) AS cnt '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.state=\'ARCHIVED\'';
    if (is_array($catId)) {
      // a list of categories
      $sql .= ' AND t.pid IN (' . implode(',', $catId) . ')';
    } else {
      // only one category
      $sql .= ' AND t.pid=' . intval($catId);
    }
    if (!empty($terms) && count($terms) > 0) {
      // filter by a list of words
      $sql .= ' AND t.subject LIKE \'%' . implode('%', $terms) . '%\'';
    }
    // execute query
    $res = $db->query($sql);
		return $res->cnt;
  }

  /**
	 * Get all archived documents in one category or more, filtered by a list of words
	 *
	 * @param int/array $catId  The category ID, or an array of category IDs
	 * @param array $terms  List of words to search in subject
	 * @param int $offset  Offset number of items, or null if not used
	 * @param int $limit  Max number of items, or null if not used
	 *
	 * @return \Model\Collection  A collection of document models
	 */
	public static function findArchivedDocuments($catId, $terms, $offset, $limit) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT t.* '.
      'FROM '.static::$strTable.' AS t '.
      'WHERE t.state =\'ARCHIVED\'';
    if (is_array($catId)) {
      // a list of categories
      $sql .= ' AND t.pid IN (' . implode(',', $catId) . ')';
    } else {
      // only one category
      $sql .= ' AND t.pid=' . intval($catId);
    }
    if (!empty($terms) && count($terms) > 0) {
      // filter by a list of words
      $sql .= ' AND t.subject LIKE \'%' . implode('%', $terms) . '%\'';
    }
    $sql .= ' ORDER BY CONCAT(RIGHT(t.number,4),LPAD(LEFT(t.number,LENGTH(t.number)-5),10,\'0\')) DESC';
    // limits
    if ($limit > 0) {
      $sql .= ' LIMIT '.$offset.','.$limit;
    }
    // execute query
    $res = $db->query($sql);
		return \Model\Collection::createFromDbResult($res, static::$strTable);
  }

  /**
	 * Get an archived document by his own ID
	 *
	 * @param int $catId  The category ID
	 * @param int $docId  The document ID
	 *
	 * @return \Model\Collection|Null  A document model or null if document not exists
	 */
	public static function getArchivedDocument($catId, $docId) {
    // create db instance
		$db = \Database::getInstance();
    // query sql
    $sql =
      'SELECT c.*,t.* '.
      'FROM '.static::$strTable.' AS t, tl_zad_albo_category AS c '.
      'WHERE t.pid=c.id AND c.id='.$catId.' AND t.id='.$docId.' '.
      'AND t.state=\'ARCHIVED\' AND t.canceled=\'\'';
    // execute query
    $res = $db->execute($sql);
		if ($res->numRows < 1) {
			return null;
		} else {
  		return \Model\Collection::createFromDbResult($res, static::$strTable);
    }
  }

	/**
	 * Find an archived document by file UUID
	 *
	 * @param int $catId  The category ID
	 * @param string $uuid  UUID of the file
	 *
	 * @return \Model\Collection|Null  A document model or null if document not exists
	 */
	public static function getArchivedDocumentByFile($catId, $uuid) {
		$db = \Database::getInstance();
    $hex = bin2hex(\String::uuidToBin($uuid));
    // set query
    $sql = 'SELECT c.enableAttach,t.* '.
           'FROM '.static::$strTable.' AS t, tl_zad_albo_category AS c '.
           'WHERE t.pid=c.id AND c.id='.$catId.' '.
           'AND (t.document=unhex(\''.$hex.'\') OR t.attach LIKE concat(\'%;s:16:"\',unhex(\''.$hex.'\'),\'";%\')) ';
           'AND t.state=\'ARCHIVED\' AND t.canceled=\'\'';
    // execute query
    $res = $db->query($sql);
		if ($res->numRows < 1) {
			return null;
		} else {
		  return \Model\Collection::createFromDbResult($res, static::$strTable);
    }
	}

}

