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
		$colNames = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z', 27 => 'AA', 28 => 'AB', 29 => 'AC', 30 => 'AD', 31 => 'AE', 32 => 'AF', 33 => 'AG', 34 => 'AH', 35 => 'AI', 36 => 'AJ', 37 => 'AK', 38 => 'AL', 39 => 'AM', 40 => 'AN');
		
		foreach($context->getConfig('commitmentAccount/export'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $unused) {
			$property = $context->getConfig('commitmentAccount'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
			if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
			$i++;
			$sheet->setCellValue($colNames[$i].'1', $property['labels'][$context->getLocale()]);
		}

		$j = 1;
		foreach ($view->accounts as $account) {
			$j++;
			$i = 0;
			foreach($context->getConfig('commitmentAccount/export'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $unused) {
				$property = $context->getConfig('commitmentAccount'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
				if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
				$i++;
				if ($propertyId == 'customer_name') $sheet->setCellValue($colNames[$i].$j, $account->customer_name);
				elseif ($propertyId == 'place_id') $sheet->setCellValue($colNames[$i].$j, $account->place_caption);
				elseif ($propertyId == 'n_first') $sheet->setCellValue($colNames[$i].$j, $account->n_first);
				elseif ($propertyId == 'n_last') $sheet->setCellValue($colNames[$i].$j, $account->n_last);
				elseif ($propertyId == 'tel_work') $sheet->setCellValue($colNames[$i].$j, $account->tel_work);
				elseif ($propertyId == 'tel_cell') $sheet->setCellValue($colNames[$i].$j, $account->tel_cell);
				elseif ($propertyId == 'email') $sheet->setCellValue($colNames[$i].$j, $account->email);
				elseif ($propertyId == 'birth_date') $sheet->setCellValue($colNames[$i].$j, $context->decodeDate($account->birth_date));
				elseif ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $context->decodeDate($account->properties[$propertyId]));
				elseif ($property['type'] == 'number') $sheet->setCellValue($colNames[$i].$j, $context->formatFloat($account->properties[$propertyId], 2));
				elseif ($property['type'] == 'select')  $sheet->setCellValue($colNames[$i].$j, (array_key_exists($account->properties[$propertyId], $property['modalities'])) ? $property['modalities'][$account->properties[$propertyId]][$context->getLocale()] : $account->properties[$propertyId]);
				else $sheet->setCellValue($colNames[$i].$j, $account->properties[$propertyId]);
			}
		}
		$i = 0;
		foreach($context->getConfig('commitmentAccount/export'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $property) {
			$i++;
			$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
	}
}