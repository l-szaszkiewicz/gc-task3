<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Shop</title>
	<style>
		img {
			width: 100%;
		}
		.products {
			display: flex;
			justify-content: space-between;
		}
		.products > div {
			flex-basis: 30%;
			display: flex;
			flex-direction: column
		}
		.products a:hover {
			opacity: .65;
		}
	</style>
</head>
<body>
<?php
	function add_order ($data) {
		try {
			$dbh = new PDO('');
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("INSERT INTO orders SET name = :name, email = :email, amount = :amount, status = :status, hash = :hash, date = :date");
			$stmt->execute($data);
			return (int) 1;
		} catch (PDOException $e) {
			return (int) 0;
		}
	}
	function select_order ($hash) {
		try {
			$dbh = new PDO('');
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("SELECT * FROM orders WHERE hash = :hash");
			$stmt->execute(['hash' => $hash]);
			return (array) $stmt->fetchAll();
		} catch (PDOException $e) {
			return (int) 0;
		}
	}

	if(isset($_GET['hash'])) {
		$data = select_order($_GET['hash']);
		?>
		<h2>Status płatności: <?= $data[0]['status']?></h2>
		<?php
	} else if(!isset($_GET['product'])){
		include('products.php');
	}
	if (isset($_GET['product'])) {
		$amount = [
			'fiat' => 250.00,
			'subaru' => 600.00,
			'bmw' => 720.50
		];
		$date = date("Y-m-d H:i:s");
		$crc = md5(implode('&', [$_GET['product'], $date]));
		$md = md5(implode('&', [1010, $amount[$_GET['product']], $crc, 'demo']));

		add_order([
			'name' => 'John Doe',
			'email' => 'john.doe@example.com',
			'amount' => $amount[$_GET['product']],
			'status' => 'Pending',
			'hash' => $crc,
			'date' => $date
		])
?>				
			<form action='https://secure.tpay.com' method='POST'>
				<input type='hidden' name='id' value='1010'/>
				<input type='hidden' name='name' value='John Doe'/>
				<input type='hidden' name='email' value='john.doe@example.com'/>
				<input type='hidden' name='amount' value='<?= $amount[$_GET['product']] ?>'/>
				<input type='hidden' name='crc' value='<?= $crc ?>'/>
				<input type='hidden' name='description' value='<?= $_GET['product'] ?>'/>
				<input type='hidden' name='md5sum' value='<?= $md ?>'/>
				<input type='hidden' name='return_url' value='http://szaszkiewicz.kylos.pl?hash=<?= $crc ?>'/>
				<input type='hidden' name='result_url' value='http://szaszkiewicz.kylos.pl/result.php'/>

				<button type='submit'>Zapłać</button>
			</form>
		<?php
	}
?>
</body>
</html>