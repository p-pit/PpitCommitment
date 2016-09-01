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

		$title = (isset ($context->getConfig('commitmentAccount/search')['title']) ? $context->getConfig('commitmentAccount/search')['title'][$context->getLocale()] : $this->translate('Accounts', 'ppit-commitment', $context->getLocale()));
		
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
		
		foreach($context->getConfig('commitmentAccount/update'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $unused) {
			$property = $context->getConfig('commitmentAccount'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
			if ($property['type'] == 'specific') $property = $context->getConfig($property['definition']);
			$i++;
			$sheet->setCellValue($colNames[$i].'1', $property['labels'][$context->getLocale()]);
		}

		$j = 1;
		foreach ($view->accounts as $account) {
			$j++;
			$i = 0;
		foreach($context->getConfig('commitmentAccount/update'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $unused) {
			$property = $context->getConfig('commitmentAccount'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
			if ($property['type'] == 'specific') $property = $context->getConfig($property['definition']);
				$i++;
				if ($propertyId == 'customer_name') $sheet->setCellValue($colNames[$i].$j, $account->customer_name);
				elseif ($propertyId == 'place_id') $sheet->setCellValue($colNames[$i].$j, $context->getConfig('student/property/place')['modalities'][$account->place_id][$context->getLocale()]);
				elseif ($propertyId == 'n_first') $sheet->setCellValue($colNames[$i].$j, $account->n_first);
				elseif ($propertyId == 'n_last') $sheet->setCellValue($colNames[$i].$j, $account->n_last);
				elseif ($propertyId == 'tel_work') $sheet->setCellValue($colNames[$i].$j, $account->tel_work);
				elseif ($propertyId == 'tel_cell') $sheet->setCellValue($colNames[$i].$j, $account->tel_cell);
				elseif ($propertyId == 'email') $sheet->setCellValue($colNames[$i].$j, $account->email);
				elseif ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $context->decodeDate($account->properties[$propertyId]));
				elseif ($property['type'] == 'number') $sheet->setCellValue($colNames[$i].$j, $context->formatFloat($account->properties[$propertyId], 2));
				else $sheet->setCellValue($colNames[$i].$j, $account->properties[$propertyId]);
			}
		}
		$i = 0;
		foreach($context->getConfig('commitmentAccount')['properties'] as $propertyId => $property) {
			$i++;
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
	}
}