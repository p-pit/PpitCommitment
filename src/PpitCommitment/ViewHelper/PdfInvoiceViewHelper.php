<?php
namespace PpitCommitment\ViewHelper;

use Zend\View\Model\ViewModel;
use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\Term;
use PpitCore\Model\Context;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitMasterData\Model\Product;
use PpitMasterData\Model\ProductOption;

require_once('vendor/TCPDF-master/tcpdf.php');

class PdfInvoiceViewHelper
{	
    public static function render($pdf, $invoice, $place, $proforma = true)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$invoiceSpecs = ($proforma) ? $context->getConfig('commitment/proforma') : $context->getConfig('commitment/invoice');

    	// create new PDF document
    	$pdf->footer = ($place->legal_footer) ? $place->legal_footer : $context->getConfig('headerParams')['footer']['value'];
    	$pdf->footer_2 = ($place->legal_footer_2) ? $place->legal_footer_2 : ((array_key_exists('footer_2', $context->getConfig('headerParams'))) ? $context->getConfig('headerParams')['footer_2']['value'] : null);
    	 
    	// set document information
    	$pdf->SetCreator(PDF_CREATOR);
    	$pdf->SetAuthor('P-Pit');
    	$pdf->SetTitle('Invoice');
    	$pdf->SetSubject('Invoice');
    	$pdf->SetKeywords('P-Pit, PDF, Invoice');
    	
    	// set default header data
		if ($place && $place->banner_src) $pdf->SetHeaderData($place->banner_src, ($place->banner_width) ? $place->banner_width : $context->getConfig('corePlace')['properties']['banner_width']['maxValue']);
		else $pdf->SetHeaderData('logos/'.$context->getInstance()->caption.'/'.$context->getConfig('headerParams')['advert'], $context->getConfig('headerParams')['advert-width']);
    	// set header and footer fonts
    	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
    	// set default monospaced font
    	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    	
    	// set margins
    	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    	 
    	// set auto page breaks
    	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    	
    	// set image scale factor
    	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    	
    	// set some language-dependent strings (optional)
    	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    		require_once(dirname(__FILE__).'/lang/eng.php');
    		$pdf->setLanguageArray($l);
    	}
    	
    	// set additional information
    	$info = array(
    			'Name' => 'Invoice',
    			'Location' => 'Office',
    			'Reason' => 'Invoice',
    			'ContactInfo' => 'https://www.p-pit.fr',
    	);
    	
    	// set font
    	$pdf->SetFont('helvetica', '', 12);
    	
    	// add a page
    	$pdf->AddPage();

    	// Invoice header
    	if (array_key_exists('header', $invoice)) {
    		$pdf->SetFont('', 'B', 8);
    		$pdf->writeHTML($invoice['header'], true, 0, true, 0);
    	}
    	 
    	$pdf->MultiCell(100, 5, '', 0, 'L', 0, 0, '', '', true);
    	$pdf->SetTextColor(0);
    	$pdf->SetFont('', '', 12);
    	
    	$addressee = "";
    	if (array_key_exists('customer_invoice_name', $invoice)) $addressee .= $invoice['customer_invoice_name']."\n";
    	if (array_key_exists('customer_n_fn', $invoice)) $addressee .= $invoice['customer_n_fn']."\n";
    	if (array_key_exists('customer_adr_street', $invoice)) $addressee .= $invoice['customer_adr_street']."\n";
    	if (array_key_exists('customer_adr_extended', $invoice)) $addressee .= $invoice['customer_adr_extended']."\n";
    	if (array_key_exists('customer_adr_post_office_box', $invoice)) $addressee .= $invoice['customer_adr_post_office_box']."\n";
    	if (array_key_exists('customer_adr_zip', $invoice) || array_key_exists('customer_city', $invoice)) {
	    	if (array_key_exists('customer_adr_zip', $invoice)) $addressee .= $invoice['customer_adr_zip'].' ';
	    	if (array_key_exists('customer_adr_city', $invoice)) $addressee .= $invoice['customer_adr_city'];
	    	$addressee .= "\n";
    	}
	    if (array_key_exists('customer_adr_state', $invoice)) $addressee .= $invoice['customer_adr_state']."\n";
	    if (array_key_exists('customer_adr_country', $invoice)) $addressee .= $invoice['customer_adr_country']."\n";
    	$pdf->MultiCell(80, 5, $addressee, 0, 'L', 0, 1, '', '', true);
    	$pdf->Ln(5);

    	if ($invoice['identifier']) $proforma = false; else $proforma = true;

    	// Title
    	$text = '<div style="text-align: center"><strong>';
    	if ($proforma) $text .= 'Facture proforma';
    	else $text .= 'Facture n° '.$invoice['identifier'];
    	$text .= ' au '.$context->decodeDate($invoice['date']);
    	$text .= '</strong></div>';
    	$pdf->writeHTML($text, true, 0, true, 0);
    	$pdf->Ln(5);

    	// Invoice references
		$pdf->SetFillColor(255, 255, 255);
    	$pdf->SetDrawColor(255, 255, 255);
    	$pdf->SetLineWidth(0.2);
    	$pdf->SetFont('', '', 9);
    	foreach($invoice['description'] as $line) {
    		$pdf->MultiCell(30, 5, '<strong>'.$line['title'].'</strong>', 1, 'L', 1, 0, '', '', true, 0, true);
    		$pdf->MultiCell(5, 5, ':', 1, 'L', 1, 0, '', '', true);
    		$pdf->MultiCell(145, 5, $line['value'], 1, 'L', 0, 1, '' ,'', true);
    	}

    	$taxComputing = ($invoice['tax'] == 'excluding') ? 'HT' : 'TTC';
    	
    	// Invoice lines
    	$pdf->Ln();
    	$pdf->SetDrawColor(0, 0, 0);
    	$pdf->SetFillColor(0, 97, 105);
    	$pdf->SetFont('', '', 8);
    	$pdf->SetTextColor(255);
    	$pdf->Cell(110, 7, 'Libellé', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'PU ('.$invoice['currency_symbol'].(($invoiceSpecs['tax']) ? ' '.$taxComputing : '').')', 1, 0, 'C', 1);
    	$pdf->Cell(20, 7, 'Quantité', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'Montant ('.$invoice['currency_symbol'].(($invoiceSpecs['tax']) ? ' '.$taxComputing : '').')', 1, 0, 'R', 1);
    	// Color and font restoration
    	$pdf->SetFillColor(239, 239, 239);
    	$pdf->SetTextColor(0);
    	$color = 0;
    	foreach ($invoice['lines'] as $line) {
    		$pdf->Ln();
    		$caption = $line['caption'];
    		if (!$proforma) {
    			if ($line['tax_rate'] == 0) $caption .= ' (exonéré)';
	    		else $caption .= ' (TVA '.sprintf('%d', round($line['tax_rate']*100, 1)).'%)';
    		}
    		$pdf->Cell(110, 6, $caption, 'LR', 0, 'L', $color);
    		$pdf->Cell(25, 6, $context->formatFloat($line['unit_price'], 2), 'LR', 0, 'R', $color);
    		$pdf->Cell(20, 6, $line['quantity'], 'LR', 0, 'C', $color);
    		$pdf->Cell(25, 6, $context->formatFloat($line['amount'], 2), 'LR', 0, 'R', $color);
    		$color = ($color+1)%2;
    	}

    	$pdf->Ln();
    	$pdf->Cell(180, 0, '', 'T');
    	$pdf->SetDrawColor(255, 255, 255);
    	if (!$proforma) {
    		$pdf->Ln();
    		$pdf->Cell(155, 6, 'Total HT :', 'LR', 0, 'R', false);
	    	$pdf->Cell(25, 6, $context->formatFloat($invoice['excluding_tax'], 2).' '.$invoice['currency_symbol'], 'LR', 0, 'R', false);
	    	if (array_key_exists('tax_1_amount', $invoice)) {
		    	$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 20% sur '.$context->formatFloat($invoice['taxable_1_total'], 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($invoice['tax_1_amount'], 2).' '.$invoice['currency_symbol'], 'LR', 0, 'R', false);
	    	}
	    	if (array_key_exists('tax_2_amount', $invoice)) {
	    		$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 10% sur '.$context->formatFloat($invoice['taxable_2_total'], 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($invoice['tax_2_amount'], 2).' '.$invoice['currency_symbol'], 'LR', 0, 'R', false);
	    	}
	    	if (array_key_exists('tax_3_amount', $invoice)) {
	    		$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 5,5% sur '.$context->formatFloat($invoice['taxable_3_total'], 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($invoice['tax_3_amount'], 2).' '.$invoice['currency_symbol'], 'LR', 0, 'R', false);
	    	}
    	}
    	$pdf->Ln();
    	$pdf->SetFont('', 'B');
    	$pdf->Cell(155, 6, 'Total '.(($invoiceSpecs['tax']) ? 'TTC ' : '').':', 'LR', 0, 'R', false);
    	$pdf->Cell(25, 6, $context->formatFloat($invoice['tax_inclusive'], 2).' '.$invoice['currency_symbol'], 'LR', 0, 'R', false);

    	// Terms
    	if (array_key_exists('terms', $invoice)) {
		    $pdf->Ln();
		    $text = '<strong>Echéancier</strong>';
		    $pdf->writeHTML($text, true, 0, true, 0);
	    	$pdf->Ln();
	    	$pdf->SetDrawColor(0, 0, 0);
	    	$pdf->SetFillColor(0, 97, 105);
	    	$pdf->SetFont('', '', 8);
	    	$pdf->SetTextColor(255);
	    	$pdf->Cell(60, 7, 'Echéance', 1, 0, 'C', 1);
	    	$pdf->Cell(30, 7, 'Prévue le', 1, 0, 'C', 1);
	    	$pdf->Cell(30, 7, 'Statut', 1, 0, 'C', 1);
	    	$pdf->Cell(30, 7, 'Réglée le', 1, 0, 'C', 1);
	    	$pdf->Cell(30, 7, 'Montant', 1, 0, 'R', 1);
	    	// Color and font restoration
	    	$pdf->SetFillColor(239, 239, 239);
	    	$pdf->SetTextColor(0);
	    	// Data
	    	foreach($invoice['terms'] as $term) {
	    		if ($term['status'] == 'expected' && $term['due_date'] < $invoice['date']) $pdf->SetTextColor(255, 0, 0); else $pdf->SetTextColor(0);
		    	$pdf->Ln();
		    	$pdf->Cell(60, 6, $term['caption'], 'LR', 0, 'L', $color);
		    	$pdf->Cell(30, 6, $context->decodeDate($term['due_date']), 'LR', 0, 'C', $color);
		    	$status = $context->getConfig('commitmentTerm')['properties']['status']['modalities'][$term['status']][$context->getLocale()];
		    	$pdf->Cell(30, 6, $status, 'LR', 0, 'C', $color);
		    	$pdf->Cell(30, 6, $context->decodeDate($term['settlement_date']).(($term['settlement_date'] && $term['means_of_payment']) ? ' ('.$context->localize($context->getConfig('commitmentTerm')['properties']['means_of_payment']['modalities'][$term['means_of_payment']]).')' : ''), 'LR', 0, 'L', $color);
		    	$pdf->Cell(30, 6, $context->formatFloat($term['amount'], 2), 'LR', 0, 'R', $color);
		    	$color = ($color+1)%2;
	    	}
	    	$pdf->Ln();
	    	$pdf->Cell(180, 0, '', 'T');
	
	    	$pdf->SetTextColor(0);
	    	$pdf->SetFont('', 'B');
    	}

		$pdf->Ln();
		$pdf->SetDrawColor(255, 255, 255);
		$pdf->Cell(155, 6, 'Total réglé :', 'LR', 0, 'R', false);
		$pdf->Cell(25, 6, $context->formatFloat($invoice['settled_amount'], 2).' '.$invoice['currency_symbol'], 'LR', 0, 'R', false);

		$pdf->Ln();
		$pdf->SetDrawColor(255, 255, 255);
		$pdf->Cell(155, 6, 'Restant dû :', 'LR', 0, 'R', false);
		$pdf->Cell(25, 6, $context->formatFloat($invoice['still_due'], 2).' '.$invoice['currency_symbol'], 'LR', 0, 'R', false);

    	$pdf->Ln();

    	if (array_key_exists('tax_mention', $invoice)) {
	    	$pdf->SetFont('', 'B', 10);
	    	$pdf->Ln();
	    	$pdf->writeHTML($invoice['tax_mention'], true, 0, true, 0);
    	}

    	if (array_key_exists('bank_details', $invoice)) {
	    	$pdf->SetFont('', '', 8);
	    	$pdf->Ln();
	    	$pdf->SetDrawColor(0, 0, 0);
	    	$pdf->writeHTML($invoice['bank_details'], true, 0, true, 0);
    	}

    	if (array_key_exists('footer_mention_1', $invoice)) {
	    	$pdf->SetFont('', '', 10);
	    	$pdf->Ln();
	    	$pdf->writeHTML($invoice['footer_mention_1'], true, 0, true, 0);
    	}

    	if (array_key_exists('footer_mention_2', $invoice)) {
    		$pdf->SetFont('', '', 10);
    		$pdf->Ln();
    		$pdf->writeHTML($invoice['footer_mention_2'], true, 0, true, 0);
    	}

    	if (array_key_exists('footer_mention_3', $invoice)) {
    		$pdf->SetFont('', '', 10);
    		$pdf->Ln();
    		$pdf->writeHTML($invoice['footer_mention_3'], true, 0, true, 0);
    	}
    	 
    	// Close and output PDF document
    	// This method has several options, check the source code documentation for more information.
    	return $pdf;
    }
}
