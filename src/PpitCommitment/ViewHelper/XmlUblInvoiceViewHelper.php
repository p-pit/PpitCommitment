<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;

class XmlUblInvoiceViewHelper
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
	<cbc:InvoiceTypeCode></cbc:InvoiceTypeCode>
	<cbc:Note>[6]</cbc:Note>
	<cbc:DocumentCurrencyCode>EUR</cbc:DocumentCurrencyCode>
	<cbc:LineCountNumeric>1</cbc:LineCountNumeric>
</Invoice>
XML;
	
	public function __construct()
	{
		$this->content = new \SimpleXMLElement(XmlUblInvoiceViewHelper::$template);
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

	public function setInvoiceTypeCode($name, $value) // [4]
	{
		$global = $this->content;
		$namespaces = $global->getNameSpaces(true);
		$cbc = $global->children($namespaces['cbc']);
		$cbc->InvoiceTypeCode['name'] = $name;
		$cbc->InvoiceTypeCode = $value;
	}
	
	public function setNote($value) // [4]
	{
		$global = $this->content;
		$namespaces = $global->getNameSpaces(true);
		$cbc = $global->children($namespaces['cbc']);
		$cbc->Note = $value;
	}

	public function setLineCountNumeric($value) // [4]
	{
		$global = $this->content;
		$namespaces = $global->getNameSpaces(true);
		$cbc = $global->children($namespaces['cbc']);
		$cbc->LineCountNumeric = $value;
	}

	public function setInvoicePeriod($beginDate, $endDate, $description)
	{
		$global = $this->content;
		$invoicePeriod = $global->addChild('xmlns:cac:InvoicePeriod');
		$invoicePeriod->addChild('xmlns:cbc:StartDate', $beginDate); // [9]
		$invoicePeriod->addChild('xmlns:cbc:EndDate', $endDate); // [10]
		$invoicePeriod->addChild('xmlns:cbc:Description', $description); // [11]
	}
	
	public function setBillingReference($value) // [12]
	{
		$global = $this->content;
		$billingReference = $global->addChild('xmlns:cac:BillingReference');
		$invoiceDocumentReference = $billingReference->addChild('xmlns:cac:InvoiceDocumentReference');
		$invoiceDocumentReference->addChild('xmlns:cbc:Id', $value);
	}

	public function setContractDocumentReference($value, $documentTypeCode) // [13]
	{
		$global = $this->content;
		$contractDocumentReference = $global->addChild('xmlns:cac:ContractDocumentReference');
		$contractDocumentReference->addChild('xmlns:cbc:ID', $value);
		$contractDocumentReference->addChild('xmlns:cbc:DocumentTypeCode', $documentTypeCode);
	}
	
	public function setAccountingSupplierParty() {
		$context = Context::getCurrent();
		$supplier = $context->getConfig('commitment/supplierIdentificationSheet');
		$global = $this->content;
		$accountingSupplierParty = $global->addChild('xmlns:cac:AccountingSupplierParty');
		$party = $accountingSupplierParty->addChild('xmlns:cac:Party');
		$partyIdentification = $party->addChild('xmlns:cac:PartyIdentification');
		$id = $partyIdentification->addChild('xmlns:cbc:ID', $supplier['ID']);
		$id->addAttribute('schemeName', 'SIRET');

		$partyName = $party->addChild('xmlns:cac:PartyName');
		$partyName->addChild('xmlns:cbc:Name', $supplier['Name']);

		$postalAddress = $party->addChild('xmlns:cac:PostalAddress');
		$postalAddress->addChild('xmlns:cbc:CityName', $supplier['CityName']);
		$postalAddress->addChild('xmlns:cbc:PostalZone', $supplier['PostalZone']);
		$addressLine = $postalAddress->addChild('xmlns:cac:AddressLine');
		$addressLine->addChild('xmlns:cbc:Line', $supplier['AddressLine1']);
		$addressLine = $postalAddress->addChild('xmlns:cac:AddressLine');
		$addressLine->addChild('xmlns:cbc:Line', $supplier['AddressLine2']);
		$country = $postalAddress->addChild('xmlns:cac:Country');
		$country->addChild('xmlns:cbc:IdentificationCode', $supplier['Country']);

		$partyTaxScheme = $party->addChild('xmlns:cac:PartyTaxScheme');
		$companyID = $partyTaxScheme->addChild('xmlns:cbc:CompanyID', $supplier['TaxSchemeID']);
		$companyID->addAttribute('schemeName', $supplier['TaxSchemeName']);
		$taxScheme = $partyTaxScheme->addChild('xmlns:cac:TaxScheme');
		$taxScheme->addChild('xmlns:cbc:TaxTypeCode', $context->getConfig('commitment/invoice_tax_mention'));

		$partyLegalEntity = $party->addChild('xmlns:cac:PartyLegalEntity');
		$partyLegalEntity->addChild('xmlns:cbc:RegistrationName', $supplier['RegistrationName']);
		$partyLegalEntity->addChild('xmlns:cbc:CompanyID', $supplier['LegalEntityID']);
		$registrationAddress = $partyLegalEntity->addChild('xmlns:cac:RegistrationAddress');
		$registrationAddress->addChild('xmlns:cbc:CityName', $supplier['LegalEntityCityName']);
		$registrationAddress->addChild('xmlns:cbc:PostalZone', $supplier['LegalEntityPostalZone']);
		$addressLine = $registrationAddress->addChild('xmlns:cac:AddressLine');
		$addressLine->addChild('xmlns:cbc:Line', $supplier['LegalEntityAddressLine1']);
		if ($supplier['LegalEntityAddressLine2']) {
			$addressLine = $registrationAddress->addChild('xmlns:cac:AddressLine');
			$addressLine->addChild('xmlns:cbc:Line', $supplier['LegalEntityAddressLine2']);
		}
		$country = $registrationAddress->addChild('xmlns:cac:Country');
		$country->addChild('xmlns:cbc:IdentificationCode', $supplier['LegalEntityCountry']);
		$corporateRegistrationScheme = $partyLegalEntity->addChild('xmlns:cac:CorporateRegistrationScheme');
		$corporateRegistrationScheme->addChild('xmlns:cbc:ID', $supplier['CorporateRegistrationScheme']);

		$contact = $party->addChild('xmlns:cac:Contact');
		if ($supplier['ContactID']) $contact->addChild('xmlns:cbc:ID', $supplier['ContactID']);
		if ($supplier['ContactName']) $contact->addChild('xmlns:cbc:Name', $supplier['ContactName']);
		if ($supplier['ContactTelephone']) $contact->addChild('xmlns:cbc:Telephone', $supplier['ContactTelephone']);
		if ($supplier['ContactElectronicMail']) $contact->addChild('xmlns:cbc:ElectronicMail', $supplier['ContactElectronicMail']);
	}

	public function setAccountingCustomerParty($commitment) {
		$context = Context::getCurrent();
		$global = $this->content;
		$accountingCustomerParty = $global->addChild('xmlns:cac:AccountingCustomerParty');
		$party = $accountingCustomerParty->addChild('xmlns:cac:Party');
		$partyIdentification = $party->addChild('xmlns:cac:PartyIdentification');
		$id = $partyIdentification->addChild('xmlns:cbc:ID', $commitment->customer_identifier);
		$id->addAttribute('schemeName', 'SIREN');
	
		$partyName = $party->addChild('xmlns:cac:PartyName');
		$partyName->addChild('xmlns:cbc:Name', $commitment->customer_invoice_name);
	
		$postalAddress = $party->addChild('xmlns:cac:PostalAddress');
		$postalAddress->addChild('xmlns:cbc:CityName', $commitment->customer_adr_city);
		$postalAddress->addChild('xmlns:cbc:PostalZone', $commitment->customer_adr_zip);
		$addressLine = $postalAddress->addChild('xmlns:cac:AddressLine');
		$addressLine->addChild('xmlns:cbc:Line', $commitment->customer_adr_street);
		if ($commitment->customer_adr_extended) {
			$addressLine = $postalAddress->addChild('xmlns:cac:AddressLine');
			$addressLine->addChild('xmlns:cbc:Line', $commitment->customer_adr_extended);
		}
		elseif ($commitment->customer_adr_post_office_box) {
			$addressLine = $postalAddress->addChild('xmlns:cac:AddressLine');
			$addressLine->addChild('xmlns:cbc:Line', $commitment->customer_adr_post_office_box);
		}
		$country = $postalAddress->addChild('xmlns:cac:Country');
		$country->addChild('xmlns:cbc:IdentificationCode', $commitment->customer_adr_country);
/*	
		$partyTaxScheme = $party->addChild('xmlns:cac:PartyTaxScheme');
		$companyId = $partyTaxScheme->addChild('xmlns:cbc:CompanyID', $customer['TaxSchemeCompanyID']);
		$companyId->addAttribute('schemeName', 'Num TVA intra-communautaire');
		$taxScheme = $partyTaxScheme->addChild('xmlns:cac:TaxScheme');
		$taxScheme->addChild('xmlns:cbc:Name', $customer['TaxSchemeName']);*/
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
		$financialAccount = $paymentMeans->addChild('xmlns:cac:PayeeFinancialAccount');
		$financialAccount->addChild('xmlns:cbc:ID', $payeeFinancialAccount);
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
		$taxableAmount = $taxSubtotal->addChild('xmlns:cbc:TaxableAmount', $taxableAmount);
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
		$taxExclusiveAmount = $legalMonetaryTotal->addChild('xmlns:cbc:TaxExclusiveAmount', $taxExclusiveAmount);
		$taxExclusiveAmount->addAttribute('currencyID', $currencyId);
		$taxInclusiveAmount = $legalMonetaryTotal->addChild('xmlns:cbc:TaxInclusiveAmount', $taxInclusiveAmount);
		$taxInclusiveAmount->addAttribute('currencyID', $currencyId);
		$payableAmount = $legalMonetaryTotal->addChild('xmlns:cbc:PayableAmount', $payableAmount);
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
		$taxTypeCode = $taxScheme->addChild('xmlns:cbc:TaxTypeCode', $taxTypeCode);
		
		if ($description1 || $description2 || $description3 || $description4 || $description5 || $name || $classifiedTaxCategoryPercent) {
			$item = $invoiceLine->addChild('xmlns:cac:Item');
	
			if ($description1) $item->addChild('xmlns:cbc:Description', $description1);
			if ($description2) $item->addChild('xmlns:cbc:Description', $description2);
			if ($description3) $item->addChild('xmlns:cbc:Description', $description3);
			if ($description4) $item->addChild('xmlns:cbc:Description', $description4);
			if ($description5) $item->addChild('xmlns:cbc:Description', $description5);
	
			if ($name) $item->addChild('xmlns:cbc:Name', $name);
			
//			if ($classifiedTaxCategoryPercent) {
				$classifiedTaxCategory = $item->addChild('xmlns:cac:ClassifiedTaxCategory');
				$classifiedTaxCategory->addChild('xmlns:cbc:Percent', $classifiedTaxCategoryPercent);
				$taxCategoryScheme = $classifiedTaxCategory->addChild('xmlns:cac:TaxScheme');
				$taxCategoryScheme->addChild('xmlns:cbc:ID', $classifiedTaxCategoryScheme);
//			}
		}
		
		$price = $invoiceLine->addChild('xmlns:cac:Price');
		$priceAmount = $price->addChild('xmlns:cbc:PriceAmount', $priceAmount);
		$priceAmount->addAttribute('currencyID', $currency);
		$price->addChild('xmlns:cbc:BaseQuantity', $baseQuantity);
	}
}
