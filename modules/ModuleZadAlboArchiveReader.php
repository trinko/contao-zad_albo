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
 * Class ModuleZadAlboArchiveReader
 *
 * Front end module "Albo Archive Reader".
 *
 * @copyright Antonello Dessì 2015
 * @author    Antonello Dessì
 * @package   zad_albo
 */
class ModuleZadAlboArchiveReader extends \Module {

	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'zadaa_message';

	/**
	 * Albo's table data
	 *
	 * @var \ZadAlboModel
	 */
	protected $albo = null;

	/**
	 * The base URL for the page
	 *
	 * @var string
	 */
	protected $baseUrl = null;


	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate() {
		if (TL_MODE == 'BE') {
			$template = new \BackendTemplate('be_wildcard');
      $template->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['zad_albo_archivereader'][0]) . ' ###';
			$template->title = $this->headline;
			$template->id = $this->id;
			$template->link = $this->name;
			$template->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
      return $template->parse();
		}
		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile() {
    // set base url
    $this->baseUrl = $this->createBaseUrl();
    // get data
    $this->albo = \ZadAlboModel::findByPk($this->zad_albo);
    if ($this->albo === null || !$this->albo->active) {
      // no data: exit without any output
      $this->errorMessage(null);
      return;
    }
    // get action info
    $action = \Input::get('zaA');
    $cid = intval(\Input::get('zaC'));
    switch ($action) {
      case 'show':  // show document details
        $did = intval(\Input::get('zaD'));
        $this->documentShow($cid, $did);
        break;
      case 'download':  // download a file
        $fid = \Input::get('zaF');
        $this->fileDownload($cid, $fid);
        break;
      default:  // list documents
        $pid = intval(\Input::get('zaP'));
        $this->documentList($cid, $pid);
        break;
    }
	}

	/**
	 * Show the preview of a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the published document to cancel
	 */
	protected function documentShow($category, $id) {
    // set template
    $this->Template = new \FrontendTemplate('zadaa_show');
    // get document
    $doc = \ZadAlboDocumentModel::getArchivedDocument($category, $id);
    if ($doc === null) {
      // error, invalid document
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
      return;
    }
    // get data
    $data = array();
    // number
    $data['number'] = $doc->number;
    // category
    $data['category'] = $doc->name;
    if ($doc->showRefNumber) {
      // set reference number
      $data['referenceNumber'] = $doc->referenceNumber;
    }
    if ($doc->showRefDate) {
      // format reference date
      $date = new \Date($doc->referenceDate);
      $data['referenceDate'] = $date->date;
    }
    // start date
    $date = new \Date($doc->startDate);
    $data['startDate'] = $date->date;
    // end date
    $date = new \Date($doc->endDate);
    $data['endDate'] = $date->date;
    // unpublish date
    $date = new \Date($doc->unpublishDate);
    $data['unpublishDate'] = $date->date;
    // document
    $param = array();
    $param['zaA'] = 'download';
    $param['zaC'] = $category;
    $param['zaF'] = \String::binToUuid($doc->document);
    $data['href_document'] = $this->createUrl($param, $this->baseUrl);
    $data['document'] = $doc->documentName;
    // attach files
    $attach = array();
    if ($doc->enableAttach) {
      $attaches = unserialize($doc->attach);
      $attachnames = unserialize($doc->attachNames);
      foreach ($attaches as $katt=>$att) {
        $param['zaF'] = \String::binToUuid($att);
        $href = $this->createUrl($param, $this->baseUrl);
        $attach[] = array(
          'href' => $href,
          'attach' => $attachnames[$katt]);
      }
    }
    // set template vars
    $this->Template->header = $doc->subject;
    $this->Template->data = $data;
    $this->Template->attach = $attach;
    $this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
    $this->Template->lbl_number = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_number'];
    $this->Template->lbl_category = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_category'];
    $this->Template->lbl_referenceNumber = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceNumber'];
    $this->Template->lbl_referenceDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceDate'];
    $this->Template->lbl_startDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_startDate'];
    $this->Template->lbl_endDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_endDate'];
    $this->Template->lbl_unpublishDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_unpublishDate'];
    $this->Template->lbl_document = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_document'];
    $this->Template->lbl_attach = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_attach'];
  }

  /**
	 * Download a file
	 *
	 * @param int $category  Category identifier
	 * @param string $uuid  The file uuid
	 */
	protected function fileDownload($category, $uuid) {
    // get file
    $file = \FilesModel::findByUuid($uuid);
    if ($file === null) {
      // error no file
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nofile']);
      return;
    }
    $doc = \ZadAlboDocumentModel::getArchivedDocumentByFile($category, $uuid);
    if ($doc === null) {
      // error no file
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
      return;
    }
    // get name
    $filename = null;
    if ($doc->document == \String::uuidToBin($uuid)) {
      // get document file name
      $filename = $doc->documentName;
    } elseif ($doc->enableAttach) {
      // get attachment file name
      $attaches = unserialize($doc->attach);
      $attachnames = unserialize($doc->attachNames);
      foreach ($attaches as $katt=>$att) {
        if ($att == \String::uuidToBin($uuid)) {
          // attach found
          $filename = $attachnames[$katt];
          break;
        }
      }
    }
    if ($filename === null) {
      // error, document not found
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nofile']);
      return;
    }
    // send file end exit
    $fileobj = new \File($file->path, true);
    $fileobj->sendToBrowser($filename);
  }

	/**
	 * Show document list
	 *
	 * @param int $category  Category identifier
	 * @param int $page  Page number
	 */
	protected function documentList($category=0, $page=0) {
    // set template
    $this->Template = new \FrontendTemplate('zadaa_list');
    // get search subject
    $subject = trim(\Input::get('zaS'));
    $subject_terms = preg_split('/[^a-z0-9àèìòùé]+/i', $subject);
    foreach ($subject_terms as $kterm=>$term) {
      if ($term == '') {
        unset($subject_terms[$kterm]);
      }
    }
    $subject = implode(' ', $subject_terms);
    // get categories
    $categories = array();
    $category_ids = array();
    $cat = \ZadAlboCategoryModel::listCategories($this->albo->id);
    while ($cat->next()) {
      $categories[] = array(
        'id' => $cat->id,
        'name' => $cat->name);
      $category_ids[] = $cat->id;
    }
    // pagination
    $limit = null;
    $offset = null;
    $total = \ZadAlboDocumentModel::countArchivedDocuments(($category == 0) ? $category_ids : $category, $subject_terms);
		if ($this->perPage > 0 && $total > $this->perPage) {
      // adjust page number
      if ($page < 1) {
        // first page
        $page = 1;
      } elseif ($page > ceil($total / $this->perPage)) {
        // last page
        $page = ceil($total / $this->perPage);
      }
			// set limit and offset
			$limit = $this->perPage;
			$offset = ($page - 1) * $this->perPage;
			if ($offset + $limit > $total) {
				$limit = $total - $offset;
			}
			// add the pagination menu
			$pagination = new \Pagination($total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], 'zaP');
			$this->Template->pagination = $pagination->generate("\n  ");
    }
    // get docs
    $data = array();
    $docs = \ZadAlboDocumentModel::findArchivedDocuments(($category == 0) ? $category_ids : $category, $subject_terms, $offset, $limit);
    $index = 0;
    while ($docs->next()) {
      // number
      $data[$index]['number'] = $docs->number;
      // subject
      $data[$index]['subject'] = $docs->subject;
      $param = array();
      $param['zaC'] = $docs->pid;
      $param['zaD'] = $docs->id;
      $param['zaA'] = 'show';
      $data[$index]['subject_href'] = $this->createUrl($param, $this->baseUrl);
      // publication date interval
      $datestart = new \Date($docs->startDate);
      $dateend = new \Date($docs->endDate);
      $data[$index]['pubdate'] = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_pubdtinterval'], $datestart->date, $dateend->date);
      if ($category == 0) {
        // add category name
        $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $docs->pid);
        if ($cat !== null) {
          $data[$index]['category'] = $cat->name;
          $param = array();
          $param['zaC'] = $docs->pid;
          $param['zaA'] = 'list';
          $data[$index]['category_href'] = $this->createUrl($param, $this->baseUrl);
        }
      }
      if ($docs->canceled) {
        // canceled
        $data[$index]['subject_href'] = null;
      }
      $index++;
    }
    // set other template vars
    $this->Template->category = $category;
    $this->Template->subject = $subject;
    $this->Template->categories = $categories;
    $this->Template->docs = $data;
    $this->Template->header = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_searchoptions'];
    $this->Template->wrn_nodata = $GLOBALS['TL_LANG']['tl_zad_albo']['wrn_nodata'];
    $this->Template->lbl_searchcategory = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_searchcategory'];
    $this->Template->lbl_searchanycategory = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_searchanycategory'];
    $this->Template->lbl_searchsubject = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_searchsubject'];
    $this->Template->lbl_archivedlist = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_archivedlist'];
    $this->Template->lbl_number = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_number'];
    $this->Template->lbl_subject = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_subject'];
    $this->Template->lbl_category = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_category'];
    $this->Template->lbl_pubdate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_pubdate'];
    $this->Template->lbl_pubcanceled = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_pubcanceled'];
    $this->Template->but_search = $GLOBALS['TL_LANG']['tl_zad_albo']['but_search'];
	}

	/**
	 * Show an error message and terminate
	 *
	 * @param string $message  Message to show, if null disable module
 	 */
	private function errorMessage($message) {
    $this->Template = new \FrontendTemplate('zadaa_message');
    if ($message == null) {
      // no message, disable module
      $this->Template->active = false;
    } else {
      // show message
      $this->Template->active = true;
    	$this->Template->referer = $this->baseUrl;
  		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
      $this->Template->message = $message;
    }
  }

  /**
	 * Create a base URL without some parameters
	 *
	 * @param bool $encode  True to $encode "&" in URL, False otherwise
	 *
	 * @return string  The new URL
	 */
	private function createBaseUrl($encode=true) {
    $params = array('zaA', 'zaC', 'zaP', 'zaD', 'zaF', 'zaS');
    $base = explode('?', \Environment::get('request'));
    $q = '';
    if (isset($base[1])) {
      // delete parameters
  		$queries = preg_split('/&(amp;)?/i', $base[1]);
  		foreach ($queries as $k=>$v) {
  		  $explode = explode('=', $v);
  			if (in_array($explode[0], $params)) {
  				unset($queries[$k]);
  			}
  		}
      if (!empty($queries)) {
  			$q = '?' . implode($encode ? '&amp;' : '&', $queries);
  		}
    }
    return $base[0] . $q;
  }

  /**
	 * Create a new URL with some parameters
	 *
	 * @param array $params  List of couples key=>value to be added
	 * @param string $base  The base url
	 * @param bool $encode  True to $encode "&" in URL, False otherwise
	 *
	 * @return string  The new URL
	 */
	private function createUrl($params, $base='', $encode=true) {
    if ($base == '') {
      $base = $this->createBaseUrl();
    }
    // create query list
    $queries = array();
		foreach ($params as $k=>$v) {
      $queries[] = "$k=$v";
    }
    $q = implode($encode ? '&amp;' : '&', $queries);
    return $base . ((strpos($base, '?') === false) ? '?' : ($encode ? '&amp;' : '&')) . $q;
  }

}

