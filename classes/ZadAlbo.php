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
 * Class ZadAlbo
 * This class is used by the cron job.
 *
 * @copyright  Antonello Dessì 2015
 * @author     Antonello Dessì
 * @package    zad_albo
 */
class ZadAlbo extends \Backend {

	/**
	 * Function used by the dayly cron job.
 	 */
	public function cronJobDaily() {
    // get date
    $now = time();
    // get documents to archive
    $sql = "SELECT d.id
            FROM tl_zad_albo AS a, tl_zad_albo_category AS c, tl_zad_albo_document AS d
            WHERE c.pid=a.id AND d.pid=c.id
            AND a.active='1'
            AND d.state='PUBLISHED'
            AND d.endDate<=$now";
    $docs = $this->Database->execute($sql);
    if ($docs->numRows > 0) {
      // archive documents
      $ids = array();
      while ($docs->next()) {
        $ids[] = $docs->id;
      }
      $ids_txt = implode(',', $ids);
      $sql = "UPDATE tl_zad_albo_document SET state='ARCHIVED' WHERE id in ($ids_txt)";
      $res = $this->Database->execute($sql);
      // add a log entry
      $this->log(sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['log_archived'], $ids_txt), __METHOD__, TL_CRON);
    }
  }

}

