<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;
use PpitCommitment\Model\Account;

class SsmlCommitmentViewHelper
{
	public static function formatXls($workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');

		$title = (isset ($context->getConfig('commitment/search')['title']) ? $context->getConfig('commitment/search')['title'][$context->getLocale()] : $translator->translate('Accounts', 'ppit-commitment', $context->getLocale()));
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-PIT')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		$i = 0;
		$colNames = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T');

		foreach($context->getConfig('commitment/update'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $unused) {
			$property = $context->getConfig('commitment'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
			if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
			$i++;
			$sheet->setCellValue($colNames[$i].'1', $property['labels'][$context->getLocale()]);
		}
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Name', 'ppit-core', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Product', 'ppit-commitment', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Unit price', 'ppit-commitment', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Quantity', 'ppit-commitment', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Amount', 'ppit-commitment', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Tax exempt share', 'ppit-master-data', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Tax 1 share', 'ppit-master-data', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Tax 2 share', 'ppit-master-data', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Tax 3 share', 'ppit-master-data', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Options', 'ppit-master-data', $context->getLocale()).' ('.$translator->translate('Tax exempt', 'ppit-master-data', $context->getLocale()).')');
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Options', 'ppit-master-data', $context->getLocale()).' ('.$translator->translate('Tax 1', 'ppit-master-data', $context->getLocale()).')');
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Options', 'ppit-master-data', $context->getLocale()).' ('.$translator->translate('Tax 2', 'ppit-master-data', $context->getLocale()).')');
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Options', 'ppit-master-data', $context->getLocale()).' ('.$translator->translate('Tax 3', 'ppit-master-data', $context->getLocale()).')');
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Total avec options', 'ppit-commitment', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Total (TVA standard)', 'ppit-commitment', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Total (TVA intermédiaire)', 'ppit-commitment', $context->getLocale()));
		$sheet->setCellValue($colNames[$i++].'1', $translator->translate('Total (TVA réduite)', 'ppit-commitment', $context->getLocale()));
		
		$j = 1;
		foreach ($view->commitments as $commitment) {
			
	    	$taxExemptAmount = $commitment->amount - $commitment->taxable_1_amount - $commitment->taxable_2_amount - $commitment->taxable_3_amount;
			$vatExemptOptions = 0;
			$vat1Options = 0;
			$vat2Options = 0;
			$vat3Options = 0;
			if (is_array($commitment->options)) foreach ($commitment->options as $option) {
    			if ($option['vat_id'] == 0) $vatExemptOptions += $option['amount'];
    			elseif ($option['vat_id'] == 1) $vat1Options += $option['amount'];
    			elseif ($option['vat_id'] == 2) $vat2Options += $option['amount'];
    			elseif ($option['vat_id'] == 3) $vat3Options += $option['amount'];
	    	}
	    	
	    	$j++;
			$i = 0;
			foreach($context->getConfig('commitment/update'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $unused) {
				$property = $context->getConfig('commitment'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
				if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
				$i++;
				if ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $context->decodeDate($commitment->properties[$propertyId]));
				elseif ($property['type'] == 'number') $sheet->setCellValue($colNames[$i].$j, $context->formatFloat($commitment->properties[$propertyId], 2));
				elseif ($property['type'] == 'select')  $sheet->setCellValue($colNames[$i].$j, (array_key_exists($commitment->properties[$propertyId], $property['modalities'])) ? $property['modalities'][$commitment->properties[$propertyId]][$context->getLocale()] : $commitment->properties[$propertyId]);
				else $sheet->setCellValue($colNames[$i].$j, $commitment->properties[$propertyId]);
			}
			$sheet->setCellValue($colNames[$i++].$j, $commitment->account_name);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->product_caption);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->unit_price);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->quantity);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->amount);
			$sheet->setCellValue($colNames[$i++].$j, $taxExemptAmount);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->taxable_1_amount);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->taxable_2_amount);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->taxable_3_amount);
			$sheet->setCellValue($colNames[$i++].$j, $vatExemptOptions);
			$sheet->setCellValue($colNames[$i++].$j, $vat1Options);
			$sheet->setCellValue($colNames[$i++].$j, $vat2Options);
			$sheet->setCellValue($colNames[$i++].$j, $vat3Options);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->including_options_amount);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->taxable_1_total);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->taxable_2_total);
			$sheet->setCellValue($colNames[$i++].$j, $commitment->taxable_3_total);
		}
		$i = 0;
		foreach($context->getConfig('commitment/update'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $property) {
			$i++;
			$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
		for ($j = $i; $j < $i + 17; $j++) {
			$sheet->getStyle($colNames[$j].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$j].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$j].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$j].'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($colNames[$j])->setAutoSize(true);
		}
	}
}