<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;
use PpitCommitment\Model\Account;

class SsmlAccountViewHelper
{
	public static function formatXls($workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');

		$title = (isset ($context->getInstance()->specifications['ppitCommitment']['account/search']['title']) ? $context->getInstance()->specifications['ppitCommitment']['account/search']['title'][$context->getLocale()] : $this->translate('Accounts', 'ppit-commitment', $context->getLocale()));
		
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
		$colNames = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M');
		
		foreach($context->getInstance()->specifications['ppitCommitment']['account_properties'] as $propertyId => $property) {
			$i++;
			$sheet->setCellValue($colNames[$i].'1', $property['labels'][$context->getLocale()]);
		}

		$j = 1;
		foreach ($view->accounts as $account) {
			$j++;
			$i = 0;
			foreach($context->getInstance()->specifications['ppitCommitment']['account_properties'] as $propertyId => $property) {
				$i++;
				if ($propertyId == 'customer_name') $sheet->setCellValue($colNames[$i].$j, $account->customer_name);
				elseif ($propertyId == 'place_id') $sheet->setCellValue($colNames[$i].$j, $view->places[$account->place_id]->name);
				elseif ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $context->decodeDate($account->properties[$propertyId]));
				elseif ($property['type'] == 'number') $sheet->setCellValue($colNames[$i].$j, $context->formatFloat($account->properties[$propertyId], 2));
				else $sheet->setCellValue($colNames[$i].$j, $account->properties[$propertyId]);
			}
		}
		$i = 0;
		foreach($context->getInstance()->specifications['ppitCommitment']['account_properties'] as $propertyId => $property) {
			$i++;
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
	}
}