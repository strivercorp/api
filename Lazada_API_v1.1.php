<?php
//*******************
//This Lazada API integration script is created by STRIVER Corp.
//It is distributed freely for your use.
//We hope the efforts will reduce your efforts to maintain the Lazada store and increase sales!
//We can help to automate the inventory synchronization for nominal fee of CAD $1.99 per month.
//The script is maintained and run on our server. The fee is necessary for the server upkeep.
//For more details about the fees, refer to the STRIVER webpage http://striver.ca/api-lazada/
//For enquiry, email: sales@striver.ca
//*******************
//Author: Kian Jin Jason Teo
//*******************
//Change Log
//2017-04-03: Version 1.1
//*******************
//Function to call Lazada API and update product quantity, retail price and sale price
//You will need to provide seven input parameters; namely
//$sku: product sku to update
//$qty: product quantity to update
//$rprice: product retail price to update
//$sprice: product sale price to update
//$userid: user id to access Lazada inventory
//$api_key: the unique API key provided by Lazada associated with your user id.
//$url: URL of the API host
//This function must be called by PHP script and the PHP must support cURL.
//The script updates the product price and quantity only.
//*******************
function lazada_qty_price_update($sku,$qty,$rprice,$sprice,$userid,$api_key,$url)
{
	//Store XML Request in a variable
	$input_xml = '<?xml version="1.0" encoding="UTF-8"?>
	<Request>
	  <Product>
		<Skus>
		  <Sku>
			<SellerSku>'.$sku.'</SellerSku>
			<Quantity>'.$qty.'</Quantity>
			<Price>'.$rprice.'</Price>
			<SalePrice>'.$sprice.'</SalePrice>
			<SaleStartDate/>
			<SaleEndDate/>
		  </Sku>
		</Skus>
	  </Product>
	</Request>';

	// The current time. Needed to create the Timestamp parameter below.
	date_default_timezone_set("UTC");
	$now = new DateTime();

	// The parameters for our GET request. These will get signed.
	$parameters = array(
		// The user ID for which we are making the call.
		'UserID' => $userid,

		// The API version. Currently must be 1.0
		'Version' => '1.0',

		// The API method to call.
		'Action' => 'UpdatePriceQuantity',

		// The format of the result.
		'Format' => 'JSON',

		// The current time formatted as ISO8601
		'Timestamp' => $now->format(DateTime::ISO8601)
	);

	// Sort parameters by name.
	ksort($parameters);

	// URL encode the parameters.
	$encoded = array();
	foreach ($parameters as $name => $value) {
		$encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
	}

	// Concatenate the sorted and URL encoded parameters into a string.
	$concatenated = implode('&', $encoded);

	// Compute signature and add it to the parameters.
	$parameters['Signature'] =
		rawurlencode(hash_hmac('sha256', $concatenated, $api_key, false));
		
	// Build Query String
	$queryString = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);

	// Open cURL connection
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url."?".$queryString);

	// Save response to the variable $data
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	// Post XML to update product price and quantity
	curl_setopt($ch, CURLOPT_POSTFIELDS, $input_xml);

	$data = curl_exec($ch);

	// Close Curl connection
	curl_close($ch);	
}
?> 
