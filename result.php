<?php 
 // Check IP address and POST parameters
 $ipTable = array('195.149.229.109', '148.251.96.163', '178.32.201.77',
 '46.248.167.59', '46.29.19.106', '176.119.38.175');

 if(!empty($_POST)) {
	if (in_array($_SERVER['REMOTE_ADDR'], $ipTable)) {

		$sellerID = $_POST['id'];
		$transactionStatus = $_POST['tr_status'];
		$transactionID = $_POST['tr_id'];
		$transactionAmount = $_POST['tr_amount'];
		$paidAmount = $_POST['tr_paid'];
		$error = $_POST['tr_error'];
		$transactionDate = $_POST['tr_date'];
		$transactionDescription = $_POST['tr_desc'];
		$CRC = $_POST['tr_crc'];
		$customerEmail = $_POST['tr_email'];
		$md5sum = $_POST['md5sum'];
  
		// check transaction status
		if ($transactionStatus=='TRUE' && $error=='none') {
			/*
			More functions:
			- Verification of md5sum
			- Identifying transaction by CRC
			- Paid amount verification
			- Verification of order status
			*/			
			try {
				$dbh = new PDO('');
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$stmt = $dbh->prepare("UPDATE orders SET `status` = :status WHERE `hash` = :hash");
				$stmt->execute([
					'status' => 'Płatność zakończona',
					'hash' => $CRC
				]);
				echo 'TRUE';
			} catch (PDOException $e) {
				echo 'FALSE';
			}
  
		} else {
			// Transaction processed with error but handled by merchant system
			echo 'TRUE';
		}
   } else {
		echo 'FALSE - Invalid request';
		var_dump($transactionStatus);
		var_dump($error);
   }  
 } else {
	// Display data
	echo('no post');
 }
