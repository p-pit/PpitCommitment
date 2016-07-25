<?php
namespace PpitOrder\Model;

class XmlOrderResponse
{
	public $content;

	static $template = 
	<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OrderResponseList xmlns="rrn:org.xcbl:schemas/xcbl/v4_0/ordermanagement/v1_0/ordermanagement.xsd"
    xmlns:core="rrn:org.xcbl:schemas/xcbl/v4_0/core/core.xsd"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ListOfOrderResponse>
    <OrderResponse>
    <OrderResponseHeader>
        <OrderResponseNumber>
            <BuyerOrderResponseNumber>1</BuyerOrderResponseNumber>
        </OrderResponseNumber>
        <OrderResponseIssueDate>2</OrderResponseIssueDate>
        <OrderResponseDocTypeCoded>OrderResponse</OrderResponseDocTypeCoded>
        <OrderResponseDocTypeCodedOther></OrderResponseDocTypeCodedOther>
        <OrderReference>
            <core:RefNum></core:RefNum>
        </OrderReference>
        <SellerParty>
            <core:PartyID>
                <core:Ident></core:Ident>
            </core:PartyID>
        </SellerParty>
        <BuyerParty>
            <core:PartyID>
                <core:Ident>6</core:Ident>
            </core:PartyID>
        </BuyerParty>
        <ResponseType>
            <core:ResponseTypeCoded>7</core:ResponseTypeCoded>
        </ResponseType>
        <ShipmentStatusEvent>
            <core:StatusEvent>
                <core:StatusEventCoded>Other</core:StatusEventCoded>
                <core:StatusEventCodedOther>DeliveryDateTimePromisedFor</core:StatusEventCodedOther>
            </core:StatusEvent>
            <core:ShipDate>10</core:ShipDate>
        </ShipmentStatusEvent>
        <ListOfStructuredNote>
             <core:StructuredNote>
                   <core:GeneralNote>10a</core:GeneralNote>
                   <core:NoteID>10b</core:NoteID>
            </core:StructuredNote>
       </ListOfStructuredNote>
    </OrderResponseHeader>
	</OrderResponse>
</ListOfOrderResponse>
</OrderResponseList>
XML;
	
	public function __construct()
	{
		$this->content = new \SimpleXMLElement(XmlOrderResponse::$template);
	}
	
	public function asXML(){
		return $this->content->asXML();
	}

	public function setBuyerOrderResponseNumber($value)
	{
		$this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->OrderResponseNumber[0]->BuyerOrderResponseNumber = $value;
	}

	public function setOrderResponseIssueDate($value)
	{
		$this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->OrderResponseIssueDate = $value;
	}

	public function setOrderReference($value)
	{
		$orderReference = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->OrderReference;
		$namespaces = $orderReference->getNameSpaces(true);
		$core = $orderReference->children($namespaces['core']);
		$core->RefNum = $value;
	}

	public function setSellerIdent($value)
	{
		$sellerParty = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->SellerParty;
		$namespaces = $sellerParty->getNameSpaces(true);
		$core = $sellerParty->children($namespaces['core']);
		$core->PartyID->Ident = $value;
	}

	public function setBuyerIdent($value)
	{
		$buyerParty = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->BuyerParty;
		$namespaces = $buyerParty->getNameSpaces(true);
		$core = $buyerParty->children($namespaces['core']);
		$core->PartyID->Ident = $value;
	}

	public function setResponseType($value)
	{
		$responseType = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->ResponseType;
		$namespaces = $responseType->getNameSpaces(true);
		$core = $responseType->children($namespaces['core']);
		$core->ResponseTypeCoded = $value;
	}
/*
	public function setItemDetailResponse($value)
	{
		$this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseDetail[0]->ListOfOrderResponseItemDetail[0]->OrderResponseItemDetail[0]->ItemDetailResponseCoded = $value;
	}*/
	
	public function setHeaderStatusEvent($value)
	{
		$node = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->ShipmentStatusEvent;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		$core->ShipDate = $value.'T00:00:00';
	}
/*
	public function setDetailStatusEvent($value)
	{
		$node = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseDetail[0]->ListOfOrderResponseItemDetail[0]->OrderResponseItemDetail[0]->ShipmentStatusEvent;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		$core->ShipDate = $value.'T00:00:00';
	}*/
	
	public function setHeaderGeneralNote($value)
	{
		$node = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->ListOfStructuredNote;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		$core->StructuredNote[0]->GeneralNote = $value;
	}

	public function setHeaderNoteId($value)
	{
		$node = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseHeader[0]->ListOfStructuredNote;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		$core->StructuredNote[0]->NoteID = $value;
	}
/*	
	public function setDetailGeneralNote($value)
	{
		$node = $this->content->ListOfOrderResponse[0]->OrderResponse[0]->OrderResponseDetail[0]->ListOfOrderResponseItemDetail[0]->OrderResponseItemDetail[0]->ListOfStructuredNote;
		$namespaces = $node->getNameSpaces(true);
		$core = $node->children($namespaces['core']);
		$core->StructuredNote[0]->GeneralNote = $value;
	}*/
}