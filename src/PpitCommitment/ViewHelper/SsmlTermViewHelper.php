<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;

class SsmlTermViewHelper
{
	public static function formatXls($workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');

		$title = (isset ($context->getConfig('commitment/search')['title']) ? $context->getConfig('commitmentTerm/search')['title'][$context->getLocale()] : $this->translate('Terms', 'ppit-commitment', $context->getLocale()));
		
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

		$i++;
		$property = $context->getConfig('commitmentTerm'.(($view->type) ? '/'.$view->type: ''))['properties']['name'];
		if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
		$sheet->setCellValue($colNames[$i].'1', $property['labels'][$context->getLocale()]);
		$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
		$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
		$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
		$i++;
		$sheet->setCellValue($colNames[$i].'1', $translator->translate('Commitment', 'ppit-commitment', $context->getLocale()));
		$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
		$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
		$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
		foreach($context->getConfig('commitmentTerm/update'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $unused) {
			$property = $context->getConfig('commitmentTerm'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
			if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
			$i++;
			$sheet->setCellValue($colNames[$i].'1', $property['labels'][$context->getLocale()]);
			$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
		}

		$j = 1;
		foreach ($view->terms as $term) {
			$j++;
			$i = 0;
			$i++;
			$sheet->setCellValue($colNames[$i].$j, $term->properties['name']);
			$i++;
			$sheet->setCellValue($colNames[$i].$j, $term->commitment_caption);
			foreach($context->getConfig('commitmentTerm/update'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $unused) {
				$property = $context->getConfig('commitmentTerm'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
				if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
				$i++;
				if ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $term->properties[$propertyId]);
				elseif ($property['type'] == 'number') {
					$sheet->setCellValue($colNames[$i].$j, $term->properties[$propertyId]);
					$sheet->getStyle($colNames[$i].$j)->getNumberFormat()->setFormatCode('### ##0.00');
				}
				elseif ($property['type'] == 'select')  $sheet->setCellValue($colNames[$i].$j, (array_key_exists($term->properties[$propertyId], $property['modalities'])) ? $property['modalities'][$term->properties[$propertyId]][$context->getLocale()] : $term->properties[$propertyId]);
				else $sheet->setCellValue($colNames[$i].$j, $term->properties[$propertyId]);
			}
		}
		$i = 0;
		$i++;
		$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		$i++;
		$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		foreach($context->getConfig('commitmentTerm/update'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $property) {
			$i++;
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
	}
}