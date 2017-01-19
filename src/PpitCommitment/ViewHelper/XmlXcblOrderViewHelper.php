<?php
namespace PpitCommitment\ViewHelper;

class XmlXcblOrderViewHelper
{
	public $content;

	public static $types = array(
			'LC4_VOLET_1' => 'part_1',
			'LC4_VOLET_2' => 'part_2',
			'LC4_VOLET_3' => 'part_3',
	);
	
	public function __construct($content)
	{
		$this->content = $content;
	}
	
	public function asXML(){
		return $this->content->asXML();
	}

	public function getPurpose()
	{
		$purpose = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->Purpose;
		$namespaces = $purpose->getNameSpaces(true);
		$core = $purpose->children($namespaces['core']);
		return (string)$core->PurposeCoded;
	}
	
	public function getType()
	{
		$itemIdentifiers = $this->content->ListOfOrder[0]->Order[0]->OrderDetail[0]->ListOfItemDetail[0]->ItemDetail[0]->BaseItemDetail[0]->ItemIdentifiers;
		$namespaces = $itemIdentifiers->getNameSpaces(true);
		$core = $itemIdentifiers->children($namespaces['core']);
		$codedType = (string)$core->PartNumbers[0]->BuyerPartNumber[0]->PartID;
		return (array_key_exists($codedType, XmlXcblOrderViewHelper::$types)) ? (string) XmlXcblOrderViewHelper::$types[$codedType] : 'part_1';
	}

	public function getLineItemType()
	{
		$result = null;
		$itemDetail = $this->content->ListOfOrder[0]->Order[0]->OrderDetail->ListOfItemDetail->ItemDetail;
		$numberOfItemDetail = count($itemDetail);
		for ($i = 0; $i < $numberOfItemDetail; $i++) {
			$lineItemType = $itemDetail[$i]->BaseItemDetail->LineItemType;
			$namespaces = $lineItemType->getNameSpaces(true);
			$core = $lineItemType->children($namespaces['core']);
			if (!$result) $result = (string)$core->LineItemTypeCodedOther;
			elseif ((string)$core->LineItemTypeCodedOther != $result) return null;
		}
		return $result;
	}
	
	public function getOrderDate()
	{
		return (string) $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderIssueDate;
	}

	public function getIdentifier()
	{
		return (string) $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderNumber[0]->BuyerOrderNumber;
	}

	public function getCommercialOperationNumber()
	{
		$otherOrderReferences = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderReferences[0]->OtherOrderReferences;
		$namespaces = $otherOrderReferences->getNameSpaces(true);
		$core = $otherOrderReferences->children($namespaces['core']);
	
		$numberOfReferences = count($core->ReferenceCoded);
		for ($i = 0; $i < $numberOfReferences; $i++) {
			if ($core->ReferenceCoded[$i]->ReferenceTypeCoded == 'OperationNumber') {
				return (string) $core->ReferenceCoded[$i]->PrimaryReference[0]->RefNum;
			}
		}
	}

	public function getLateChargeStartDate()
	{
		$listOfDateCoded = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderDates[0]->ListOfDateCoded;
		$namespaces = $listOfDateCoded->getNameSpaces(true);
		$core = $listOfDateCoded->children($namespaces['core']);
	
		$numberOfDates = count($core->DateCoded);
		for ($i = 0; $i < $numberOfDates; $i++) {
			if ($core->DateCoded[$i]->DateQualifier[0]->DateQualifierCoded == 'ContractualDeliveryDate') {
				return (string) $core->DateCoded[$i]->Date;
			}
		}
	}
	
	public function getBuyerIdentifier()
	{
		$buyerParty = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderParty[0]->BuyerParty;
		$namespaces = $buyerParty->getNameSpaces(true);
		$core = $buyerParty->children($namespaces['core']);
		return (string) $core->PartyID[0]->Ident;
	}

	public function getSellerIdentifier()
	{
		$sellerParty = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderParty[0]->SellerParty;
		$namespaces = $sellerParty->getNameSpaces(true);
		$core = $sellerParty->children($namespaces['core']);
		return (string) $core->PartyID[0]->Ident;
	}
	
	public function getHopedDeliveryDate()
	{
		return (string) $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderDates[0]->RequestedDeliverByDate;
	}

	public function getItemDescription()
	{
		$itemIdentifiers = $this->content->ListOfOrder[0]->Order[0]->OrderDetail[0]->ListOfItemDetail[0]->ItemDetail[0]->BaseItemDetail[0]->ItemIdentifiers;
		$namespaces = $itemIdentifiers->getNameSpaces(true);
		$core = $itemIdentifiers->children($namespaces['core']);
		return (string) $core->ItemDescription;
	}
	
	public function getNameAddressName()
	{
		$shipToParty = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderParty[0]->ShipToParty;
		$namespaces = $shipToParty->getNameSpaces(true);
		$core = $shipToParty->children($namespaces['core']);
		return ((string) $core->NameAddress[0]->Name1).' - '.((string) $core->NameAddress[0]->Name2);
	}

	public function getNameAddressCity()
	{
		$shipToParty = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderParty[0]->ShipToParty;
		$namespaces = $shipToParty->getNameSpaces(true);
		$core = $shipToParty->children($namespaces['core']);
		return (string) $core->NameAddress[0]->City;
	}

	public function getShipToPartyPostalCode()
	{
		$shipToParty = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderParty[0]->ShipToParty;
		$namespaces = $shipToParty->getNameSpaces(true);
		$core = $shipToParty->children($namespaces['core']);
		return (string) $core->NameAddress[0]->PostalCode;
	}

	public function getStartOfScheduleLineDate()
	{
		$listOfStructuredNote = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->ListOfStructuredNote;
		$namespaces = $listOfStructuredNote->getNameSpaces(true);
		$core = $listOfStructuredNote->children($namespaces['core']);
		foreach ($core->StructuredNote as $structuredNote) {
			if ($structuredNote->TextTypeCodedOther == 'StartOfScheduleLineDate') {
				$date = $structuredNote->GeneralNote;
				return '20'.substr($date, 6, 2).'-'.substr($date, 3, 2).'-'.substr($date, 0, 2);
			}
		}
	}

	public function getEndOfScheduleLineDate()
	{
		$listOfStructuredNote = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->ListOfStructuredNote;
		$namespaces = $listOfStructuredNote->getNameSpaces(true);
		$core = $listOfStructuredNote->children($namespaces['core']);
		foreach ($core->StructuredNote as $structuredNote) {
			if ($structuredNote->TextTypeCodedOther == 'EndOfScheduleLineDate') {
				$date = $structuredNote->GeneralNote;
				return '20'.substr($date, 6, 2).'-'.substr($date, 3, 2).'-'.substr($date, 0, 2);
			}
		}
	}
	
	public function getPrice()
	{
		$pricingDetail = $this->content->ListOfOrder[0]->Order[0]->OrderDetail[0]->ListOfItemDetail[0]->ItemDetail[0]->PricingDetail;
		$namespaces = $pricingDetail->getNameSpaces(true);
		$core = $pricingDetail->children($namespaces['core']);
		return (string) $core->LineItemTotal[0]->MonetaryAmount;
	}
	
	public function getNumberOfLines()
	{
		return count($this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail); //(string) $this->content->ListOfOrder[0]->Order[0]->OrderSummary[0]->NumberOfLines;
	}

	public function getBuyerLineItemNum($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->BaseItemDetail->LineItemNum;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		return (string) $core->BuyerLineItemNum;
	}
/*
	public function getSellerLineItemNum($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->BaseItemDetail->LineItemNum;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		return (string) $core->SellerLineItemNum;
	}

	public function getBuyerPartNumber($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->BaseItemDetail->ItemIdentifiers;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		return (string) $core->PartNumbers->BuyerPartId->PartId;
	}*/

	public function getLineProductIdentifier($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->BaseItemDetail->ItemIdentifiers;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		return $core->PartNumbers->OtherItemIdentifiers->ProductIdentifierCoded[0]->ProductIdentifier;
	}

	public function getLineSerialNumber($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->BaseItemDetail->ItemIdentifiers;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		foreach ($core->ListOfItemCharacteristic->ItemCharacteristic as $itemCharacteristic) {
			if ($itemCharacteristic->ItemCharacteristicCodedOther == 'LC4_NUM_SERIE') return $itemCharacteristic->ItemCharacteristicValue;
		}
		return null;
	}
	
	public function getLineTotalQuantity($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->BaseItemDetail->TotalQuantity;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		return $core->QuantityValue;
	}

	public function getLineUnitPrice($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->PricingDetail;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		return $core->ListOfPrice->Price->UnitPrice->UnitPriceValue;
	}

	public function getLineCalculatedPriceBasisQuantity($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->PricingDetail;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		return $core->ListOfPrice->Price->CalculatedPriceBasisQuantity->QuantityValue;
	}

	public function getLineItemTotal($i)
	{
		$node = $this->content->ListOfOrder->Order->OrderDetail->ListOfItemDetail->ItemDetail[$i]->PricingDetail;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		return $core->LineItemTotal->MonetaryAmount;
	}
	
	public function getTaxRows()
	{
		$tax_rows = array();
		$listOfTaxSummary = $this->content->ListOfOrder[0]->Order[0]->OrderSummary[0]->ListOfTaxSummary;
		if ($listOfTaxSummary) {
			$namespaces = $listOfTaxSummary->getNameSpaces(true);
			$core = $listOfTaxSummary->children($namespaces['core']);
			$numberOfRows = count($core->TaxSummary);
			for ($i = 0; $i < $numberOfRows; $i++) {
				$tax_rows[] = array(
						'rate' => (string) $core->TaxSummary[$i]->TaxFunctionQualifierCodedOther,
						'amount' => (string) $core->TaxSummary[$i]->TaxAmount,
				);
			}
		}
		return $tax_rows;
	}
	
	public function getTaxExclusive()
	{
		$orderTotal = $this->content->ListOfOrder[0]->Order[0]->OrderSummary[0]->OrderTotal;
		if ($orderTotal) {
			$namespaces = $orderTotal->getNameSpaces(true);
			$core = $orderTotal->children($namespaces['core']);
			return (string) $core->MonetaryAmount;
		}
		else return 0;
	}
}