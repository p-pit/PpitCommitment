<?php
namespace PpitOrder\Model;

class XmlShipmentResponse
{
	public $content;

	static $ASNLivTemplate =
	<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AdvanceShipmentNoticeList xmlns="rrn:org.xcbl:schemas/xcbl/v4_0/materialsmanagement/v1_0/materialsmanagement.xsd"
	xmlns:core="rrn:org.xcbl:schemas/xcbl/v4_0/core/core.xsd"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<ListOfAdvanceShipmentNotice>
		<AdvanceShipmentNotice>
			<ASNHeader>
				<ASNNumber>?</ASNNumber>
				<ASNIssueDate>?</ASNIssueDate>
				<ASNOrderNumber>
					<core:BuyerOrderNumber>?</core:BuyerOrderNumber>
				</ASNOrderNumber>
				<ASNPurpose>
					<ASNPurposeCoded>Original</ASNPurposeCoded>
				</ASNPurpose>
				<ASNType>
					<ASNTypeCoded>?</ASNTypeCoded>
				</ASNType>
				<ASNDates>
					<ShipDate>?</ShipDate>
				</ASNDates>
				<ASNParty>
					<BuyerParty>
						<core:PartyID>
							<core:Ident>MIXT</core:Ident>
						</core:PartyID>
					</BuyerParty>
					<SellerParty>
						<core:PartyID>
						<core:Ident>?</core:Ident>
						</core:PartyID>
					</SellerParty>
				</ASNParty>
			</ASNHeader>
		</AdvanceShipmentNotice>
	</ListOfAdvanceShipmentNotice>
</AdvanceShipmentNoticeList>
XML;

	static $ASNLiv12Template =
	<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AdvanceShipmentNoticeList xmlns="rrn:org.xcbl:schemas/xcbl/v4_0/materialsmanagement/v1_0/materialsmanagement.xsd"
	xmlns:core="rrn:org.xcbl:schemas/xcbl/v4_0/core/core.xsd"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<ListOfAdvanceShipmentNotice>
		<AdvanceShipmentNotice>
			<ASNHeader>
				<ASNNumber>?</ASNNumber>
				<ASNIssueDate>?</ASNIssueDate>
				<ASNOrderNumber>
					<core:BuyerOrderNumber>?</core:BuyerOrderNumber>
				</ASNOrderNumber>
				<ASNPurpose>
					<ASNPurposeCoded>Original</ASNPurposeCoded>
				</ASNPurpose>
				<ASNType>
					<ASNTypeCoded>?</ASNTypeCoded>
				</ASNType>
				<ASNDates>
					<ShipDate>?</ShipDate>
				</ASNDates>
				<ASNParty>
					<BuyerParty>
						<core:PartyID>
							<core:Ident>MIXT</core:Ident>
						</core:PartyID>
					</BuyerParty>
					<SellerParty>
						<core:PartyID>
						<core:Ident>?</core:Ident>
						</core:PartyID>
					</SellerParty>
				</ASNParty>
			</ASNHeader>
			<ASNDetail>
				<ListOfASNItemDetail>
				</ListOfASNItemDetail>
			</ASNDetail>
		</AdvanceShipmentNotice>
	</ListOfAdvanceShipmentNotice>
</AdvanceShipmentNoticeList>
XML;
	
	static $ASNLivFullTemplate = 
	<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AdvanceShipmentNoticeList xmlns="rrn:org.xcbl:schemas/xcbl/v4_0/materialsmanagement/v1_0/materialsmanagement.xsd"
	xmlns:core="rrn:org.xcbl:schemas/xcbl/v4_0/core/core.xsd"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<ListOfAdvanceShipmentNotice>
		<AdvanceShipmentNotice>
			<ASNHeader>
				<ASNNumber>?</ASNNumber>
				<ASNIssueDate>?</ASNIssueDate>
				<ASNOrderNumber>
					<core:BuyerOrderNumber>?</core:BuyerOrderNumber>
				</ASNOrderNumber>
				<ASNPurpose>
					<ASNPurposeCoded>Original</ASNPurposeCoded>
				</ASNPurpose>
				<ASNType>
					<ASNTypeCoded>?</ASNTypeCoded>
				</ASNType>
				<ASNDates>
					<ShipDate>?</ShipDate>
				</ASNDates>
				<ASNParty>
					<BuyerParty>
						<core:PartyID>
							<core:Ident>MIXT</core:Ident>
						</core:PartyID>
					</BuyerParty>
					<SellerParty>
						<core:PartyID>
						<core:Ident>?</core:Ident>
						</core:PartyID>
					</SellerParty>
				</ASNParty>
			</ASNHeader>
			<ASNDetail>
				<ListOfASNItemDetail>
					<ASNItemDetail>
						<ASNBaseItemDetail>
							<LineItemNum>
								<core:BuyerLineItemNum></core:BuyerLineItemNum>
							</LineItemNum>
							<ItemIdentifiers>
								<core:PartNumbers>
									<core:SellerPartNumber>
										<core:PartID></core:PartID>
									</core:SellerPartNumber>
									<core:BuyerPartNumber>
										<core:PartID></core:PartID>
									</core:BuyerPartNumber>
									<core:OtherItemIdentifiers>
										<core:ProductIdentifierCoded>
											<core:ProductIdentifierQualifierCoded>SerialNumber</core:ProductIdentifierQualifierCoded>
											<core:ProductIdentifier></core:ProductIdentifier>
										</core:ProductIdentifierCoded>
									</core:OtherItemIdentifiers>
								</core:PartNumbers>
							</ItemIdentifiers>
							<ASNQuantities>
								<ShippedQuantity>
									<core:QuantityValue></core:QuantityValue>
									<core:UnitOfMeasurement>
										<core:UOMCoded></core:UOMCoded>
										<core:UOMCodedOther></core:UOMCodedOther>
									</core:UnitOfMeasurement>
								</ShippedQuantity>
							</ASNQuantities>
							<ASNItemDates>
								<ShipDate></ShipDate>
							</ASNItemDates>
						</ASNBaseItemDetail>
					</ASNItemDetail>
				</ListOfASNItemDetail>
			</ASNDetail>
		</AdvanceShipmentNotice>
	</ListOfAdvanceShipmentNotice>
</AdvanceShipmentNoticeList>
XML;
	
	public function __construct($type)
	{
		/*if ($type == 'ASNLIV') $this->content = new \SimpleXMLElement(XmlShipmentResponse::$ASNLivTemplate);
		else */$this->content = new \SimpleXMLElement(XmlShipmentResponse::$ASNLiv12Template);
	}
	
	public function asXML(){
		return $this->content->asXML();
	}

	public function setASNNumber($value)
	{
		$this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNHeader->ASNNumber = $value;
	}

	public function setASNIssueDate($value)
	{
		$this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNHeader->ASNIssueDate = $value;
	}

	public function setBuyerOrderNumber($value)
	{
		$global = $this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNHeader->ASNOrderNumber;
		$namespaces = $global->getNameSpaces(true);
		$core = $global->children($namespaces['core']);
		$core->BuyerOrderNumber = $value;
	}

	public function setType($value)
	{
		$this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNHeader->ASNType->ASNTypeCoded = $value;
	}

	public function setShipDate($value)
	{
		$this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNHeader->ASNDates->ShipDate = $value;
	}
/*	
	public function setBuyerParty($value)
	{
		$global = $this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNHeader->ASNParty->BuyerParty;
		$namespaces = $global->getNameSpaces(true);
		$core = $global->children($namespaces['core']);
		$core->PartyID->Ident = $value;
	}*/

	public function setSellerParty($value)
	{
		$global = $this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNHeader->ASNParty->SellerParty;
		$namespaces = $global->getNameSpaces(true);
		$core = $global->children($namespaces['core']);
		$core->PartyID->Ident = $value;
	}

	public function addItemDetail($itemNum, $identifier, $shipDate)
	{
		$global = $this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNDetail->ListOfASNItemDetail;
		$itemDetail = $global->addChild('ASNItemDetail');
		$baseItemDetail = $itemDetail->addChild('ASNBaseItemDetail');
		
		// LineItemNum
		$lineItemNum = $baseItemDetail->addChild('LineItemNum');
		$lineItemNum->addChild('xmlns:core:BuyerLineItemNum', $itemNum);
		
		// ProductIdentifier
		$itemIdentifiers = $baseItemDetail->addChild('ItemIdentifiers');
		$partNumbers = $itemIdentifiers->addChild('xmlns:core:PartNumbers');
		$otherItemIdentifiers = $partNumbers->addChild('xmlns:core:OtherItemIdentifiers');
		$productIdentifierCoded = $otherItemIdentifiers->addChild('xmlns:core:ProductIdentifierCoded');
		$productIdentifierQualifierCoded = $productIdentifierCoded->addChild('xmlns:core:ProductIdentifierQualifierCoded', 'SerialNumber');
		$productIdentifier = $productIdentifierCoded->addChild('xmlns:core:ProductIdentifier', $identifier);

		// Quantities
		$quantities = $baseItemDetail->addChild('ASNQuantities');
		$shippedQuantity = $quantities->addChild('ShippedQuantity');
		$quantityValue = $shippedQuantity->addChild('xmlns:core:QuantityValue', 1);
		$unitOfMeasurement = $shippedQuantity->addChild('xmlns:core:UnitOfMeasurement');
		$unitOfMeasurement->addChild('xmlns:core:UOMCoded', 'Other');
		$unitOfMeasurement->addChild('xmlns:core:UOMCodedOther', 'PCE');
		
		// ShipDate
		$asmItemDates = $baseItemDetail->addChild('ASNItemDates');
		$asmItemDates->addChild('ShipDate', $shipDate);
	}
/*
	public function setLineItemNum($value)
	{
		$global = $this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNDetail->ListOfASNItemDetail->ASNItemDetail->ASNBaseItemDetail->LineItemNum;
		$namespaces = $global->getNameSpaces(true);
		$core = $global->children($namespaces['core']);
		$core->BuyerLineItemNum = $value;
	}

	public function setSellerPartNumber($value)
	{
		$global = $this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNDetail->ListOfASNItemDetail->ASNItemDetail->ASNBaseItemDetail->ItemIdentifiers;
		$namespaces = $global->getNameSpaces(true);
		$core = $global->children($namespaces['core']);
		$core->PartNumbers->SellerPartNumber->PartID = $value;
	}

	public function setBuyerPartNumber($value)
	{
		$global = $this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNDetail->ListOfASNItemDetail->ASNItemDetail->ASNBaseItemDetail->ItemIdentifiers;
		$namespaces = $global->getNameSpaces(true);
		$core = $global->children($namespaces['core']);
		$core->PartNumbers->BuyerPartNumber->PartID = $value;
	}

	public function setProductIdentifier($value)
	{
		$global = $this->content->ListOfAdvanceShipmentNotice->AdvanceShipmentNotice->ASNDetail->ListOfASNItemDetail->ASNItemDetail->ASNBaseItemDetail->ItemIdentifiers;
		$namespaces = $global->getNameSpaces(true);
		$core = $global->children($namespaces['core']);
		$core->PartNumbers->OtherItemIdentifiers->ProductIdentifier = $value;
	}*/
}
