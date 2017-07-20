<?php
/*Δημιουργία κλάσης Medicines, με τις ιδιότητες τους*/
class Medicines{
		
	private $code;
	private $name;
	private $pack;
	private $instructions;
	private $price;
	private $fpa;
	
/*Δημιουργία constructor*/	
	public function __construct($code = null)
	{
		if(!is_null($code))
			$this->code = $code;
	}

/*Δημιουργία μεθόδου για την εμφάνιση των προιόντων ανά σελίδα*/		
	public function getMedicines($page)
	{
		require_once("\..\db.php");
		$medicines["code"] = array();
		$medicines["name"] = array();
		$medicines["pack"] = array();
		$medicines["instructions"] = array();
		$medicines["price"] = array();
		$medicines["fpa"] = array();
		$from = ($page*pageSize)-pageSize;
		$to=pageSize;
		$query = 'select * from medications ORDER BY  aa ASC  limit '.$from.','.$to.' ;';
		//echo $query;
		$result = mysql_query($query);
		if (!$result) { 
			die('Invalid query: ' . mysql_error());
		}
		while($medicine=mysql_fetch_array($result))
		{ 	
			array_push($medicines["code"],$medicine["code"]);
			array_push($medicines["name"],$medicine["name"]);
			array_push($medicines["pack"],$medicine["pack"]);
			array_push($medicines["instructions"],$medicine["instructions"]);
			array_push($medicines["price"],$medicine["price"]);
			array_push($medicines["fpa"],$medicine["fpa"]);
		}	
		return $medicines;	
	}

/*Δημιουργία μεθόδου για την σελιδοποίηση*/		
	public function getPagination($page)
	{
		require_once("\..\db.php");
				
		$query =('select count(*) from medications');
		$result = mysql_query($query);
		if (!$result) { 
			die('Invalid query: ' . mysql_error());
		}
		$medicineQ=mysql_fetch_array($result);

		$pages = ceil($medicineQ[0] / pageSize);
		if($pages>0 && $page<$pages)
		{
			$next = $page;
			$next++;
		}
		else
		{
			$next = null;
		}
		
		if($page>1)
		{
			$previous = $page;
			$previous--;
		}
		else
		{
			$previous = null;
		}
		
		$pagination = array($previous,$page,$next);
		return $pagination;
	}
/*Δημιουργία μεθόδου για την εμφάνιση του εκάστοτε προιόντος στη σελίδα με τις λεπτομέρειες του*/		
	public function getSingleMedicine($code)
	{
		require_once("\..\db.php");
		$sm["code"] = array();
		$sm["name"] = array();
		$sm["pack"] = array();
		$sm["instructions"] = array();
		$sm["price"] = array();
		$sm["fpa"] = array();
		$query =('select * from medications where code="'.$code.'"');
		//echo $query;
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
	
		if (!$result) { 
			die('Invalid query: ' . mysql_error());
		}
		while($medicine=mysql_fetch_array($result))
		{
			array_push($sm["code"],$medicine["code"]);
			array_push($sm["name"],$medicine["name"]);
			array_push($sm["pack"],$medicine["pack"]);
			array_push($sm["instructions"],$medicine["instructions"]);
			array_push($sm["price"],$medicine["price"]);
			array_push($sm["fpa"],$medicine["fpa"]);
		}
		return $sm;		
	}
	
/*Δημιουργία μεθόδου για την εμφάνιση των προιόντων στο καλάθι αγορών*/	
	public function getMedicine($code)
	{
		require_once("\..\db.php");
		if (isset($_SESSION["medicineCode"])){			
			$medCode=$_SESSION["medicineCode"][0];	
		}
		$query=('select count(code) from medications where code="'.$medCode.'"');
		//echo $query;
		$result = mysql_query($query);
		if (!$result) { 
			die('Invalid query: ' . mysql_error());
		}
		$medicineExists=mysql_fetch_array($result);

		if($medicineExists["count(code)"] != 0)
		{
			$query =('select * from medications where code="'.$medCode.'"');
			//echo $query;
			$result = mysql_query($query);
			$medicines = mysql_fetch_array($result);
			if (!$result) { 
				die('Invalid query: ' . mysql_error());
			}
			$this->code= $medicines["code"];
			$this->name= $medicines["name"];
			$this->pack= $medicines["pack"];
			$this->instructions= $medicines["instructions"];
			$this->price= $medicines["price"];
			$this->fpa= $medicines["fpa"];

			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getCode()
	{
		return $this->code;
	}
	
	public function setCode($code)
	{
		$this->code = $code;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function getPack()
	{
		return $this->pack;
	}
	
	public function setPack($pack)
	{
		$this->pack = $pack;
	}
	
	public function getInstructions()
	{
		return $this->instructions;
	}
	
	public function setInstructions($instructions)
	{
		$this->instructions = $instructions;
	}
	
	public function getPrice()
	{
		return $this->price;
	}
	
	public function setPrice($price)
	{
		$this->price = $price;
	}
	
	public function getFpa()
	{
		return $this->fpa;
	}
	
	public function setFpa($fpa)
	{
		$this->fpa = $fpa;
	}

}
?>