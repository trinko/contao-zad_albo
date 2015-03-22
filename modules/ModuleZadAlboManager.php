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
 * Class ModuleZadAlboManager
 *
 * Front end module "Albo Manager".
 *
 * @copyright Antonello Dessì 2015
 * @author    Antonello Dessì
 * @package   zad_albo
 */
class ModuleZadAlboManager extends \Module {

	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'zadam_message';

	/**
	 * Albo's table data
	 *
	 * @var \ZadAlboModel
	 */
	protected $albo = null;

	/**
	 * ID of the logged user
	 *
	 * @var int
	 */
	protected $userId = 0;

	/**
	 * True if user is an administrator for the albo
	 *
	 * @var bool
	 */
	protected $isAdmin = false;

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
      $template->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['zad_albo_manager'][0]) . ' ###';
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
    // check if a member is logged
    if (!FE_USER_LOGGED_IN) {
      // error, no member logged
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nologged']);
      return;
    }
    // check logged member
    $this->import('FrontendUser', 'User');
    $this->userId = $this->User->id;
    $groups = deserialize($this->albo->groups);
    $this->isAdmin = false;
   	if (in_array($this->albo->manager, $this->User->groups)) {
      // member is document administrator
      $this->isAdmin = true;
    } elseif (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups))) {
      // error, member not allowed
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_auth']);
      return;
    }
    // get action info
    $action = \Input::get('zaA');
    $cid = intval(\Input::get('zaC'));
    switch ($action) {
      case 'upload':  // upload files
        $did = intval(\Input::get('zaD'));
        $this->fileUpload($cid, $did);
        break;
      case 'cancel':  // cancel uploaded files
        $did = intval(\Input::get('zaD'));
        $this->fileCancel($cid, $did);
        break;
      case 'add':  // add a document
        $this->documentEdit($cid);
        break;
      case 'addx':  // add a document - execute
        $this->documentEditExec($cid);
        break;
      case 'edit':  // edit a document
        $did = intval(\Input::get('zaD'));
        $this->documentEdit($cid, $did);
        break;
      case 'editx':  // edit a document - execute
        $did = intval(\Input::get('zaD'));
        $this->documentEditExec($cid, $did);
        break;
      case 'delete':  // delete a document
        $did = intval(\Input::get('zaD'));
        $this->documentDelete($cid, $did);
        break;
      case 'deletex':  // delete a document - execute
        $did = intval(\Input::get('zaD'));
        $this->documentDeleteExec($cid, $did);
        break;
      case 'publish':  // publish a document
        $did = intval(\Input::get('zaD'));
        $this->documentPublish($cid, $did);
        break;
      case 'publishx':  // publish a document - execute
        $did = intval(\Input::get('zaD'));
        $this->documentPublishExec($cid, $did);
        break;
      case 'cancelpub':  // cancel a published document
        $did = intval(\Input::get('zaD'));
        $this->documentCancelPublished($cid, $did);
        break;
      case 'cancelpubx':  // cancel a published document - exec
        $did = intval(\Input::get('zaD'));
        $this->documentCancelPublishedExec($cid, $did);
        break;
      case 'uncancelpub':  // uncancel a published document
        $did = intval(\Input::get('zaD'));
        $this->documentUncancelPublished($cid, $did);
        break;
      case 'uncancelpubx':  // uncancel a published document - exec
        $did = intval(\Input::get('zaD'));
        $this->documentUncancelPublishedExec($cid, $did);
        break;
      case 'show':  // show a document
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
	 * Upload a file
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document owner (0=a new one)
	 */
	protected function fileUpload($category, $id) {
    if ($id > 0) {
      // check category
      $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
      if ($cat === null) {
        // error, invalid category
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
        return;
      }
      // check document
      $doc = \ZadAlboDocumentModel::getDocument($category, $id, 'DRAFT');
      if ($doc === null) {
        // error, invalid document
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
        return;
      }
    }
    if (empty($_FILES)) {
      // return files already existent
      $result  = array();
      // get file parameter name
      $pname = \Input::post('pname');
      if (isset($_SESSION['zad_albo'][$pname])) {
        // get data from session
        $result = $_SESSION['zad_albo'][$pname];
      } elseif ($id > 0 && $pname == 'document') {
        // get document file info
        $file = \FilesModel::findByUuid($doc->document);
        if ($file === null) {
          // upload error
          $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_file']);
          return;
        }
        $obj = array(
          'type' => 'existent',
          'uuid' => \String::binToUuid($doc->document),
          'name' => $doc->documentName,
          'ext' => $file->extension,
          'size' => filesize(TL_ROOT . '/' . $file->path));
        $result[] = $obj;
        // save files in session
        $_SESSION['zad_albo'][$pname][] = $obj;
      } elseif ($id > 0 && $pname == 'attach') {
        // get attachment files info
        $attach = unserialize($doc->attach);
        $attachNames = unserialize($doc->attachNames);
        foreach ($attach as $katt=>$att) {
          $file = \FilesModel::findByUuid($att);
          if ($file === null) {
            // upload error
            $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_file']);
            return;
          }
          $obj = array(
            'type' => 'existent',
            'uuid' => \String::binToUuid($att),
            'name' => $attachNames[$katt],
            'ext' => $file->extension,
            'size' => filesize(TL_ROOT . '/' . $file->path));
          $result[] = $obj;
          // save files in session
          $_SESSION['zad_albo'][$pname][] = $obj;
        }
      }
      // send back file info and exit
      header('Content-type: application/json');
      echo(json_encode($result));
      die();
    } elseif (isset($_FILES['document'])) {
      // upload document
      $this->saveUploadedFiles($id, 'document', $_FILES['document']);
    } elseif (isset($_FILES['attach'])) {
      // upload attachments
      $this->saveUploadedFiles($id, 'attach', $_FILES['attach']);
    } else {
      // upload error
      $this->errorUpload($GLOBALS['TL_LANG']['tl_zad_albo']['err_file']);
    }
  }

	/**
	 * Cancel an uploaded a file
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document owner (0=a new one)
	 */
	protected function fileCancel($category, $id) {
    if ($id > 0) {
      // check category
      $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
      if ($cat === null) {
        // error, invalid category
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
        return;
      }
      // check document
      $doc = \ZadAlboDocumentModel::getDocument($category, $id, 'DRAFT');
      if ($doc === null) {
        // error, invalid document
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
        return;
      }
    }
    // delete uploaded files
    $pname = \Input::post('pname');
    $file = \Input::post('file');
    if ($file) {
      // remove files from session
      $this->import('Files');
      foreach ($_SESSION['zad_albo'][$pname] as $kfl=>$fl) {
        if ($fl['uuid'] == $file['uuid']) {
          // found: remove
          if ($file['type'] == 'uploaded') {
            // delete uploaded file
            $this->Files->delete('system/tmp/' . $file['uuid']);
            unset($_SESSION['zad_albo'][$pname][$kfl]);
          } elseif ($file['type'] == 'existent') {
            // remove later
            $_SESSION['zad_albo'][$pname][$kfl]['type'] = 'removed';
          }
          break;
        }
      }
    }
  }

	/**
	 * Show the form for creating/editing a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document to edit (0=add a new one)
	 */
	protected function documentEdit($category, $id=0) {
    // init session
    unset($_SESSION['zad_albo']);
    // set template
    $this->Template = new \FrontendTemplate('zadam_edit');
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    if ($id > 0) {
      // edit a document
      $param = array();
      $param['zaA'] = 'editx';
      $param['zaC'] = $category;
      $param['zaD'] = $id;
      $this->Template->href_action = $this->createUrl($param, $this->baseUrl);
      $this->Template->header = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentedit'], $cat->name);
      // get document
      $doc = \ZadAlboDocumentModel::getDocument($category, $id, 'DRAFT');
      if ($doc === null) {
        // error, invalid document
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
        return;
      }
      // get data
      $data = array();
      $data['subject'] = $doc->subject;
      if ($cat->showRefNumber) {
        // set reference number
        $data['referenceNumber'] = $doc->referenceNumber;
      }
      if ($cat->showRefDate) {
        // format reference date
        $date = new \Date($doc->referenceDate);
        $data['referenceDate'] = $date->date;
      }
      if ($cat->endDate == 'ed_0') {
        // format end date
        $date = new \Date($doc->endDate);
        $data['endDate'] = $date->date;
      }
      if ($cat->unpublishDate == 'ud_0') {
        // format unpublish date
        $date = new \Date($doc->unpublishDate);
        $data['unpublishDate'] = $date->date;
      }
    } else {
      // add a new document
      $param = array();
      $param['zaA'] = 'addx';
      $param['zaC'] = $category;
      $this->Template->href_action = $this->createUrl($param, $this->baseUrl);
      $this->Template->header = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentadd'], $cat->name);
      // data
      $data = array();
      if ($cat->showRefDate) {
        // default for refdate
        $data['referenceDate'] = $this->createDate(time());
      }
    }
    // set zebra_datapicker css and javascript
    $GLOBALS['TL_CSS'][] = 'system/modules/zad_albo/vendor/zebra_datepicker-1.8.9/css/default.css';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/zad_albo/vendor/zebra_datepicker-1.8.9/js/zebra_datepicker.min.js';
    // set dropzone css and javascript
    $GLOBALS['TL_CSS'][] = 'system/modules/zad_albo/vendor/dropzone-3.10.2/css/dropzone.min.css';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/zad_albo/vendor/dropzone-3.10.2/js/dropzone.min.js';
    // set dropzone urls
    $param = array();
    $param['zaA'] = 'upload';
    $param['zaC'] = $category;
    $param['zaD'] = $id;
    $this->Template->href_dropzone = $this->createUrl($param, $this->baseUrl, false);
    $param['zaA'] = 'cancel';
    $this->Template->href_dropzone_cancel = $this->createUrl($param, $this->baseUrl, false);
    // set other template vars
    $this->Template->error = array();
    $this->Template->data = $data;
    $this->Template->attach = $cat->enableAttach;
    $this->Template->maxFilesize = intVal(\Config::get('maxFileSize') / (1024 * 1024));
    $this->Template->acceptedFiles = implode(',', array_map(function($a) { return '.'.$a; }, trimsplit(',', strtolower($this->albo->fileTypes))));
    $this->Template->showRefNumber = $cat->showRefNumber;
    $this->Template->showRefDate = $cat->showRefDate;
    $this->Template->showEndDate = ($cat->endDate == 'ed_0');
    $this->Template->showUnpublishDate = ($cat->unpublishDate == 'ud_0');
    $this->Template->dateFormat = \Config::get('dateFormat');
    $this->Template->months = implode('\',\'', $GLOBALS['TL_LANG']['MONTHS']);
    $this->Template->days = implode('\',\'', $GLOBALS['TL_LANG']['DAYS']);
    $this->Template->description = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_mandatorydesc'];
    $this->Template->lbl_document = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_document'];
    $this->Template->lbl_attach = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_attach'];
    $this->Template->lbl_mandatory = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_mandatory'];
    $this->Template->lbl_subject = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_subject'];
    $this->Template->lbl_referenceNumber = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceNumber'];
    $this->Template->lbl_referenceDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceDate'];
    $this->Template->lbl_endDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_endDate'];
    $this->Template->lbl_unpublishDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_unpublishDate'];
    $this->Template->lbl_clearDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_clearDate'];
    $this->Template->lbl_todayDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_todayDate'];
    $this->Template->lbl_dropzone = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_dropzone'];
    $this->Template->but_save = $GLOBALS['TL_LANG']['tl_zad_albo']['but_save'];
    $this->Template->but_cancel = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancel'];
    $this->Template->but_removefile = $GLOBALS['TL_LANG']['tl_zad_albo']['but_removefile'];
    $this->Template->but_cancelupload = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancelupload'];
    $this->Template->wrn_cancelupload = $GLOBALS['TL_LANG']['tl_zad_albo']['wrn_cancelupload'];
    $this->Template->err_filetype = $GLOBALS['TL_LANG']['tl_zad_albo']['err_filetype'];
    $this->Template->err_filesize = $GLOBALS['TL_LANG']['tl_zad_albo']['err_filesize'];
    $this->Template->err_filecount = $GLOBALS['TL_LANG']['tl_zad_albo']['err_filecount'];
    $this->Template->err_dropzone = $GLOBALS['TL_LANG']['tl_zad_albo']['err_dropzone'];
  }

	/**
	 * Edit or create a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document to edit (0=add a new one)
	 */
	protected function documentEditExec($category, $id=0) {
    // init
    $error = array();
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    if ($id > 0) {
      // get document
      $doc = \ZadAlboDocumentModel::getDocument($category, $id, 'DRAFT');
      if ($doc === null) {
        // error, invalid document
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
        return;
      }
    }
    if (strlen(\Input::post('_save')) == 0) {
      // cancel button pressed: remove uploaded files and exit
      $this->import('Files');
      if (isset($_SESSION['zad_albo']['document'])) {
        foreach ($_SESSION['zad_albo']['document'] as $file) {
          if ($file['type'] == 'uploaded') {
            // delete uploaded file
            $this->Files->delete('system/tmp/' . $file['uuid']);
          }
        }
      }
      if (isset($_SESSION['zad_albo']['attach'])) {
        foreach ($_SESSION['zad_albo']['attach'] as $file) {
          if ($file['type'] == 'uploaded') {
            // delete uploaded file
            $this->Files->delete('system/tmp/' . $file['uuid']);
          }
        }
      }
      // redirect to document list
      $param = array();
      $param['zaA'] = 'list';
      $param['zaC'] = $category;
      $this->redirect($this->createUrl($param, $this->baseUrl));
    }
    // validate data
    $data = array();
    // subject
    $data['subject'] = trim(\Input::post('field_subject'));
    if (empty($data['subject'])) {
      // no data
      $error['subject'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_mandatory'];
    }
    // reference number
    if ($cat->showRefNumber) {
      $data['referenceNumber'] = trim(\Input::post('field_referenceNumber'));
      if (empty($data['referenceNumber'])) {
        // no data
        $error['referenceNumber'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_mandatory'];
      }
    }
    // reference date
    if ($cat->showRefDate) {
      $data['referenceDate'] = trim(\Input::post('field_referenceDate'));
      if (empty($data['referenceDate'])) {
        // no data
        $error['referenceDate'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_mandatory'];
      } else {
        // check format
        try {
          $date = new \Date($data['referenceDate'], \Config::get('dateFormat'));
        } catch (\Exception $e) {
          // invalid format
          $date = null;
        }
        if (!$date || ($date->date != $data['referenceDate'])) {
          // invalid format
          $error['referenceDate'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_dateformat'];
        } else {
          // ok, save it as timestamp
          $data['referenceDate'] = $date->timestamp;
        }
      }
    }
    // end date
    if ($cat->endDate == 'ed_0') {
      $data['endDate'] = trim(\Input::post('field_endDate'));
      if (empty($data['endDate'])) {
        // no data
        $error['endDate'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_mandatory'];
      } else {
        // check format
        try {
          $date = new \Date($data['endDate'], \Config::get('dateFormat'));
        } catch (\Exception $e) {
          // invalid format
          $date = null;
        }
        if (!$date || ($date->date != $data['endDate'])) {
          // invalid format
          $error['endDate'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_dateformat'];
        } else {
          // ok, save it as timestamp
          $data['endDate'] = $date->timestamp;
          if ($cat->showRefDate && !isset($error['referenceDate']) && $data['referenceDate'] >= $data['endDate']) {
            // invalid date
            $error['endDate'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_enddate'];
          }
        }
      }
    }
    // unpublish date
    if ($cat->unpublishDate == 'ud_0') {
      $data['unpublishDate'] = trim(\Input::post('field_unpublishDate'));
      if (empty($data['unpublishDate'])) {
        // no data
        $error['unpublishDate'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_mandatory'];
      } else {
        // check format
        try {
          $date = new \Date($data['unpublishDate'], \Config::get('dateFormat'));
        } catch (\Exception $e) {
          // invalid format
          $date = null;
        }
        if (!$date || ($date->date != $data['unpublishDate'])) {
          // invalid format
          $error['unpublishDate'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_dateformat'];
        } else {
          // ok, save it as timestamp
          $data['unpublishDate'] = $date->timestamp;
          if (isset($data['endDate']) && !isset($error['endDate']) && $data['endDate'] >= $data['unpublishDate']) {
            // invalid date
            $error['unpublishDate'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_unpublishdate'];
          }
        }
      }
    }
    // check file document
    if (!isset($_SESSION['zad_albo']['document'])) {
      // no document
      $error['document'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_filemandatory'];
    } else {
      $cnt = 0;
      foreach ($_SESSION['zad_albo']['document'] as $fl) {
        if ($fl['type'] != 'removed') {
          $cnt++;
          break;
        }
      }
      if ($cnt == 0) {
        // no document
        $error['document'] = $GLOBALS['TL_LANG']['tl_zad_albo']['err_filemandatory'];
      }
    }
    // save or show errors
    if (empty($error)) {
      // save document
      if ($id == 0) {
        // new document
  		  $doc = new \ZadAlboDocumentModel();
      }
      $doc->pid = $category;
      $doc->tstamp = time();
      $doc->save(); // create new id
      // save document
      $file_list = $this->storeFiles($_SESSION['zad_albo']['document'], $doc->id);
      if ($file_list === NULL) {
        // error, can't store file
        if ($id == 0) {
          // remove new document
    		  $doc->delete();
        }
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_store']);
        return;
      }
      $doc->document = $file_list[0][0];
      $doc->documentName = $file_list[1][0];
      // save attached files
      $file_list[0] = array();
      $file_list[1] = array();
      if ($cat->enableAttach && isset($_SESSION['zad_albo']['attach'])) {
        $file_list = $this->storeFiles($_SESSION['zad_albo']['attach'], $doc->id);
        if ($file_list === NULL) {
          // error, can't store file
          if ($id == 0) {
            // remove new document
      		  $doc->delete();
          }
          $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_store']);
          return;
        }
      }
      $doc->attach = serialize($file_list[0]);
      $doc->attachNames = serialize($file_list[1]);
      // save other data
      $doc->number = '';
      $doc->subject = $data['subject'];
      $doc->referenceNumber = ($cat->showRefNumber) ? $data['referenceNumber'] : '';
      $doc->referenceDate = ($cat->showRefDate) ? $data['referenceDate'] : 0;
      $doc->startDate = 0;
      $doc->endDate = ($cat->endDate == 'ed_0') ? $data['endDate'] : 0;
      $doc->unpublishDate = ($cat->unpublishDate == 'ud_0') ? $data['unpublishDate'] : 0;
      $doc->state = 'DRAFT';
      $doc->sentBy = $this->userId;
      $doc->canceled = '';
      $doc->save();
      // go to document list
      $param = array();
      $param['zaA'] = 'list';
      $param['zaC'] = $category;
      $this->redirect($this->createUrl($param, $this->baseUrl));
    } else {
      // show errors
      $this->Template = new \FrontendTemplate('zadam_edit');
      if ($id > 0) {
        // edit a document
        $param = array();
        $param['zaA'] = 'editx';
        $param['zaC'] = $category;
        $param['zaD'] = $id;
        $this->Template->href_action = $this->createUrl($param, $this->baseUrl);
        $this->Template->header = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentedit'], $cat->name);
      } else {
        // add a new document
        $param = array();
        $param['zaA'] = 'addx';
        $param['zaC'] = $category;
        $this->Template->href_action = $this->createUrl($param, $this->baseUrl);
        $this->Template->header = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentadd'], $cat->name);
      }
      // format dates
      if ($cat->showRefDate && $data['referenceDate'] > 0) {
        // format reference date
        $date = new \Date($data['referenceDate']);
        $data['referenceDate'] = $date->date;
      }
      if ($cat->endDate == 'ed_0' && $data['endDate'] > 0) {
        // format end date
        $date = new \Date($data['endDate']);
        $data['endDate'] = $date->date;
      }
      if ($cat->unpublishDate == 'ud_0' && $data['unpublishDate'] > 0) {
        // format unpublish date
        $date = new \Date($data['unpublishDate']);
        $data['unpublishDate'] = $date->date;
      }
      // set zebra_datapicker css and javascript
      $GLOBALS['TL_CSS'][] = 'system/modules/zad_albo/vendor/zebra_datepicker-1.8.9/css/default.css';
      $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/zad_albo/vendor/zebra_datepicker-1.8.9/js/zebra_datepicker.min.js';
      // set dropzone css and javascript
      $GLOBALS['TL_CSS'][] = 'system/modules/zad_albo/vendor/dropzone-3.10.2/css/dropzone.min.css';
      $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/zad_albo/vendor/dropzone-3.10.2/js/dropzone.min.js';
      // set dropzone urls
      $param = array();
      $param['zaA'] = 'upload';
      $param['zaC'] = $category;
      $param['zaD'] = $id;
      $this->Template->href_dropzone = $this->createUrl($param, $this->baseUrl, false);
      $param['zaA'] = 'cancel';
      $this->Template->href_dropzone_cancel = $this->createUrl($param, $this->baseUrl, false);
      // set other template vars
      $this->Template->error = $error;
      $this->Template->data = $data;
      $this->Template->attach = $cat->enableAttach;
      $this->Template->maxFilesize = intVal(\Config::get('maxFileSize') / (1024 * 1024));
      $this->Template->acceptedFiles = implode(',', array_map(function($a) { return '.'.$a; }, trimsplit(',', strtolower($this->albo->fileTypes))));
      $this->Template->showRefNumber = $cat->showRefNumber;
      $this->Template->showRefDate = $cat->showRefDate;
      $this->Template->showEndDate = ($cat->endDate == 'ed_0');
      $this->Template->showUnpublishDate = ($cat->unpublishDate == 'ud_0');
      $this->Template->dateFormat = \Config::get('dateFormat');
      $this->Template->months = implode('\',\'', $GLOBALS['TL_LANG']['MONTHS']);
      $this->Template->days = implode('\',\'', $GLOBALS['TL_LANG']['DAYS']);
      $this->Template->description = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_mandatorydesc'];
      $this->Template->lbl_document = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_document'];
      $this->Template->lbl_attach = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_attach'];
      $this->Template->lbl_mandatory = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_mandatory'];
      $this->Template->lbl_subject = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_subject'];
      $this->Template->lbl_referenceNumber = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceNumber'];
      $this->Template->lbl_referenceDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceDate'];
      $this->Template->lbl_endDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_endDate'];
      $this->Template->lbl_unpublishDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_unpublishDate'];
      $this->Template->lbl_clearDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_clearDate'];
      $this->Template->lbl_todayDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_todayDate'];
      $this->Template->lbl_dropzone = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_dropzone'];
      $this->Template->but_save = $GLOBALS['TL_LANG']['tl_zad_albo']['but_save'];
      $this->Template->but_cancel = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancel'];
      $this->Template->but_removefile = $GLOBALS['TL_LANG']['tl_zad_albo']['but_removefile'];
      $this->Template->but_cancelupload = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancelupload'];
      $this->Template->wrn_cancelupload = $GLOBALS['TL_LANG']['tl_zad_albo']['wrn_cancelupload'];
      $this->Template->err_filetype = $GLOBALS['TL_LANG']['tl_zad_albo']['err_filetype'];
      $this->Template->err_filesize = $GLOBALS['TL_LANG']['tl_zad_albo']['err_filesize'];
      $this->Template->err_filecount = $GLOBALS['TL_LANG']['tl_zad_albo']['err_filecount'];
      $this->Template->err_dropzone = $GLOBALS['TL_LANG']['tl_zad_albo']['err_dropzone'];
    }
  }

	/**
	 * Show the confirm for deleting a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document to delete
	 */
	protected function documentDelete($category, $id) {
    // set action URL
    $param = array();
    $param['zaA'] = 'deletex';
    $param['zaC'] = $category;
    $param['zaD'] = $id;
    $href_action = $this->createUrl($param, $this->baseUrl);
    // set header
    $header = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentdelete']);
    // set buttons
    $but_confirm = $GLOBALS['TL_LANG']['tl_zad_albo']['but_confirm'];
    $but_cancel = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancel'];
    // show confirm form
    $this->confirm($category, $id, 'DRAFT', $href_action, $header, $but_confirm, $but_cancel);
  }

  /**
	 * Delete a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document to delete
	 */
	protected function documentDeleteExec($category, $id) {
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    // get document
    $doc = \ZadAlboDocumentModel::getDocument($category, $id, 'DRAFT');
    if ($doc === null) {
      // error, invalid document
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
      return;
    }
    if (strlen(\Input::post('_confirm')) == 0) {
      // redirect to document list
      $param = array();
      $param['zaA'] = 'list';
      $param['zaC'] = $category;
      $this->redirect($this->createUrl($param, $this->baseUrl));
    }
    // delete files
		$this->import('Files');
    $file = \FilesModel::findByUuid($doc->document);
		$this->Files->delete($file->path);
    $file->delete();
    if ($cat->enableAttach && !empty($doc->attach)) {
      $attach = deserialize($doc->attach);
      foreach ($attach as $fl) {
        $file = \FilesModel::findByUuid($fl);
        $this->Files->delete($file->path);
        $file->delete();
      }
    }
    // delete document
    $doc->delete();
    // go to document list
    $param = array();
    $param['zaA'] = 'list';
    $param['zaC'] = $category;
    $this->redirect($this->createUrl($param, $this->baseUrl));
  }

  /**
	 * Show the confirm for publishing a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document to publish
	 */
	protected function documentPublish($category, $id) {
    // set action URL
    $param = array();
    $param['zaA'] = 'publishx';
    $param['zaC'] = $category;
    $param['zaD'] = $id;
    $href_action = $this->createUrl($param, $this->baseUrl);
    // set header
    $header = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentpublish']);
    // set buttons
    $but_confirm = $GLOBALS['TL_LANG']['tl_zad_albo']['but_confirm'];
    $but_cancel = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancel'];
    // show confirm form
    $this->confirm($category, $id, 'DRAFT', $href_action, $header, $but_confirm, $but_cancel);
  }

  /**
	 * Publish a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document to publish
	 */
	protected function documentPublishExec($category, $id) {
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    // get document
    $doc = \ZadAlboDocumentModel::getDocument($category, $id, 'DRAFT');
    if ($doc === null) {
      // error, invalid document
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
      return;
    }
    if (strlen(\Input::post('_confirm')) == 0) {
      // redirect to document list
      $param = array();
      $param['zaA'] = 'list';
      $param['zaC'] = $category;
      $this->redirect($this->createUrl($param, $this->baseUrl));
    }
    // publish the document
    if ($cat->enablePdf) {
		  // convert document to PDF
      $file_pdf = $this->convertToPdf($doc->document);
      if ($file_pdf === null) {
        // error, no file
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nofile']);
        return;
      }
      if ($file_pdf != $doc->document) {
        // file converted
        $dot = strrpos($doc->documentName, '.');
        $doc->documentName = substr($doc->documentName, 0, ($dot === null ? strlen($doc->documentName) : $dot)) . '.pdf';
      }
      if ($cat->enableAttach) {
        $files = unserialize($doc->attach);
        $filenames = unserialize($doc->attachNames);
        foreach ($files as $kfile=>$file) {
          $file_pdf = $this->convertToPdf($file);
          if ($file_pdf === null) {
            // error, no file
            $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nofile']);
            return;
          }
          $files[$kfile] = $file_pdf;
          $dot = strrpos($filenames[$kfile], '.');
          $filenames[$kfile] = substr($filenames[$kfile], 0, ($dot === null ? strlen($filenames[$kfile]) : $dot)) . '.pdf';
        }
        $doc->attach = serialize($files);
        $doc->attachNames = serialize($filenames);
      }
    }
    $doc->tstamp = time();
    $doc->startDate = $this->createTimestamp($doc->tstamp);
    if ($cat->endDate != 'ed_0') {
      // set end date
      $doc->endDate = $this->createTimestamp($doc->startDate, substr($cat->endDate, 3));
    }
    if ($cat->unpublishDate != 'ud_0') {
      // set unpublish date
      $doc->unpublishDate = $this->createTimestamp($doc->startDate, 0, substr($cat->unpublishDate, 3));
    }
    $doc->state = 'PUBLISHED';
    $doc->sentBy = $this->userId;
    // set number
    $year = date('Y', $doc->tstamp);
    if ($this->albo->lastNumber == '' || substr($this->albo->lastNumber, -4) != $year) {
      // first document number for this year
      $doc->number = '1/' . $year;
    } else {
      // get next document number for this year
      $num = explode('/', $this->albo->lastNumber);
      $doc->number = ($num[0] + 1) . '/' . $year;
    }
    // save document data
    $doc->save();
    // update document number
    $this->albo->lastNumber = $doc->number;
    $this->albo->save();
		// add a log entry for published file
    $this->log(sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['log_published'], $doc->number, $id), __METHOD__, 'ZAD_ALBO');
    // go to document list
    $param = array();
    $param['zaA'] = 'list';
    $param['zaC'] = $category;
    $this->redirect($this->createUrl($param, $this->baseUrl));
  }

  /**
	 * Show the confirm for canceling a published document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the published document to cancel
	 */
	protected function documentCancelPublished($category, $id) {
    // set action URL
    $param = array();
    $param['zaA'] = 'cancelpubx';
    $param['zaC'] = $category;
    $param['zaD'] = $id;
    $href_action = $this->createUrl($param, $this->baseUrl);
    // set header
    $header = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_cancelpublished']);
    // set buttons
    $but_confirm = $GLOBALS['TL_LANG']['tl_zad_albo']['but_confirm'];
    $but_cancel = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancel'];
    // show confirm form
    $this->confirm($category, $id, 'PUBLISHED', $href_action, $header, $but_confirm, $but_cancel);
  }

  /**
	 * Cancel a published document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the published document to cancel
	 */
	protected function documentCancelPublishedExec($category, $id) {
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    // get document
    $doc = \ZadAlboDocumentModel::getDocument($category, $id, 'PUBLISHED');
    if ($doc === null) {
      // error, invalid document
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
      return;
    }
    if (strlen(\Input::post('_confirm')) == 0) {
      // redirect to document list
      $param = array();
      $param['zaA'] = 'list';
      $param['zaC'] = $category;
      $this->redirect($this->createUrl($param, $this->baseUrl));
    }
    // cancel the published document
    $doc->tstamp = time();
    $doc->sentBy = $this->userId;
    $doc->canceled = '1';
    // save document data
    $doc->save();
		// add a log entry for published file
    $this->log(sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['log_pubcanceled'], $doc->number, $id), __METHOD__, 'ZAD_ALBO');
    // go to document list
    $param = array();
    $param['zaA'] = 'list';
    $param['zaC'] = $category;
    $this->redirect($this->createUrl($param, $this->baseUrl));
  }

  /**
	 * Show the confirm for uncanceling a published document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the published document to uncancel
	 */
	protected function documentUncancelPublished($category, $id) {
    // set action URL
    $param = array();
    $param['zaA'] = 'uncancelpubx';
    $param['zaC'] = $category;
    $param['zaD'] = $id;
    $href_action = $this->createUrl($param, $this->baseUrl);
    // set header
    $header = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_uncancelpublished']);
    // set buttons
    $but_confirm = $GLOBALS['TL_LANG']['tl_zad_albo']['but_confirm'];
    $but_cancel = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancel'];
    // show confirm form
    $this->confirm($category, $id, 'PUBLISHED', $href_action, $header, $but_confirm, $but_cancel);
  }

  /**
	 * Uncancel a published document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the published document to cancel
	 */
	protected function documentUncancelPublishedExec($category, $id) {
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    // get document
    $doc = \ZadAlboDocumentModel::getDocument($category, $id, 'PUBLISHED');
    if ($doc === null) {
      // error, invalid document
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
      return;
    }
    if (strlen(\Input::post('_confirm')) == 0) {
      // redirect to document list
      $param = array();
      $param['zaA'] = 'list';
      $param['zaC'] = $category;
      $this->redirect($this->createUrl($param, $this->baseUrl));
    }
    // uncancel the published document
    $doc->tstamp = time();
    $doc->sentBy = $this->userId;
    $doc->canceled = '';
    // save document data
    $doc->save();
		// add a log entry for published file
    $this->log(sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['log_pubuncanceled'], $doc->number, $id), __METHOD__, 'ZAD_ALBO');
    // go to document list
    $param = array();
    $param['zaA'] = 'list';
    $param['zaC'] = $category;
    $this->redirect($this->createUrl($param, $this->baseUrl));
  }

	/**
	 * Show the preview of a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the published document to cancel
	 */
	protected function documentShow($category, $id) {
    // set template
    $this->Template = new \FrontendTemplate('zadam_show');
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    // get document
    $doc = \ZadAlboDocumentModel::getDocument($category, $id);
    if ($doc === null) {
      // error, invalid document
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
      return;
    }
    // get data
    $data = array();
    // category
    $data['category'] = $cat->name;
    // state
    if ($doc->canceled) {
      // canceled
      $data['state'] = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_statecanc'], $doc->number);
    } elseif ($doc->state == 'PUBLISHED') {
      // published
      $data['state'] = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_statepub'], $doc->number);
    } else {
      // draft
      $data['state'] = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_statedraft'];
    }
    // subject
    $data['subject'] = $doc->subject;
    if ($cat->showRefNumber) {
      // set reference number
      $data['referenceNumber'] = $doc->referenceNumber;
    }
    if ($cat->showRefDate) {
      // format reference date
      $date = new \Date($doc->referenceDate);
      $data['referenceDate'] = $date->date;
    }
    if ($doc->state != 'DRAFT') {
      // format start date
      $date = new \Date($doc->startDate);
      $data['startDate'] = $date->date;
    }
    if ($cat->endDate == 'ed_0' || $doc->state != 'DRAFT') {
      // format end date
      $date = new \Date($doc->endDate);
      $data['endDate'] = $date->date;
    }
    if ($cat->unpublishDate == 'ud_0' || $doc->state != 'DRAFT') {
      // format unpublish date
      $date = new \Date($doc->unpublishDate);
      $data['unpublishDate'] = $date->date;
    }
    // document
    $param = array();
    $param['zaA'] = 'download';
    $param['zaC'] = $category;
    $param['zaF'] = \String::binToUuid($doc->document);
    $data['href_document'] = $this->createUrl($param, $this->baseUrl);
    $data['document'] = $doc->documentName;
    // attach files
    $attach = array();
    if ($cat->enableAttach) {
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
    // last modified
    $date = new \Date($doc->tstamp);
    $user = \MemberModel::findByPk($doc->sentBy);
    $username = $user->lastname.' '.$user->firstname;
    $data['lastmodified'] = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_lastmodinfo'], $date->datim, $username);
    // referer url
    $param = array();
    $param['zaA'] = 'list';
    $param['zaC'] = $category;
    $referer = $this->createUrl($param, $this->baseUrl);
    // set template vars
    $this->Template->header = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentshow'];
    $this->Template->data = $data;
    $this->Template->attach = $attach;
    $this->Template->showRefNumber = $cat->showRefNumber;
    $this->Template->showRefDate = $cat->showRefDate;
  	$this->Template->referer = $referer;
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
    $this->Template->lbl_category = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_category'];
    $this->Template->lbl_state = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_state'];
    $this->Template->lbl_subject = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_subject'];
    $this->Template->lbl_referenceNumber = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceNumber'];
    $this->Template->lbl_referenceDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceDate'];
    $this->Template->lbl_startDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_startDate'];
    $this->Template->lbl_endDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_endDate'];
    $this->Template->lbl_unpublishDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_unpublishDate'];
    $this->Template->lbl_document = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_document'];
    $this->Template->lbl_attach = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_attach'];
    $this->Template->lbl_lastmodified = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_lastmodified'];
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
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    // get document
    $doc = \ZadAlboDocumentModel::getDocumentByFile($category, $uuid);
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
    } elseif ($cat->enableAttach) {
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
    $this->Template = new \FrontendTemplate('zadam_list');
    // check category
    if ($category == 0) {
      // category listing
      $categories = array();
      $cat = \ZadAlboCategoryModel::listCategories($this->albo->id);
      $param = array();
      $param['zaA'] = 'list';
      while ($cat->next()) {
        $param['zaC'] = $cat->id;
        $categories[] = array(
          'href' => $this->createUrl($param, $this->baseUrl),
          'name' => $cat->name);
      }
      // set other template vars
      $this->Template->categories = $categories;
      $this->Template->lbl_categorylist = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_categorylist'];
    } else {
      // document listing
      $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
      if ($cat === null) {
        // error, invalid category
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
        return;
      }
      // pagination
      $limit = null;
      $offset = null;
      $total = \ZadAlboDocumentModel::countDocuments($category);
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
      $docs = \ZadAlboDocumentModel::findDocuments($category, $offset, $limit);
      $index = 0;
      while ($docs->next()) {
        // state
        if ($docs->state == 'PUBLISHED') {
          // published document
          $date = new \Date($docs->startDate);
          $data[$index]['state'] = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_published'], $date->date);
        } else {
          // draft document
          $date = new \Date($docs->tstamp);
          $data[$index]['state'] = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_draft'], $date->date);
        }
        if ($docs->canceled) {
          // canceled
          $data[$index]['state'] = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_canceled'] . $data[$index]['state'];
        }
        if ($cat->showRefNumber) {
          // reference number
          $data[$index]['referenceNumber'] = $docs->referenceNumber;
        }
        if ($cat->showRefDate) {
          // reference date
          $date = new \Date($docs->referenceDate);
          $data[$index]['referenceDate'] = $date->date;
        }
        $data[$index]['subject'] = $docs->subject;
        // buttons
        $data[$index]['href_edit'] = null;
        $data[$index]['href_delete'] = null;
        $data[$index]['href_publish'] = null;
        $data[$index]['href_cancel'] = null;
        $data[$index]['href_uncancel'] = null;
        $param = array();
        $param['zaD'] = $docs->id;
        $param['zaC'] = $category;
        if ($docs->state == 'DRAFT') {
          // user can edit/delete/publish a document
          $param['zaA'] = 'edit';
          $data[$index]['href_edit'] = $this->createUrl($param, $this->baseUrl);
          $param['zaA'] = 'delete';
          $data[$index]['href_delete'] = $this->createUrl($param, $this->baseUrl);
          $param['zaA'] = 'publish';
          $data[$index]['href_publish'] = $this->createUrl($param, $this->baseUrl);
        } elseif ($this->isAdmin) {
          // user can cancel/uncancel a published document
          $param['zaA'] = ($docs->canceled) ? 'uncancelpub' : 'cancelpub';
          $data[$index]['href_'.(($docs->canceled) ? 'uncancel' : 'cancel')] = $this->createUrl($param, $this->baseUrl);
        }
        // preview
        $param['zaA'] = 'show';
        $data[$index]['href_show'] = $this->createUrl($param, $this->baseUrl);
        $index++;
      }
      // set add url
      $param = array();
      $param['zaA'] = 'add';
      $param['zaC'] = $category;
      $this->Template->href_add = $this->createUrl($param, $this->baseUrl);
      // set category list url
      $param = array();
      $param['zaA'] = 'list';
      $this->Template->href_catlist = $this->createUrl($param, $this->baseUrl);
      // set other template vars
      $this->Template->docs = $data;
      $this->Template->wrn_nodata = $GLOBALS['TL_LANG']['tl_zad_albo']['wrn_nodata'];
      $this->Template->showRefNumber = $cat->showRefNumber;
      $this->Template->showRefDate = $cat->showRefDate;
      $this->Template->lbl_categoryname = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_categoryname'], $cat->name);
      $this->Template->lbl_documentlist = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentlist'];
      $this->Template->lbl_state = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_state'];
      $this->Template->lbl_subject = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_subject'];
      $this->Template->lbl_referenceNumber = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceNumber'];
      $this->Template->lbl_referenceDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceDate'];
      $this->Template->but_add = $GLOBALS['TL_LANG']['tl_zad_albo']['but_add'];
      $this->Template->but_show = $GLOBALS['TL_LANG']['tl_zad_albo']['but_show'];
      $this->Template->but_edit = $GLOBALS['TL_LANG']['tl_zad_albo']['but_edit'];
      $this->Template->but_delete = $GLOBALS['TL_LANG']['tl_zad_albo']['but_delete'];
      $this->Template->but_publish = $GLOBALS['TL_LANG']['tl_zad_albo']['but_publish'];
      $this->Template->but_cancelpub = $GLOBALS['TL_LANG']['tl_zad_albo']['but_cancelpub'];
      $this->Template->but_uncancelpub = $GLOBALS['TL_LANG']['tl_zad_albo']['but_uncancelpub'];
      $this->Template->but_catlist = $GLOBALS['TL_LANG']['tl_zad_albo']['but_catlist'];
    }
	}

	/**
	 * Show an error message and terminate
	 *
	 * @param string $message  Message to show, if null disable module
 	 */
	private function errorMessage($message) {
    if (\Input::get('zaA') == 'upload' || \Input::get('zaA') == 'cancel') {
      // we are in AJAX context, abort module
      header("HTTP/1.1 500 Internal Server Error");
      header('Content-type: text/plain');
      echo $message;
      die();
    } else {
      // no AJAX, use normal module termination
      $this->Template = new \FrontendTemplate('zadam_message');
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
  }

  /**
	 * Create a base URL without some parameters
	 *
	 * @param bool $encode  True to $encode "&" in URL, False otherwise
	 *
	 * @return string  The new URL
	 */
	private function createBaseUrl($encode=true) {
    $params = array('zaA', 'zaC', 'zaP', 'zaD', 'zaF');
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

  /**
	 * Create a new formatted date with an offset of months or years
	 *
	 * @param int $date  Date as timestamp
	 * @param int $months  Number of months to add to date
	 * @param int $years  Number of years to add to date
	 *
	 * @return string  The new formatted date
	 */
  private function createDate($date, $months=0, $years=0) {
    $dt = new \Date(mktime(0, 0, 0, date('n', $date) + $months, date('j', $date), date('Y', $date) + $years));
    return $dt->date;
  }

  /**
	 * Create a new timestamp with an offset of months or years
	 *
	 * @param int $date  Date as timestamp
	 * @param int $months  Number of months to add to date
	 * @param int $years  Number of years to add to date
	 *
	 * @return int  The new t imestamp
	 */
  private function createTimestamp($date, $months=0, $years=0) {
    $ts = mktime(0, 0, 0, date('n', $date) + $months, date('j', $date), date('Y', $date) + $years);
    return $ts;
  }

	/**
	 * Save uploaded files to temp folder and send back file info
	 *
	 * @param int $id  ID of the document owner (0=a new one)
	 * @param string $pname  Parameter name used in $_FILES array
	 * @param array $files  $_FILES array for this upload
	 */
	private function saveUploadedFiles($id, $pname, $files) {
    // init return value
    $result = array();
    // allowed file types
    $allowed_types = array_intersect(
      trimsplit(',', strtolower($this->albo->fileTypes)),
      trimsplit(',', strtolower(\Config::get('uploadTypes'))));
    // save files
    for ($i = 0; $i < count($files['name']); $i++) {
      $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
      // check
      if (!is_uploaded_file($files['tmp_name'][$i])) {
		    // file was not uploaded
        if ($files['error'][$i] == 1 || $files['error'][$i] == 2) {
          // fatal: file size error
          $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_filesize']);
          return;
        } else {
          // fatal: generic upload error
          $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_file']);
          return;
        }
      } elseif ($files['size'][$i] > \Config::get('maxFileSize')) {
        // fatal: file size error
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_filesize']);
        return;
	    } elseif (!in_array($ext, $allowed_types)) {
        // fatal: file type error
        $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_filetype']);
        return;
	    } else {
        // ok, file uploaded
        $this->import('Files');
        $name = 'zad_albo-'.$id.'-'.uniqid(rand());
        $path = 'system/tmp/'.$name;
        $this->Files->move_uploaded_file($files['tmp_name'][$i], $path);
	      $this->Files->chmod($path, \Config::get('defaultFileChmod'));
        $obj = array(
          'type' => 'uploaded',
          'uuid' => $name,
          'name' => $files['name'][$i],
          'ext' => $ext,
          'size' => $files['size'][$i]);
        $result[] = $obj;
        // store info in session
        $_SESSION['zad_albo'][$pname][] = $obj;
      }
    }
    // send back file info and exit
    header('Content-type: application/json');
    echo(json_encode($result));
    die();
  }

	/**
	 * Store files previously saved in SESSION
	 *
	 * @param array $files  List of uploaded file
	 * @param array $doc_id  The document owner ID
	 *
	 * @return array|NULL  The stored file list, or NULL on error
	 */
	private function storeFiles($files, $doc_id) {
		$this->import('Files');
    $file_list[0] = array();
    $file_list[1] = array();
    foreach ($files as $file) {
      if ($file['type'] == 'uploaded') {
        // add a new uploaded file
				$folder = \FilesModel::findByUuid($this->albo->dir);
				if ($folder === NULL) {
				  // error, upload folder not found
          return NULL;
				}
        // dest folder
        $dir = new \Folder($folder->path.'/'.date('Y', time()));
        $pathname = $dir->path . '/zad_albo-' . $doc_id . '-' . uniqid(rand()) . '.' . strtolower($file['ext']);
				$this->Files->rename('system/tmp/' . $file['uuid'], $pathname);
        $this->Files->chmod($pathname, \Config::get('defaultFileChmod'));
				// generate the DB entries
				$fileobj = \FilesModel::findByPath($pathname);
    		// existing file is being replaced
    	  if ($fileobj !== null) {
          // update file info
    			$fileobj->tstamp = time();
    			$fileobj->path = $pathname;
    			$fileobj->hash = md5_file(TL_ROOT.'/'.$pathname);
    			$fileobj->save();
      		// update the hash of the target folder
      		\Dbafs::updateFolderHashes($folder->path);
    		} else {
          // new file info
    		  $fileobj = \Dbafs::addResource($pathname);
    		}
        // save file
        $file_list[0][] = $fileobj->uuid;
        $file_list[1][] = substr($file['name'], 0, -strlen($file['ext'])) . $file['ext'];
  			// add a log entry for new file
  			$this->log(sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['log_newfile'],$file['uuid'],$pathname), __METHOD__, TL_FILES);
      } elseif ($file['type'] == 'removed') {
        // remove an existent file
				$fileobj = \FilesModel::findByUuid(\String::uuidToBin($file['uuid']));
				if ($fileobj === NULL) {
				  // error, file to be removed not found
          return NULL;
				}
        // remove file
        $this->Files->delete($fileobj->path);
        \Dbafs::deleteResource($fileobj->path);
  			// add a log entry for removed file
  			$this->log(sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['log_removedfile'],$fileobj->path), __METHOD__, TL_FILES);
      } else {
        // existent file
				$fileobj = \FilesModel::findByUuid(\String::uuidToBin($file['uuid']));
				if ($fileobj === NULL) {
				  // error, existent file not found
          return NULL;
				}
        // save file
        $file_list[0][] = $fileobj->uuid;
        $file_list[1][] = substr($file['name'], 0, -strlen($file['ext'])) . $file['ext'];
      }
    }
    // return stored file list
    return $file_list;
  }

	/**
	 * Show the confirm for deleting a document
	 *
	 * @param int $category  Category identifier
	 * @param int $id  ID of the document to delete
	 * @param string $state  State of the document
	 * @param string $href_action  Form action URL
	 * @param string $header  Text info header
	 * @param string $but_confirm  Text for confirm button
	 * @param string $but_cancel  Text for cancel button
	 */
	private function confirm($category, $id, $state, $href_action, $header, $but_confirm, $but_cancel) {
    // set template
    $this->Template = new \FrontendTemplate('zadam_confirm');
    // check category
    $cat = \ZadAlboCategoryModel::getCategory($this->albo->id, $category);
    if ($cat === null) {
      // error, invalid category
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory']);
      return;
    }
    // get document
    $doc = \ZadAlboDocumentModel::getDocument($category, $id, $state);
    if ($doc === null) {
      // error, invalid document
      $this->errorMessage($GLOBALS['TL_LANG']['tl_zad_albo']['err_id']);
      return;
    }
    // get data
    $data = array();
    $data['subject'] = $doc->subject;
    if ($cat->showRefNumber) {
      // set reference number
      $data['referenceNumber'] = $doc->referenceNumber;
    }
    if ($cat->showRefDate) {
      // format reference date
      $date = new \Date($doc->referenceDate);
      $data['referenceDate'] = $date->date;
    }
    if ($doc->state != 'DRAFT') {
      // format start date
      $date = new \Date($doc->startDate);
      $data['startDate'] = $date->date;
    }
    if ($cat->endDate == 'ed_0' || $doc->state != 'DRAFT') {
      // format end date
      $date = new \Date($doc->endDate);
      $data['endDate'] = $date->date;
    }
    if ($cat->unpublishDate == 'ud_0' || $doc->state != 'DRAFT') {
      // format unpublish date
      $date = new \Date($doc->unpublishDate);
      $data['unpublishDate'] = $date->date;
    }
    // document
    $param = array();
    $param['zaA'] = 'download';
    $param['zaC'] = $category;
    $param['zaF'] = \String::binToUuid($doc->document);
    $data['href_document'] = $this->createUrl($param, $this->baseUrl);
    $data['title_document'] = sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_filedownload'], $doc->documentName);
    $data['document'] = $doc->documentName;
    // attach files
    $attach = array();
    if ($cat->enableAttach) {
      $attaches = unserialize($doc->attach);
      $attachnames = unserialize($doc->attachNames);
      foreach ($attaches as $katt=>$att) {
        $param['zaF'] = \String::binToUuid($att);
        $href = $this->createUrl($param, $this->baseUrl);
        $attach[] = array(
          'href' => $href,
          'attach' => $attachnames[$katt],
          'title' => sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['lbl_filedownload'], $attachnames[$katt]));
      }
    }
    // set template vars
    $this->Template->href_action = $href_action;
    $this->Template->header = $header;
    $this->Template->data = $data;
    $this->Template->attach = $attach;
    $this->Template->showRefNumber = $cat->showRefNumber;
    $this->Template->showRefDate = $cat->showRefDate;
    $this->Template->lbl_subject = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_subject'];
    $this->Template->lbl_referenceNumber = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceNumber'];
    $this->Template->lbl_referenceDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceDate'];
    $this->Template->lbl_startDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_startDate'];
    $this->Template->lbl_endDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_endDate'];
    $this->Template->lbl_unpublishDate = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_unpublishDate'];
    $this->Template->lbl_document = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_document'];
    $this->Template->lbl_attach = $GLOBALS['TL_LANG']['tl_zad_albo']['lbl_attach'];
    $this->Template->but_confirm = $but_confirm;
    $this->Template->but_cancel = $but_cancel;
  }

	/**
	 * Convert document to PDF format
	 *
	 * @param string $uuid  The UUID of the file
	 *
	 * @return string  The UUID of the converted file
	 */
	private function convertToPdf($uuid) {
	  // get file
    $file = \FilesModel::findByUuid($uuid);
    if ($file === null) {
      // error, no file
      return null;
    }
    if (strtolower($file->extension) != 'pdf') {
      // convert
      $filename = $file->path;
      $filename_pdf = substr($filename, 0, - strlen($file->extension)) . 'pdf';
      $cmd = 'python "' . TL_ROOT . '/system/modules/zad_albo/vendor/pyodconverter-1.9/main.py" ' .
             '"' . TL_ROOT . '/' . $filename . '" "' . TL_ROOT . '/' . $filename_pdf . '"';
      $res = exec($cmd);
      if (strlen($res) == 0 && file_exists(TL_ROOT . '/' . $filename_pdf)) {
        // PDF file created, remove original file
        unlink(TL_ROOT . '/' . $filename);
        $file->delete();
        // add to database the new file
  	    $fileobj = \Dbafs::addResource($filename_pdf);
        $uuid = $fileobj->uuid;
        $this->log(sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['log_pdffile'], $filename), __METHOD__, 'ZAD_ALBO');
      } else {
        // warning: no PDF conversion
        $this->log(sprintf($GLOBALS['TL_LANG']['tl_zad_albo']['log_pdferr'], $filename), __METHOD__, 'ZAD_ALBO');
      }
    }
    return $uuid;
  }

}

