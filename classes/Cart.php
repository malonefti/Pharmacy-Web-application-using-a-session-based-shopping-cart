<?php

/*Δημιουργία κλάσης Cart, με τις ιδιότητες τους*/
class Cart{
	
	private $medicineCode;
	private $medicineQuantity;
	private $medicinePrice;
	
/*Δημιουργία constructor*/		
	public function __construct() {

	}

/*Δημιουργία μεθόδου για την προσθήκη των προιόντων στο καλάθι αγορών*/		
	public function addToCart($medicineCode,$quantity)
	{
		$_SESSION["medicineCode"] = (empty($_SESSION["code"])) ? array() : $_GET["medicineCode"];
		$_SESSION["quantity"] = (empty($_GET["quantity"])) ? array() : $_GET["quantity"];
		$this->medicineCode = $medicineCode;
		$this->quantity = $quantity;
		$code=$_GET['medicineCode'];
		array_push($_SESSION["medicineCode"],$this->medicineCode);
		require_once("Medicines.php");
		$userId=$_SESSION["userCode"];
		if(!isset($_SESSION["cartItems"])){
			$_SESSION["cartItems"] = array();
		}
		else{
			$cart=$_SESSION['cartItems'];
			foreach ($cart as $key => $value) {
				if($code==$value[1]){	
					$value[7]=$value[7]+$quantity;
					if($value[7]>=5){
						echo '<script>alert("Το προϊόν υπάρχει ήδη στο καλάθι. Μπορείτε να παραγγείλετε μόνο μέχρι 5 τεμάχια ανα προϊόν.");</script>';	
						$_SESSION['cartItems'][$key][7]=$value[7]-$quantity;
						unset($_SESSION['medicineCode']);
					}
					else{
						echo '<script>alert("Το προϊόν υπάρχει ήδη στο καλάθι και αυξήθηκε η ποσότητά του κατά '.$quantity.' τεμάχια");</script>';
						$_SESSION['cartItems'][$key][7]=$value[7];
						unset($_SESSION['medicineCode']);
					}	
				}
			}	
		}
		if(!empty($_SESSION["medicineCode"]))
		{
			foreach($_SESSION["medicineCode"] as $item)
			{
				$medicine = new Medicines($item);
				$medicineExists = $medicine->getMedicine($item);
				if($medicineExists == true)
				{
					$code=$medicine->getCode();
					$pname=$medicine->getName();
					$pack=$medicine->getPack();
					$instructions=$medicine->getInstructions();
					$price=$medicine->getPrice();
					$fpa=$medicine->getfpa();
					$quantity=$_SESSION["quantity"];
				}
			}
			$userCart=array($userId, $code, $pname,$pack,$instructions,$price,$fpa, $quantity);
			array_push($_SESSION["cartItems"], $userCart);	
		}
		
	}	
/*Δημιουργία μεθόδου για την εμφανιση των προιόντων */			
	public function showCart()
	{
		$medicines["code"] = array();
		$medicines["name"] = array();
		$medicines["pack"] = array();
		$medicines["instructions"] = array();
		$medicines["price"] = array();
		$medicines["fpa"] = array();
		$medicines["quantity"] = array();
		require_once("Medicines.php");
		
		if(!empty($_SESSION["cartItems"]))
		{
			foreach($_SESSION["cartItems"] as $item => $value)
			{
				array_push($medicines["code"],$value[1]);
				array_push($medicines["name"],$value[2]);
				array_push($medicines["pack"],$value[3]);
				array_push($medicines["instructions"],$value[4]);
				array_push($medicines["price"],$value[5]);
				array_push($medicines["fpa"],$value[6]);
				array_push($medicines["quantity"],$value[7]);
			}
			return $medicines;
		}
		else
		{
			return false;
		}
	}

/*Δημιουργία μεθόδου για την αυξηση ποσότητας ενός προίοντος στο καλάθι αγορών*/		
	public function addQuan($medicineCode)
	{
		$code=$_GET['medicineCode'];
		$userCart=$_SESSION['cartItems'];
		foreach ($userCart as $key => $value) {
			if($code==$value[1]){
				if($value[7]==5){
					echo '<script>alert("Μπορείτε να παραγγείλετε μέχρι 5 τεμάχια ανα προϊόν.");</script>';	
				}
				else{
					$value[7]=$value[7]+1;
					$_SESSION['cartItems'][$key][7]=$value[7];
				}
			}		
		}
	}
	
/*Δημιουργία μεθόδου για την μείωση ποσότητας ενός προίοντος στο καλάθι αγορών*/		
	public function removeQuan($medicineCode)
	{
		$code=$_GET['medicineCode'];
		$userCart=$_SESSION['cartItems'];
		foreach ($userCart as $key => $value) {
			if($code==$value[1]){
				if($value[7]==1){
					echo '<script>alert("Η ποσότητα δεν μπορεί να είναι μικρότερη του 1 τεμαχίου. Αν δεν επιθυμειτέ το προϊόν παρακαλώ διαγράψτε το χρησιμοποιώντας το x στην τελευταία στήλη.");</script>';	
				}
				else{
					$value[7]=$value[7]-1;
					$_SESSION['cartItems'][$key][7]=$value[7];
				}
			}	
		}
	}	
	
/*Δημιουργία μεθόδου για την διαγραφή των προιόντων στο καλάθι αγορών*/		
	public function deleteFromCart($medicineCode)
	{
		$code=$_GET['medicineCode'];
		$userCart=$_SESSION['cartItems'];
		foreach ($userCart as $key => $value) {
			if($code==$value[1]){
				unset($_SESSION['cartItems'][$key]);
			}	
			$_SESSION["cartItems"] = array_values($_SESSION["cartItems"]);
		}
	}
	
/*Δημιουργία μεθόδου για την διαγραφή του καλάθιου αγορών*/		
	public function eraseCart()
	{
		unset($_SESSION["cartItems"]);
	}


/*Δημιουργία μεθόδου για την ολοκλήρωση των αγορών*/	
	public function orderCheck()
	{
		$user = $_SESSION["id"];
		$userName=$_SESSION["name"];
		$userAFM=$_SESSION["userCode"];
		$date=date("d/m/y H:i:s",$_SERVER['REQUEST_TIME']); 
		$time=date("H;i;s",$_SERVER['REQUEST_TIME']); 
		$currentMedicine = $userName;
		$currentMedicine.="  -  Η παραγγελία καταχωρήθηκε στις ".$date;
		$currentMedicine .= "\r\n************************************************************************\r\n";
		$medicines = $this->showCart();
		$totalPrice =0;
		for($i=0; $i<sizeof($medicines["code"]); $i++)
		{
			$file = fopen('c:\\xampp\\MedicalOrders\\'.$userAFM.'_'.$time.'.txt', 'a+')or exit("An error occurred!");
			$priceOne = round($medicines["price"][$i] + ($medicines["price"][$i] * $medicines["fpa"][$i]),2);
			$priceAll = round($medicines["quantity"][$i] * ($medicines["price"][$i] + ($medicines["price"][$i] * $medicines["fpa"][$i])),2);
			$currentMedicine .= "\r\nΚωδικός Φαρμάκου: ".$medicines["code"][$i]."\r\nΟνομασία: ".$medicines["name"][$i]."\r\nΠοσότητα: ".$medicines["quantity"][$i]."\r\nΑρχική Τιμή: ". $medicines["price"][$i] ."€\r\nΦΠΑ: ".$medicines["fpa"][$i]."\r\nΤιμή Με ΦΠΑ: ". $priceOne ."€\r\nΤελική Τιμή: ".$priceAll."€\r\n";
			$totalPrice = $totalPrice + $priceAll;
		}
		$currentMedicine .= "\r\nΣυνολική τιμή παραγγελίας: ".round($totalPrice,2)."€\r\n";
		$currentMedicine .= "\r\n************************************************************************\r\n\r\n\r\n\r\n";
		fwrite($file, $currentMedicine);
		$this->eraseCart();
	}
	
	public function getCart()
	{
		return $this->cart;
	}
	public function getMedicineCode()
	{
		return $this->medicineCode;
	}
	
	public function getMedicineQuantity()
	{
		return $this->quantity;
	}
	
	public function setMedicineQuantity($medicineQuantity)
	{
		$this->medicineQuantity = $quantity;
	}
	
	public function getMedicinePrice()
	{
		return $this->medicinePrice;
	}
	
	public function setMedicinePrice($medicinePrice)
	{
		$this->medicinePrice = $medicinePrice;
	}
}
?>