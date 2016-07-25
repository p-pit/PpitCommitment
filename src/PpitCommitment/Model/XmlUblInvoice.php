<?php
namespace Ugap\Model;

class XmlUblInvoice
{
	public $content;

	static $template =
	<<<XML
<?xml version="1.0" encoding="UTF-8"?> 
<Invoice xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:cur="urn:un:unece:uncefact:codelist:specification:54217:2001" xmlns:uni="urn:un:unece:uncefact:codelist:specification:66411:2001" xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 ./xsd/maindoc/UBL-Invoice-2.0.xsd">
	<cbc:ID>[1]</cbc:ID>
	<cbc:CopyIndicator>false</cbc:CopyIndicator>
	<cbc:UUID>[3]</cbc:UUID>	
	<cbc:IssueDate>[4]</cbc:IssueDate>
	<cbc:InvoiceTypeCode name="Facture">380</cbc:InvoiceTypeCode>
	<cbc:Note>[6]</cbc:Note>
	<cbc:DocumentCurrencyCode>EUR</cbc:DocumentCurrencyCode>
	<cbc:LineCountNumeric>1</cbc:LineCountNumeric>
	<cac:ContractDocumentReference>
		<cbc:ID>[13]</cbc:ID>
		<cbc:DocumentTypeCode>Bon de commande</cbc:DocumentTypeCode>
	</cac:ContractDocumentReference>
	<cac:AccountingSupplierParty>
		<cac:Party>
			<cac:PartyIdentification>
				<cbc:ID schemeName="SIRET">602 055 311 02533</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name>XEROX S.A.S.</cbc:Name>
			</cac:PartyName>
			<cac:PostalAddress>
				<cbc:CityName>Roissy Charles de Gaulle Cedex</cbc:CityName>
				<cbc:PostalZone>95926</cbc:PostalZone>
				<cac:AddressLine>
					<cbc:Line>immeuble "Exelmans" – 33, rue des Vanesses</cbc:Line>
				</cac:AddressLine>
				<cac:AddressLine>
					<cbc:Line>CS30026 – Villepinte</cbc:Line>
				</cac:AddressLine>
				<cac:Country>
					<cbc:IdentificationCode>FR</cbc:IdentificationCode>
				</cac:Country>
			</cac:PostalAddress>
			<cac:PartyTaxScheme>
				<cbc:CompanyID schemeName="Num TVA intra-communautaire">FR46 602 055 311</cbc:CompanyID>
				<cac:TaxScheme>
					<cbc:TaxTypeCode>TVA DEBIT</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:PartyTaxScheme>
			<cac:PartyLegalEntity>
				<cbc:RegistrationName>XEROX S.A.S.</cbc:RegistrationName>
				<cbc:CompanyID>Bobigny B 602 055 311</cbc:CompanyID>
				<cac:RegistrationAddress>
					<cbc:CityName></cbc:CityName>
					<cbc:PostalZone></cbc:PostalZone>
					<cac:AddressLine>
						<cbc:Line></cbc:Line>
					</cac:AddressLine>					
					<cac:AddressLine>
						<cbc:Line></cbc:Line>
					</cac:AddressLine>				
					<cac:Country>
						<cbc:IdentificationCode>FR</cbc:IdentificationCode>
					</cac:Country>
				</cac:RegistrationAddress>
				<cac:CorporateRegistrationScheme>
					<cbc:ID>143.524.185 €</cbc:ID>
				</cac:CorporateRegistrationScheme>
			</cac:PartyLegalEntity>
			<cac:Contact>
				<cbc:ID></cbc:ID>
				<cbc:Name></cbc:Name>
				<cbc:Telephone></cbc:Telephone>
				<cbc:ElectronicMail></cbc:ElectronicMail>
			</cac:Contact>
		</cac:Party>
	</cac:AccountingSupplierParty>
	<cac:AccountingCustomerParty>
		<cac:Party>
			<cac:PartyIdentification>
				<cbc:ID schemeName="SIREN">77605646700587</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name>UGAP</cbc:Name>
			</cac:PartyName>
			<cac:PostalAddress>
				<cbc:CityName>CHAMPS SUR MARNE</cbc:CityName>
				<cbc:PostalZone>77420</cbc:PostalZone>
				<cac:AddressLine>
					<cbc:Line>1 BOULEVARD ARCHIMEDE</cbc:Line>
				</cac:AddressLine>
				<cac:Country>
					<cbc:IdentificationCode>FR</cbc:IdentificationCode>
				</cac:Country>
			</cac:PostalAddress>
			<cac:PartyTaxScheme>
				<cbc:CompanyID schemeName="Num TVA intra-communautaire">FR51776056467</cbc:CompanyID>
				<cac:TaxScheme>
					<cbc:Name>DIRECTION FINANCIERE ET COMPTABLE - DEPARTEMENT FOURNISSEURS</cbc:Name>
				</cac:TaxScheme>
			</cac:PartyTaxScheme>
		</cac:Party>
	</cac:AccountingCustomerParty>
</Invoice>
XML;
	
	public function __construct()
	{
		$this->content = new \SimpleXMLElement(XmlUblInvoice::$template);
	}
	
	public function asXML(){
		return $this->content->asXML();
	}

	public function setID($value) // [1]
	{
		$global = $this->content;
		$namespaces = $global->getNameSpaces(true);
		$cbc = $global->children($namespaces['cbc']);
		$cbc->ID = $value;
	}
	
	public function setUUID($value) // [3]
	{
		$global = $this->content;
		$namespaces = $global->getNameSpaces(true);
		$cbc = $global->children($namespaces['cbc']);
		$cbc->UUID = $value;
	}

	public function setIssueDate($value) // [4]
	{
		$global = $this->content;
		$namespaces = $global->getNameSpaces(true);
		$cbc = $global->children($namespaces['cbc']);
		$cbc->IssueDate = $value;
	}

	public function setNote($value) // [4]
	{
		$global = $this->content;
		$namespaces = $global->getNameSpaces(true);
		$cbc = $global->children($namespaces['cbc']);
		$cbc->Note = $value;
	}
	
	public function setContractDocumentReference($value) // [13]
	{
		$global = $this->content;
		$namespaces = $global->getNameSpaces(true);
		$cac = $global->children($namespaces['cac']);
		$namespaces = $cac->ContractDocumentReference->getNameSpaces(true);
		$cbc = $cac->ContractDocumentReference->children($namespaces['cbc']);
		$cbc->ID = $value;
	}

	public function setDelivery($id, $description, $cityName, $postalZone, $addressLine1 = null, $addressLine2 = null, $addressLine3 = null, $country = null) // [13]
	{
		$global = $this->content;		
		$delivery = $global->addChild('xmlns:cac:Delivery');
		
		// ID
		if ($id) $delivery->addChild('xmlns:cbc:ID', $id);
		
		$deliveryLocation = $delivery->addChild('xmlns:cac:DeliveryLocation');
		
		// Description
		if ($description) $deliveryLocation->addChild('xmlns:cbc:Description', $description);
		
		// Address
		$address = $deliveryLocation->addChild('xmlns:cac:Address');
		if ($cityName) $address->addChild('xmlns:cbc:CityName', $cityName);
		if ($postalZone) $address->addChild('xmlns:cbc:PostalZone', $postalZone);
		if ($addressLine1) {
			$addressLine = $address->addChild('xmlns:cac:AddressLine');
			$addressLine->addChild('xmlns:cbc:Line', $addressLine1);
		}
		if ($addressLine2) {
			$addressLine = $address->addChild('xmlns:cac:AddressLine');
			$addressLine->addChild('xmlns:cbc:Line', $addressLine2);
		}
		if ($addressLine3) {
			$addressLine = $address->addChild('xmlns:cac:AddressLine');
			$addressLine->addChild('xmlns:cbc:Line', $addressLine3);
		}
		if ($country) {
			$country = $address->addChild('xmlns:cac:Country');
			$country->addChild('xmlns:cbc:IdentificationCode', $country);
		}
	}

	public function setPaymentMeans($paymentDueDate, $instructionNote, $payeeFinancialAccount)
	{
		$global = $this->content;
		$paymentMeans = $global->addChild('xmlns:cac:PaymentMeans');
		$paymentMeansCode = $paymentMeans->addChild('xmlns:cbc:PaymentMeansCode', '31');
		$paymentMeansCode->addAttribute('listID', 'UN/ECE 4461 Subset ');
		$paymentMeansCode->addAttribute('listAgencyID', 'NES ');
		$paymentMeansCode->addAttribute('listAgencyName', 'Northern European Subset ');
		$paymentMeans->addChild('xmlns:cbc:PaymentDueDate', $paymentDueDate);
		$paymentMeans->addChild('xmlns:cbc:PaymentChannelCode', 'IBAN');
		$paymentMeans->addChild('xmlns:cbc:InstructionNote', $instructionNote);
		$payeeFinancialAccount = $paymentMeans->addChild('xmlns:cac:PayeeFinancialAccount');
		$payeeFinancialAccount->addChild('xmlns:cbc:ID', $payeeFinancialAccount);
	}

	public function setPaymentTerms($note)
	{
		$global = $this->content;
		$paymentTerms = $global->addChild('xmlns:cac:PaymentTerms');
		$paymentTerms->addChild('xmlns:cbc:Note', $note);
	}

	public function setTaxTotal($taxAmount, $taxableAmount, $percent, $currencyId)
	{
		$global = $this->content;
		$taxTotal = $global->addChild('xmlns:cac:TaxTotal');
		$taxAmount = $taxTotal->addChild('xmlns:cbc:TaxAmount', $taxAmount);
		$taxAmount->addAttribute('currencyID', $currencyId);
		$taxSubtotal = $taxTotal->addChild('xmlns:cac:TaxSubtotal');
		$taxableAmount = $taxSubtotal->addChild('xmlns:cac:TaxableAmount', $taxAmount);
		$taxableAmount->addAttribute('currencyID', $currencyId);
		$taxAmount = $taxSubtotal->addChild('xmlns:cbc:TaxAmount', $taxAmount);
		$taxAmount->addAttribute('currencyID', $currencyId);
		$percent = $taxSubtotal->addChild('xmlns:cbc:Percent', $percent);
		$taxCategory = $taxSubtotal->addChild('xmlns:cac:TaxCategory');
		$taxScheme = $taxCategory->addChild('xmlns:cac:TaxScheme');
		$taxScheme->addChild('xmlns:cbc:TaxTypeCode', 'TVA');
	}

	public function setLegalMonetaryTotal($lineExtensionAmount, $taxExclusiveAmount, $taxInclusiveAmount, $payableAmount, $currencyId)
	{
		$global = $this->content;
		$legalMonetaryTotal = $global->addChild('xmlns:cac:LegalMonetaryTotal');
		$lineExtensionAmount = $legalMonetaryTotal->addChild('xmlns:cbc:LineExtensionAmount', $lineExtensionAmount);
		$lineExtensionAmount->addAttribute('currencyID', $currencyId);
		$taxExclusiveAmount = $legalMonetaryTotal->addChild('xmlns:cbc:taxExclusiveAmount', $taxExclusiveAmount);
		$taxExclusiveAmount->addAttribute('currencyID', $currencyId);
		$taxInclusiveAmount = $legalMonetaryTotal->addChild('xmlns:cbc:taxInclusiveAmount', $taxInclusiveAmount);
		$taxInclusiveAmount->addAttribute('currencyID', $currencyId);
		$payableAmount = $legalMonetaryTotal->addChild('xmlns:cbc:payableAmount', $payableAmount);
		$payableAmount->addAttribute('currencyID', $currencyId);
	}

	public function addInvoiceLine($id, $quantity, $lineExtensionAmount, $currency, $actualDeliveryDate, $taxAmount, $taxableAmount, $taxTypeCode, $priceAmount, $baseQuantity, $description1 = null, $description2 = null, $description3 = null, $description4 = null, $description5 = null, $name = null, $classifiedTaxCategoryPercent = null, $classifiedTaxCategoryScheme = null)
	{
		$global = $this->content;
		
		$invoiceLine = $global->addChild('xmlns:cac:InvoiceLine');
		$invoiceLine->addChild('xmlns:cbc:ID', $id);

		$invoicedQuantity = $invoiceLine->addChild('xmlns:cbc:InvoicedQuantity', $quantity);
		$invoicedQuantity->addAttribute('unitCode', 'EA');

		$lineExtensionAmount = $invoiceLine->addChild('xmlns:cbc:LineExtensionAmount', $lineExtensionAmount);
		$lineExtensionAmount->addAttribute('currencyID', $currency);

		$delivery = $invoiceLine->addChild('xmlns:cac:Delivery');
		$actualDeliveryDate = $delivery->addChild('xmlns:cbc:ActualDeliveryDate', $actualDeliveryDate);

		$taxTotal = $invoiceLine->addChild('xmlns:cac:TaxTotal');
		$taxAmount = $taxTotal->addChild('xmlns:cbc:TaxAmount', $taxAmount);
		$taxAmount->addAttribute('currencyID', $currency);

		$taxSubtotal = $taxTotal->addChild('xmlns:cac:TaxSubtotal');
		$taxableAmount = $taxSubtotal->addChild('xmlns:cbc:TaxableAmount', $taxableAmount);
		$taxableAmount->addAttribute('currencyID', $currency);
		$taxAmount = $taxSubtotal->addChild('xmlns:cbc:TaxAmount', $taxAmount);
		$taxAmount->addAttribute('currencyID', $currency);
		$taxCategory = $taxSubtotal->addChild('xmlns:cac:TaxCategory');
		$taxScheme = $taxCategory->addChild('xmlns:cac:TaxScheme');
		$taxTypeCode = $taxScheme->addChild('xmlns:cac:TaxTypeCode', $taxTypeCode);
		
		if ($description1 || $description2 || $description3 || $description4 || $description5 || $name || $classifiedTaxCategoryPercent) {
			$item = $invoiceLine->addChild('xmlns:cac:Item');
	
			if ($description1) $item->addChild('xmlns:cbc:Description', $description1);
			if ($description2) $item->addChild('xmlns:cbc:Description', $description2);
			if ($description3) $item->addChild('xmlns:cbc:Description', $description3);
			if ($description4) $item->addChild('xmlns:cbc:Description', $description4);
			if ($description5) $item->addChild('xmlns:cbc:Description', $description5);
	
			if ($name) $item->addChild('xmlns:cbc:Name', $name);
			
			if ($classifiedTaxCategoryPercent) {
				$classifiedTaxCategory = $item->addChild('xmlns:cac:ClassifiedTaxCategory');
				$classifiedTaxCategory->addChild('xmlns:cbc:ClassifiedTaxCategoryPercent', $classifiedTaxCategoryPercent);
				$classifiedTaxCategoryScheme = $classifiedTaxCategory->addChild('xmlns:cac:ClassifiedTaxCategoryScheme');
				$classifiedTaxCategoryScheme->addChild('xmlns:cbc:ID', $classifiedTaxCategoryScheme);
			}
		}
		
		$price = $invoiceLine->addChild('xmlns:cac:Price');
		$priceAmount = $price->addChild('xmlns:cbc:PriceAmount', $priceAmount);
		$priceAmount->addAttribute('currencyID', $currency);
		$price->addChild('xmlns:cbc:BaseQuantity', $baseQuantity);
	}
}
