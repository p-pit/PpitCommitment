<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;

require_once('vendor/TCPDF-master/tcpdf.php');

class PpitPDF extends \TCPDF {
	public $footer;
	public function Footer() {
		$context = Context::getCurrent();
		parent::Footer();
		$this->SetY(-10);
		$this->SetFont('helvetica', 'N', 8);
		if ($context->getConfig('headerParams')['footer']['type'] == 'text') $this->Cell(0, 5, $this->footer, 0, false, 'C', 0, '', 0, false, 'T', 'M');
		else {
			$img = file_get_contents('http://localhost/~bruno/p-pit.fr/public/img/FM%20Sports/bas-page.jpg');
			$this->Image('@' . $img, 20, 270, '', '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		}
	}
}
