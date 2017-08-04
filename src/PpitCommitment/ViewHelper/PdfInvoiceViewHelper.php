<?php
namespace PpitCommitment\ViewHelper;

use Zend\View\Model\ViewModel;
use PpitCommitment\Model\Account;
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
    public static function render($pdf, $commitment, $proforma = null)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
		$place = Place::get($commitment->account->place_id);
    	$specsId = ($proforma) ? 'commitment/proforma' : 'commitment/invoice';
    	$type = $commitment->type;
    	if ($context->getConfig($specsId.(($type) ? '/'.$type : ''))) $invoiceSpecs = $context->getConfig($specsId.(($type) ? '/'.$type : ''));
    	else $invoiceSpecs = $context->getConfig($specsId);

    	if (!$commitment->invoice_date) $commitment->properties['invoice_date'] = date('Y-m-d');
    	
    	// create new PDF document
    	$pdf->footer = ($place->legal_footer) ? $place->legal_footer : $context->getConfig('headerParams')['footer']['value'];
    	
    	// set document information
    	$pdf->SetCreator(PDF_CREATOR);
    	$pdf->SetAuthor('P-PIT');
    	$pdf->SetTitle('Invoice');
    	$pdf->SetSubject('TCPDF Tutorial');
    	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    	
    	// set default header data
		if ($place && $place->logo_src) $pdf->SetHeaderData($place->logo_src, $context->getConfig('headerParams')['advert-width']);
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
    	
    	// ---------------------------------------------------------
    	
    	/*
    	 NOTES:
    	 - To create self-signed signature: openssl req -x509 -nodes -days 365000 -newkey rsa:1024 -keyout tcpdf.crt -out tcpdf.crt
    	 - To export crt to p12: openssl pkcs12 -export -in tcpdf.crt -out tcpdf.p12
    	 - To convert pfx certificate to pem: openssl pkcs12 -in tcpdf.pfx -out tcpdf.crt -nodes
    	 */
    	
    	// set certificate file
    	$certificate = 'file://vendor/TCPDF-master/examples/data/cert/tcpdf.crt';
    	
    	// set additional information
    	$info = array(
    			'Name' => 'Invoice',
    			'Location' => 'Office',
    			'Reason' => 'Invoice',
    			'ContactInfo' => 'https://www.p-pit.fr',
    	);
    	
    	// set document signature
//    	$pdf->setSignature($certificate, $certificate, 'tcpdfdemo', '', 2, $info);
    	
    	// set font
    	$pdf->SetFont('helvetica', '', 12);
    	
    	// add a page
    	$pdf->AddPage();
    	 
    	// Invoice header
    	$pdf->MultiCell(100, 5, '', 0, 'L', 0, 0, '', '', true);
    	$pdf->SetTextColor(0);
    	$pdf->SetFont('', '', 12);
    	
    	$addressee = "\n"."\n";
		if ($proforma) $commitment->computeHeader($proforma);
    	if ($commitment->customer_invoice_name) $addressee .= $commitment->customer_invoice_name."\n";
    	if ($commitment->customer_n_fn) $addressee .= $commitment->customer_n_fn."\n";
    	if ($commitment->customer_adr_street) $addressee .= $commitment->customer_adr_street."\n";
    	if ($commitment->customer_adr_extended) $addressee .= $commitment->customer_adr_extended."\n";
    	if ($commitment->customer_adr_post_office_box) $addressee .= $commitment->customer_adr_post_office_box."\n";
    	if ($commitment->customer_adr_zip || $commitment->customer_adr_city) {
    		if ($commitment->customer_adr_zip) $addressee .= $commitment->customer_adr_zip." ";
	    	if ($commitment->customer_adr_city) $addressee .= $commitment->customer_adr_city;
    		$addressee .= "\n";
    	}
    	if ($commitment->customer_adr_state) $addressee .= $commitment->customer_adr_state."\n";
    	if ($commitment->customer_adr_country) $addressee .= $commitment->customer_adr_country."\n";
    	$pdf->MultiCell(80, 5, $addressee, 0, 'L', 0, 1, '', '', true);
    	$pdf->Ln(5);

    	// Title
    	if ($proforma) $text = '<div style="text-align: center"><strong>Facture proforma'.'</strong></div>';
    	else $text = '<div style="text-align: center"><strong>Facture n° '.$commitment->invoice_identifier.'</strong></div>';
    	$pdf->writeHTML($text, true, 0, true, 0);
    	$pdf->Ln(5);

    	// Invoice references
		$pdf->SetFillColor(255, 255, 255);
//    	$pdf->SetTextColor(255);
    	$pdf->SetDrawColor(255, 255, 255);
//    	$pdf->SetDrawColor(128, 0, 0);
    	$pdf->SetLineWidth(0.2);
    	$pdf->SetFont('', '', 9);
    	foreach($invoiceSpecs['description'] as $line) {
    		$arguments = array();
    		foreach($line['params'] as $propertyId) {
    			if ($propertyId == 'date') $arguments[] = $context->decodeDate(date('Y-m-d'));
    			else {
    			    if (array_key_exists($propertyId, $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'])) {
						$property = $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
					}
					else {
						$property = $context->getConfig('commitment')['properties'][$propertyId];
					}
    				if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
	    			if ($propertyId == 'account_name') $arguments[] = $commitment->account_name;
	    			elseif ($propertyId == 'caption') $arguments[] = $commitment->caption;
	    			elseif ($property['type'] == 'date') $arguments[] = $context->decodeDate($commitment->properties[$propertyId]);
	    			elseif ($property['type'] == 'number') $arguments[] = $context->formatFloat($commitment->properties[$propertyId], 2);
	    			elseif ($property['type'] == 'select' && array_key_exists($commitment->properties[$propertyId], $property['modalities'])) $arguments[] = $property['modalities'][$commitment->properties[$propertyId]][$context->getLocale()];
	    			else $arguments[] = $commitment->properties[$propertyId];
    			}
    		}
    		$value = vsprintf($line['right'][$context->getLocale()], $arguments);
    		if ($value) {
	    		$pdf->MultiCell(30, 5, '<strong>'.$line['left'][$context->getLocale()].'</strong>', 1, 'L', 1, 0, '', '', true, 0, true);
	    		$pdf->MultiCell(5, 5, ':', 1, 'L', 1, 0, '', '', true);
	    		$pdf->MultiCell(145, 5, $value, 1, 'L', 0, 1, '' ,'', true);
    		}
    	}
    	 
    	// Invoice lines
    	$pdf->Ln();
    	$pdf->SetDrawColor(0, 0, 0);
    	$pdf->SetFillColor(0, 97, 105);
    	$pdf->SetFont('', '', 8);
//    	$pdf->SetFillColor(196, 196, 196);
    	$pdf->SetTextColor(255);
    	$currencySymbol = $context->getConfig('commitment/'.$type)['currencySymbol'];
    	$taxComputing = (($context->getConfig('commitment/'.$type)['tax'] == 'excluding') ? 'HT' : 'TTC');
    	$pdf->Cell(110, 7, 'Libellé', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'PU ('.$currencySymbol.' '.$taxComputing.')', 1, 0, 'C', 1);
    	$pdf->Cell(20, 7, 'Quantité', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'Montant ('.$currencySymbol.' '.$taxComputing.')', 1, 0, 'R', 1);
    	// Color and font restoration
    	$pdf->SetFillColor(239, 239, 239);
    	$pdf->SetTextColor(0);
    	// Data
    	$product = Product::get($commitment->product_identifier, 'reference');
    	$taxExemptAmount = $commitment->amount - $commitment->taxable_1_amount - $commitment->taxable_2_amount - $commitment->taxable_3_amount;
    	$color = 0;
    	if ($commitment->product_caption) $product_caption = $commitment->product_caption;
    	else $product_caption = $commitment->description;
    	if ($proforma) {
    		$pdf->Ln();
    		$pdf->Cell(110, 6, $product_caption, 'LR', 0, 'L', $color);
    		$pdf->Cell(25, 6, $context->formatFloat($commitment->unit_price, 2), 'LR', 0, 'R', $color);
    		$pdf->Cell(20, 6, $commitment->quantity, 'LR', 0, 'C', $color);
    		$pdf->Cell(25, 6, $context->formatFloat($commitment->amount, 2), 'LR', 0, 'R', $color);
    		$color = ($color+1)%2;
    	}
    	else {
	    	if ($commitment->taxable_1_amount != 0) {
	    		$pdf->Ln();
	    		$pdf->Cell(110, 6, $product_caption.' (TVA 20%)', 'LR', 0, 'L', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->taxable_1_amount / $commitment->quantity, 2), 'LR', 0, 'R', $color);
		    	$pdf->Cell(20, 6, $commitment->quantity, 'LR', 0, 'C', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->taxable_1_amount, 2), 'LR', 0, 'R', $color);
	    		$color = ($color+1)%2;
	    	}
	        if ($commitment->taxable_2_amount != 0) {
	    		$pdf->Ln();
	        	$pdf->Cell(110, 6, $product_caption.' (TVA 10%)', 'LR', 0, 'L', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->taxable_2_amount / $commitment->quantity, 2), 'LR', 0, 'R', $color);
		    	$pdf->Cell(20, 6, $commitment->quantity, 'LR', 0, 'C', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->taxable_2_amount, 2), 'LR', 0, 'R', $color);
	    		$color = ($color+1)%2;
	    	}
	        if ($commitment->taxable_3_amount != 0) {
	    		$pdf->Ln();
	        	$pdf->Cell(110, 6, $product_caption.' (TVA 5,5%)', 'LR', 0, 'L', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->taxable_3_amount / $commitment->quantity, 2), 'LR', 0, 'R', $color);
		    	$pdf->Cell(20, 6, $commitment->quantity, 'LR', 0, 'C', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->taxable_3_amount, 2), 'LR', 0, 'R', $color);
	    		$color = ($color+1)%2;
	    	}
	        if ($taxExemptAmount != 0) {
	    		$pdf->Ln();
	        	$pdf->Cell(110, 6, $product_caption.' (exonéré)', 'LR', 0, 'L', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxExemptAmount, 2), 'LR', 0, 'R', $color);
		    	$pdf->Cell(20, 6, $commitment->quantity, 'LR', 0, 'C', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxExemptAmount * $commitment->quantity, 2), 'LR', 0, 'R', $color);
	    		$color = ($color+1)%2;
	    	}
    	}
    	
    	if (is_array($commitment->options)) foreach ($commitment->options as $option) {
    		$pdf->Ln();
    		$caption = $option['caption'];
    		if (!$proforma) {
	    		if ($option['vat_id'] == 0) $taxCaption = ' (exonéré)';
	    		elseif ($option['vat_id'] == 1) $taxCaption = ' (TVA 20%)';
	    		elseif ($option['vat_id'] == 2) $taxCaption = ' (TVA 10%)';
	    		elseif ($option['vat_id'] == 3) $taxCaption = ' (TV 5,5%)';
	    		$caption .= $taxCaption;
    		}
    		$pdf->Cell(110, 6, $caption, 'LR', 0, 'L', $color);
    		$pdf->Cell(25, 6, $context->formatFloat($option['unit_price'], 2), 'LR', 0, 'R', $color);    		
    		$pdf->Cell(20, 6, $option['quantity'], 'LR', 0, 'C', $color);    		
    		$pdf->Cell(25, 6, $context->formatFloat($option['amount'], 2), 'LR', 0, 'R', $color);
    		$color = ($color+1)%2;
    	}

    	$pdf->Ln();
    	$pdf->Cell(180, 0, '', 'T');
    	$pdf->SetDrawColor(255, 255, 255);
    	if (!$proforma) {
    		$pdf->Ln();
    		$pdf->Cell(155, 6, 'Total HT :', 'LR', 0, 'R', false);
	    	$pdf->Cell(25, 6, $context->formatFloat($commitment->excluding_tax, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
	    	if ($commitment->tax_1_amount != 0) {
		    	$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 20% sur '.$context->formatFloat($commitment->taxable_1_total, 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->tax_1_amount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
	    	}
	        if ($commitment->tax_2_amount != 0) {
		    	$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 10% sur '.$context->formatFloat($commitment->taxable_2_total, 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->tax_2_amount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
	    	}
	        if ($commitment->tax_3_amount != 0) {
		    	$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 5,5% sur '.$context->formatFloat($commitment->taxable_3_total, 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($commitment->tax_3_amount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
	    	}
    	}
    	$pdf->Ln();
    	$pdf->SetFont('', 'B');
    	$pdf->Cell(155, 6, 'Total TTC :', 'LR', 0, 'R', false);
    	$pdf->Cell(25, 6, $context->formatFloat($commitment->tax_inclusive, 2).' '.$currencySymbol, 'LR', 0, 'R', false);

    	// Terms
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
    	$terms = Term::getList(array('commitment_id' => $commitment->id), 'due_date', 'ASC', 'search');
    	$settledAmount = 0;
    	$color = 0;
    	foreach($terms as $term) {
    		if ($term->status == 'settled' || $term->status == 'collected') $settledAmount += $term->amount;
    		if ($term->status == 'expected' && $term->due_date < date('Y-m-d')) $pdf->SetTextColor(255, 0, 0); else $pdf->SetTextColor(0);
    		$meansOfPayment = ($term->means_of_payment) ? $context->getConfig('commitmentTerm')['properties']['means_of_payment']['modalities'][$term->means_of_payment][$context->getLocale()] : '';
	    	$pdf->Ln();
	    	$pdf->Cell(60, 6, $term->caption, 'LR', 0, 'L', $color);
	    	$pdf->Cell(30, 6, $context->decodeDate($term->due_date), 'LR', 0, 'C', $color);
	    	if ($term->status == 'collected') $term->status = 'settled';
	    	$status = $context->getConfig('commitmentTerm')['properties']['status']['modalities'][$term->status][$context->getLocale()];
	    	$pdf->Cell(30, 6, $status, 'LR', 0, 'C', $color);
	    	$pdf->Cell(30, 6, $context->decodeDate($term->settlement_date).(($meansOfPayment) ? ' ('.$meansOfPayment.')' : ''), 'LR', 0, 'L', $color);
	    	$pdf->Cell(30, 6, $context->formatFloat($term->amount, 2), 'LR', 0, 'R', $color);
	    	$color = ($color+1)%2;
    	}
    	$pdf->Ln();
    	$pdf->Cell(180, 0, '', 'T');

    	$pdf->SetTextColor(0);
    	$pdf->SetFont('', 'B');

    	$pdf->Ln();
    	$pdf->SetDrawColor(255, 255, 255);
    	$pdf->Cell(155, 6, 'Total réglé :', 'LR', 0, 'R', false);
    	$pdf->Cell(25, 6, $context->formatFloat($settledAmount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);

    	$pdf->Ln();
    	$pdf->SetDrawColor(255, 255, 255);
    	$pdf->Cell(155, 6, 'Restant dû :', 'LR', 0, 'R', false);
    	$pdf->Cell(25, 6, $context->formatFloat($commitment->tax_inclusive - $settledAmount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);

    	$pdf->Ln();

    	$pdf->SetFont('', 'B', 10);
    	$text = $context->getConfig('commitment/invoice_tax_mention');
    	$pdf->Ln();
    	$pdf->writeHTML($text, true, 0, true, 0);

    	if ($commitment->status != 'settled' && $context->getConfig('commitment/invoice_bank_details')) {
	    	$pdf->SetFont('', '', 8);
	    	$pdf->Ln();
	    	$pdf->SetDrawColor(0, 0, 0);
	    	$text = $context->getConfig('commitment/invoice_bank_details');
	    	$pdf->writeHTML($text, true, 0, true, 0);
    	}

    	if ($commitment->status != 'settled' && $context->getConfig('commitment/invoice_footer_mention_1')) {
	    	$pdf->SetFont('', '', 10);
	    	$text = $context->getConfig('commitment/invoice_footer_mention_1');
	    	$pdf->Ln();
	    	$pdf->writeHTML($text, true, 0, true, 0);
    	}

    	if ($commitment->status != 'settled' && $context->getConfig('commitment/invoice_footer_mention_2')) {
	    	$pdf->SetFont('', '', 10);
    		$text = $context->getConfig('commitment/invoice_footer_mention_2');
	    	$pdf->Ln();
	    	$pdf->writeHTML($text, true, 0, true, 0);
    	}

		if ($commitment->status != 'settled' && $context->getConfig('commitment/invoice_footer_mention_3')) {
	    	$pdf->SetFont('', '', 10);
			$text = $context->getConfig('commitment/invoice_footer_mention_3');
	    	$pdf->Ln();
	    	$pdf->writeHTML($text, true, 0, true, 0);
		}
    	 
    	// Close and output PDF document
    	// This method has several options, check the source code documentation for more information.
    	return $pdf;
    }
}
