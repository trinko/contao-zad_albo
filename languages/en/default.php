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
 * Error messages
 */
$GLOBALS['TL_LANG']['tl_zad_albo']['err_nologged'] = 'ATTENZIONE! Nessun utente connesso.';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_auth'] = 'ATTENZIONE! Utente non autorizzato.';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_nocategory'] = 'ATTENZIONE! La categoria indicata non esiste.';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_id'] = 'ATTENZIONE! Il documento indicato non è stato trovato.';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_mandatory'] = 'Devi compilare il campo!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_filemandatory'] = 'Devi caricare un documento!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_dateformat'] = 'La data non è nel formato corretto!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_enddate'] = 'La data di archiviazione deve essere successiva a quella di pubblicazione!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_unpublishdate'] = 'La data di rimozione deve essere successiva a quella di archiviazione!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_file'] = 'Il file non è stato caricato correttamente!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_filesize'] = 'La dimensione del file caricato è superiore ai limiti previsti!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_filetype'] = 'Il tipo di file caricato non è consentito!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_filecount'] = 'Non puoi caricare altri documenti!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_dropzone'] = 'Il programma usato non permette di caricare file trascinandoli su questa zona!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_store'] = 'Impossibile memorizzare il documento nella cartella di destinazione!';
$GLOBALS['TL_LANG']['tl_zad_albo']['err_nofile'] = 'Il file non è stato trovato!';


/**
 * Warning messages
 */
$GLOBALS['TL_LANG']['tl_zad_albo']['wrn_nodata'] = 'Nessun documento presente';
$GLOBALS['TL_LANG']['tl_zad_albo']['wrn_cancelupload'] = 'Sei sicuro di voler annullare il caricamento in corso?';


/**
 * Log messages
 */
$GLOBALS['TL_LANG']['tl_zad_albo']['log_removedfile'] = 'ZAD Albo - Il file "%s" è stato cancellato.';
$GLOBALS['TL_LANG']['tl_zad_albo']['log_newfile'] = 'ZAD Albo - Il file "%s" è stato caricato in "%s".';
$GLOBALS['TL_LANG']['tl_zad_albo']['log_pdffile'] = 'ZAD Albo - Il file "%s" è stato convertito in formato PDF.';
$GLOBALS['TL_LANG']['tl_zad_albo']['log_pdferr'] = 'ZAD Albo - Non è possibile convertire il file "%s" in formato PDF.';
$GLOBALS['TL_LANG']['tl_zad_albo']['log_published'] = 'ZAD Albo - E\' stato pubblicato il documento n. %s (id: %s)';
$GLOBALS['TL_LANG']['tl_zad_albo']['log_pubcanceled'] = 'ZAD Albo - E\' stato annullato il documento pubblicato con n. %s (id: %s)';
$GLOBALS['TL_LANG']['tl_zad_albo']['log_pubuncanceled'] = 'ZAD Albo - E\' stato ripristinato il documento pubblicato con n. %s (id: %s)';
$GLOBALS['TL_LANG']['tl_zad_albo']['log_archived'] = 'ZAD Albo - Sono stati archiviati i documenti con i seguenti id: %s';


/**
 * Labels
 */
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_categorylist'] = 'Scegli una categoria';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_categoryname'] = 'Categoria: %s';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentlist'] = 'Documenti caricati';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentadd'] = 'Crea un nuovo documento nella categoria "%s"';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentedit'] = 'Modifica il seguente documento appartenente alla categoria "%s".';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentdelete'] = 'Cancella il documento seguente';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentpublish'] = 'Pubblica il documento seguente';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_cancelpublished'] = 'Annulla la pubblicazione del documento seguente';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_uncancelpublished'] = 'Ripristina la pubblicazione del documento seguente';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_documentshow'] = 'Mostra i dettagli del documento';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_mandatory'] = 'Campo obbligatorio';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_subject'] = 'Oggetto';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceNumber'] = 'Numero di protocollo';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_referenceDate'] = 'Data di emissione';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_startDate'] = 'Data di pubblicazione';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_endDate'] = 'Data di archiviazione';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_unpublishDate'] = 'Data di rimozione';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_clearDate'] = 'Cancella data';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_todayDate'] = 'Oggi';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_document'] = 'Documento';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_attach'] = 'Allegati';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_state'] = 'Stato';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_statepub'] = 'Pubblicato (n. %s)';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_statedraft'] = 'Bozza';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_statecanc'] = 'Annullato (n. %s)';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_published'] = 'Pubblicato il %s';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_draft'] = 'Bozza del %s';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_canceled'] = '<strong>ANNULLATO</strong><br />';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_lastmodified'] = 'Ultima modifica';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_lastmodinfo'] = '%s (utente: %s)';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_dropzone'] = 'Clicca o trascina qui i file per caricarli';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_searchoptions'] = 'Imposta i criteri di ricerca';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_searchcategory'] = 'Categoria';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_searchanycategory'] = 'Qualsiasi';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_searchsubject'] = 'Oggetto';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_publishedlist'] = 'Elenco documenti pubblicati';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_number'] = 'Num. inserimento';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_category'] = 'Categoria';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_pubdate'] = 'Pubblicazione';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_pubdtinterval'] = 'Dal %s<br />al %s';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_pubcanceled'] = 'ANNULLATO PER ERRATA PUBBLICAZIONE';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_archivedlist'] = 'Elenco documenti archiviati';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_mandatorydesc'] = 'I campi contrassegnati con l\'asterisco sono obbligatori.';
$GLOBALS['TL_LANG']['tl_zad_albo']['lbl_filedownload'] = 'Scarica il file: %s';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_zad_albo']['but_save'] = array('Salva','Inserisce il nuovo documento');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_cancel'] = array('Annulla','Annulla l\'operazione in corso');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_confirm'] = array('Conferma','Conferma l\'operazione in corso');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_add'] = array('Nuovo','Inserisci un nuovo documento');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_show'] = array('Dettagli','Mostra i dettagli del documento');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_edit'] = array('Modifica','Modifica il documento');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_delete'] = array('Cancella','Cancella il documento');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_publish'] = array('Pubblica','Pubblica il documento all\'Albo');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_cancelpub'] = array('Annulla pubblicazione','Annulla la pubblicazione del documento all\'Albo');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_uncancelpub'] = array('Ripristina pubblicazione','Ripristina la pubblicazione del documento all\'Albo');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_catlist'] = array('Lista categorie','Scegli una nuova categoria');
$GLOBALS['TL_LANG']['tl_zad_albo']['but_search'] = 'Visualizza';
$GLOBALS['TL_LANG']['tl_zad_albo']['but_cancelupload'] = 'Annulla';
$GLOBALS['TL_LANG']['tl_zad_albo']['but_removefile'] = 'Cancella';

