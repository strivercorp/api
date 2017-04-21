<?php
//*******************
//This Lazada API integration script is created by STRIVER Corp.
//It is distributed freely for your use.
//We hope the efforts will reduce your efforts to maintain the Qoo10 store and increase sales!
//We can help to automate the inventory synchronization for nominal fee of CAD $1.99 per month.
//We will maintain the script on our server. The fee is necessary for server upkeep.
//For more details about the fees, refer to the STRIVER webpage http://striver.ca/api-qoo10/
//For enquiry, email: sales@striver.ca
//*******************
//Author: Kian Jin Jason Teo
//*******************
//Change Log
//2017-04-21: Version 1.1
//*******************
//Function to call Qoo10 API and update product quantity and selling price
//You will need to provide seven input parameters; namely
//$sku: Seller Code (must be unique on QSM system) to update
//$qty: product quantity to update
//$sprice: product selling price to update
//$soapUrl: URL address of API host.
//$soapUser: User name to log into Qoo10 QSM.
//$soapPassword: Password to log into Qoo10 QSM.
//$api_key: API key provided by Qoo10 to access the API. Request needed.
//This function must be called by PHP script and the PHP must support cURL.
//The script updates the product price and quantity only.
//*******************

//*******************
//Qoo10 API function to update product quantity and selling price based on Seller Code provided
//*******************
function qoo10_qty_price_update($sku,$qty,$sprice,$soapUrl,$soapUser,$soapPassword,$api_key)
{

	//*******************
	//Create Seller Authorization Key
	//*******************

	//********************		
		//Data, connection, auth
		$page = "/GMKT.INC.Front.OpenApiService/Certification.api";

        // xml post structure
        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
								<soap:Header>
									<GiosisCertificationHeader xmlns="http://giosis.net/">
										<Key>'.$api_key.'</Key>
									</GiosisCertificationHeader>
								</soap:Header>																												
								<soap:Body>
									<CreateCertificationKey xmlns="http://giosis.net/">
										<user_id>'.$soapUser.'</user_id>
										<pwd>'.$soapPassword.'</pwd>
									</CreateCertificationKey>								
								</soap:Body>
                            </soap:Envelope>';

           $headers = array(
						"POST ".$page." HTTP/1.1",
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://giosis.net/CreateCertificationKey", 
                        "Content-length: ".strlen($xml_post_string),
                    );


            // PHP cURL  for https connection with auth
			$ch = curl_init();			
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $soapUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Execute query
            $response = curl_exec($ch); 
            curl_close($ch);
			
			// Remove unnecessary strings
			$response1 = str_replace("<soap:Body>","",$response);
			$response2 = str_replace("</soap:Body>","",$response1);
			
            $parser = simplexml_load_string($response2);
			$key = (string)$parser->{"CreateCertificationKeyResponse"}->{"CreateCertificationKeyResult"}->{"ResultObject"};
			
	//*******************
	//Edit Item Quantity and Price
	//*******************

		//Data, connection, auth
		$page = "/GMKT.INC.Front.OpenApiService/GoodsOrderService.api";

        // xml post structure
        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
								<soap:Header>
									<GiosisCertificationHeader xmlns="http://giosis.net/">
										<Key>'.$key.'</Key>
									</GiosisCertificationHeader>
								</soap:Header>																												
								<soap:Body>
									<SetGoodsPrice xmlns="http://giosis.net/">
										<SellerCode>'.$sku.'</SellerCode>
										<ItemPrice>'.$sprice.'</ItemPrice>
										<ItemQty>'.$qty.'</ItemQty>
									</SetGoodsPrice>								
								</soap:Body>
                            </soap:Envelope>';

           $headers = array(
						"POST ".$page." HTTP/1.1",
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://giosis.net/SetGoodsPrice", 
                        "Content-length: ".strlen($xml_post_string),
                    );


            // PHP cURL  for https connection with auth
			$ch = curl_init();			
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $soapUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Carry out query
            $response = curl_exec($ch); 
            curl_close($ch);			
}
?>

