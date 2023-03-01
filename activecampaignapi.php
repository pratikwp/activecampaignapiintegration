<?php
$apiurl = 'Your API URL'; // replace with your ActiveCampaign API URL
$apikey = 'Your API Key'; // replace with your ActiveCampaign API key

/* Create Contact in Active Campaign */
$ac_create_contact = array(
	'contact' => array(
		'email'     => $email, // replace with email variable
		'firstName' => $first_name, // replace with first name variable
		'lastName'  => $last_name, // replace with last name variable
		'phone'     => $phone, // replace with phone variable
	)
);

$url_create_contact     = $apiurl.'/contacts'; // create contact API URL
$payload_create_contact = json_encode( $ac_create_contact ); // convert contact data to JSON format

$ch = curl_init( $url_create_contact ); // initialize cURL session
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload_create_contact );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
	'Api-Token:'.$apikey,
	'Content-Type: application/json',
	'Content-Length: ' . strlen( $payload_create_contact )
)); // set cURL options and headers

$response = curl_exec( $ch ); // execute cURL session
if (curl_errno($ch)) { // check for cURL errors
	echo 'Error: ' . curl_error($ch);
} else {
	$response = json_decode( $response ); // convert API response to JSON format
	if(!empty($response->contact) && empty($response->error)) { // check for successful response
		$contactid = $response->contact->id; // get contact ID

		/*
		Set Custom Fields value
		Here key is field id on Active Campaign
		*/
		$ac_fields_datas = array(
			'12' => $customer_name, // replace with customer name variable
			'15' => $order_number, // replace with order number variable
			'17' => $order_status, // replace with order status variable
			'28' => $total_amount, // replace with total amount variable
		);

		foreach($ac_fields_datas as $key => $value) {	
			$data = array(
				'fieldValue' => array(
					'contact' => $contactid,
					'field' => $key,
					'value'  => $value,
				)
			); // create custom field data array
			$url     = $apiurl.'/fieldValues/'.$key; // create custom field API URL
			$payload = json_encode( $data ); // convert custom field data to JSON format

			$ch = curl_init( $url ); // initialize new cURL session
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Api-Token:'.$apikey,
				'Content-Type: application/json',
				'Content-Length: ' . strlen( $payload )
			)); // set cURL options and headers
			$response = curl_exec( $ch ); // execute cURL session
			if (curl_errno($ch)) { // check for cURL errors
				echo 'Error: ' . curl_error($ch);
			}
		}
	}
}

curl_close($ch);
?>
