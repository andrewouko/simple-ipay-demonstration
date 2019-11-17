<?php

	error_reporting(E_ALL); ini_set('display_errors', 'On');
    ob_start();
    session_start();

    function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT-3');
    header('Content-type: text/html; charset=utf-8');


    //$vendorId  = "test";
    //$hashKey   = "654ekju58xdlps54rw";

    $vendorId  = "demo";
    $hashKey   = "demoCHANGED";
	$liveStatus= 0;

	$oid	= substr(uniqid(), 0, 5);//Still not unique enough. Lol
	$oid = uniqid();
	$inv	= $oid;

	
	$url_host = 'https://payments.ipayafrica.com/v3/ke';

	if(isset($_GET["txncd"]))
	{
		$val           = $vendorId;
		$val1          = $_GET['id'];
		$val2          = $_GET['ivm'];
		$val3          = $_GET['qwh'];
		$val4          = $_GET['afd'];
		$val5          = $_GET['poi'];
		$val6          = $_GET['uyt'];
		$val7          = $_GET['ifd'];


		// $ipnUrl        = "https://www.ipayafrica.com/ipn/?vendor=".$val."&id=".$val1."&ivm=".$val2."&qwh=".$val3."&afd=".$val4."&poi=".$val5."&uyt=".$val6."&ifd=".$val7;

		$ipnUrl        = $url_host . "/v1/tg/ipn/check?vendor=".$val."&id=".$val1."&ivm=".$val2."&qwh=".$val3."&afd=".$val4."&poi=".$val5."&uyt=".$val6."&ifd=".$val7;
		
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ipnUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$status = curl_exec($ch);
		curl_close($ch);
		

		switch ($status) {
			case 'aei7p7yrx4ae34':
				$state = "successful";
				break;

			case 'fe2707etr5s4wq':
				$state = "failed";
				break;

			case 'bdi6p2yy76etrs':
				$state = "pending";
				break;

			case 'dtfi4p7yty45wq':
				$state = "paid less";
				break;

			case 'eq3i7p5yt7645e':
				$state = "paid more";
				break;

			case 'cr5i3pgy9867e1':
				$state = " used";
				break;
			
			default:
				$state = "has unxpected result";
				break;
		}

		exit($status." means transaction ".$state."!!!");
	}
	elseif(isset($_POST['ttl']))
	{
		//Data needed by iPay a fair share of it obtained from the user from a form e.g email, number etc...

		$curr	= isset($_POST['curr'])?$_POST['curr']:exit('Currency is required');
		$ttl	= isset($_POST['ttl'])?$_POST['ttl']:exit('Amount is required');
		$tel	= isset($_POST['tel'])?$_POST['tel']:exit('Phone number is required');
		$eml	= isset($_POST['eml'])?$_POST['eml']:exit('Email is required');
		$p1		= isset($_POST['p1'])?$_POST['p1']:'';
		$p2		= isset($_POST['p2'])?$_POST['p2']:'';
		$p3		= isset($_POST['p3'])?$_POST['p3']:'';
		$p4		= isset($_POST['p4'])?$_POST['p4']:'';

		$fields = array("live"	=> $liveStatus,
		                "oid"	=> $oid,
		                "inv"	=> $inv,
		                "ttl"	=> $ttl,
		                "tel"	=> $tel,
		                "eml"	=> $eml,
		                "vid"	=> $vendorId,
		                "curr"	=> $curr,
		                "p1"	=> $p1,
		                "p2"	=> $p2,
		                "p3"	=> $p3,
		                "p4"	=> $p4,
		                "cbk"	=> "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
		                "lbk" 	=> "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
		                "cst"	=> "1",
		                "crl"	=> "0",
		                "autopay" => "0",
		                "creditcard" => "1",
		                "tmoney" => "1",
		                "flooz" => "1"
		                );
		
		$datastring = $fields['live'].$fields['oid'].$fields['inv'].$fields['ttl'].$fields['tel'].$fields['eml'].$fields['vid'].$fields['curr'].$fields['p1'].$fields['p2'].$fields['p3'].$fields['p4'].$fields['cbk'].$fields['cst'].$fields['crl'];
		
		$generated_hash = hash_hmac('sha1',$datastring , $hashKey);

		?>
		<!--    Generate the form for redirecting to iPay hosted payment page-->
		<!-- <form method="post" id="ipayform" action="https://payments.elipa.co.tz/v3/tz"> -->
		<form method="post" id="ipayform" action="<?php echo $url_host /* . "/v1/tg/" */ ?>">

			<?php 
			foreach ($fields as $key => $value) {
				echo $key;
				echo '&nbsp;:<input name="'.$key.'" type="text" value="'.$value.'"></br>';
			}
			?>
			hsh:&nbsp;<input name="hsh" type="text" value="<?php echo $generated_hash ?>" ><br>
			<button type="submit">&nbsp;Lipa&nbsp;</button>
			<?php echo "Loading..."; ?>
		</form>
		<script>
			document.getElementById('ipayform').submit();
		</script>
	<?php
	}

	else 
	{
		?>
		<!--    Generate the form  -->
		<form method="post" action="index.php">
			live&nbsp;:<input name="live" type="text" value=<?php echo $liveStatus;?>></br>
			oid&nbsp;:<input name="oid" type="text" value=<?php echo $oid;?>></br>
			inv&nbsp;:<input name="inv" type="text" value=<?php echo $inv;?>></br>
			curr&nbsp;:<input name="curr" type="text" value=<?php echo "KES";?>></br>
			ttl&nbsp;:<input name="ttl" type="text" value="10"></br>
			tel&nbsp;:<input name="tel" type="text" value="22870163796"></br>
			eml&nbsp;:<input name="eml" type="text" value="test@ipayafrica.com"></br>
			p1&nbsp;:<input name="p1" type="text" value=""></br>
			p2&nbsp;:<input name="p2" type="text" value=""></br>
			p3&nbsp;:<input name="p3" type="text" value=""></br>
			p4&nbsp;:<input name="p4" type="text" value=""></br>
			<button type="submit">&nbsp;Lipa&nbsp;</button>
		</form>
		<?php
	}

?>
