<?php
namespace PpitOrder\Model;

class XmlOrder
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

	public function getType($commitmentTypes)
	{
		$itemIdentifiers = $this->content->ListOfOrder[0]->Order[0]->OrderDetail[0]->ListOfItemDetail[0]->ItemDetail[0]->BaseItemDetail[0]->ItemIdentifiers;
		$namespaces = $itemIdentifiers->getNameSpaces(true);
		$core = $itemIdentifiers->children($namespaces['core']);
		$codedType = (string)$core->PartNumbers[0]->BuyerPartNumber[0]->PartID;
		return (array_key_exists($codedType, $commitmentTypes)) ? (string) XmlOrder::$types[$codedType] : 'unknown';
	}
	
	public function getOrderIssueDate()
	{
		return (string) $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderIssueDate;
	}

	public function getBuyerOrderNumber()
	{
		return (string) $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderNumber[0]->BuyerOrderNumber;
	}

	public function getReference($code)
	{
		$otherOrderReferences = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderReferences[0]->OtherOrderReferences;
		$namespaces = $otherOrderReferences->getNameSpaces(true);
		$core = $otherOrderReferences->children($namespaces['core']);
	
		$numberOfReferences = count($core->ReferenceCoded);
		for ($i = 0; $i < $numberOfReferences; $i++) {
			if ($core->ReferenceCoded[$i]->ReferenceTypeCoded == $code) {
				return (string) $core->ReferenceCoded[$i]->PrimaryReference[0]->RefNum;
			}
		}
	}

	public function getDate($qualifier)
	{
		$listOfDateCoded = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderDates[0]->ListOfDateCoded;
		$namespaces = $listOfDateCoded->getNameSpaces(true);
		$core = $listOfDateCoded->children($namespaces['core']);
	
		$numberOfDates = count($core->DateCoded);
		for ($i = 0; $i < $numberOfDates; $i++) {
			if ($core->DateCoded[$i]->DateQualifier[0]->DateQualifierCoded == $qualifier) {
				return (string) $core->DateCoded[$i]->Date;
			}
		}
	}
	
	public function getBuyerIdent()
	{
		$buyerParty = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderParty[0]->BuyerParty;
		$namespaces = $buyerParty->getNameSpaces(true);
		$core = $buyerParty->children($namespaces['core']);
		return (string) $core->PartyID[0]->Ident;
	}

	public function getSellerIdent()
	{
		$sellerParty = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderParty[0]->SellerParty;
		$namespaces = $sellerParty->getNameSpaces(true);
		$core = $sellerParty->children($namespaces['core']);
		return (string) $core->PartyID[0]->Ident;
	}
	
	public function getRequestedDeliverByDate()
	{
		return (string) $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderDates[0]->RequestedDeliverByDate;
	}

	public function getItemDescription($i) // UGAP : libellé produit
	{
		$itemIdentifiers = $this->content->ListOfOrder[0]->Order[0]->OrderDetail[0]->ListOfItemDetail[0]->ItemDetail[$i]->BaseItemDetail[0]->ItemIdentifiers;
		$namespaces = $itemIdentifiers->getNameSpaces(true);
		$core = $itemIdentifiers->children($namespaces['core']);
		return (string) $core->ItemDescription;
	}
	
	public function getShipToName() // UGAP : Raison sociale installation
	{
		$shipToParty = $this->content->ListOfOrder[0]->Order[0]->OrderHeader[0]->OrderParty[0]->ShipToParty;
		$namespaces = $shipToParty->getNameSpaces(true);
		$core = $shipToParty->children($namespaces['core']);
		return ((string) $core->NameAddress[0]->Name1).' - '.((string) $core->NameAddress[0]->Name2);
	}

	public function getShipToCity() // UGAP : Ville installation
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
	
	public function getMonetaryAmount($i)
	{
		$pricingDetail = $this->content->ListOfOrder[0]->Order[0]->OrderDetail[0]->ListOfItemDetail[0]->ItemDetail[$i]->PricingDetail;
		$namespaces = $pricingDetail->getNameSpaces(true);
		$core = $pricingDetail->children($namespaces['core']);
		return (string) $core->LineItemTotal[0]->MonetaryAmount;
	}
	
	public function getNumberOfLines()
	{
		return (string) $this->content->ListOfOrder[0]->Order[0]->OrderSummary[0]->NumberOfLines;
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
		$listOfTaxSummary = $this->content->ListOfOrder[0]->Order[0]->OrderSummary[0]->ListOfTaxSummary;
		$namespaces = $listOfTaxSummary->getNameSpaces(true);
		$core = $listOfTaxSummary->children($namespaces['core']);
	
		$tax_rows = array();
		$numberOfRows = count($core->TaxSummary);
		for ($i = 0; $i < $numberOfRows; $i++) {
			$tax_rows[] = array(
					'rate' => (string) $core->TaxSummary[$i]->TaxFunctionQualifierCodedOther,
					'amount' => (string) $core->TaxSummary[$i]->TaxAmount,
			);
		}
		return $tax_rows;
	}
	
	public function getTaxInclusive()
	{
		$orderTotal = $this->content->ListOfOrder[0]->Order[0]->OrderSummary[0]->OrderTotal;
		$namespaces = $orderTotal->getNameSpaces(true);
		$core = $orderTotal->children($namespaces['core']);
		return (string) $core->MonetaryAmount;
	}
}