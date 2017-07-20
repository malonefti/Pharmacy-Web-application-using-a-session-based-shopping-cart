<?php
	require_once("header.php");
	session_start();
	if(isset($_SESSION["logged"]) && $_SESSION["logged"] == "in")
	{
		$name=$_SESSION["name"];
	}
?>
<div id="wrap">
	<div id="content">
		<?php 
		if (!isset($_GET["option"])){
			if(isset($_SESSION["logged"]) && $_SESSION["logged"] == "in")
			{
				require_once("welcome.php");
			}
			else
			{
				echo '<div id="login">';
					if(isset($_GET["Message"])){
						echo "<p class='error'>Δεν έχετε δικαίωμα πρόσβασης. Παρακαλώ ελέγξτε τα στοιχεία σας και ξαναπροσπαθείστε</p>";
					}	
					echo '		
					<form method="post" action="login.php" class="loginform">
						<input type="hidden" name="LoggingIn" value="true">
						Email: <input id="usermail" class="mail" type="email" name="email" value=""><br>
						Password: <input id="userpass" class="code" type="password" name="pass" value=""><br>
						<input class="button" type="submit" value="Login">
					</form>
				</div>';
			}
		}
		else
		{
			switch($_GET["option"])
			{
				case "logout":
					session_destroy();
					unset($_SESSION["medicines"]);
					unset($_SESSION["cartItems"]);
					echo '<script>window.location = "index.php"</script>';
				break;
				case "error":
					echo '<div id="login">';
						echo "<p class='error'>Για να έχετε πρόσβαση στο ηλεκτρονικό μας φαρμακείο, πρέπει να συνδεθείτε.</p>";
					echo '</div>';
					echo '		
						<form method="post" action="login.php" class="loginform">
							<input type="hidden" name="LoggingIn" value="true">
							Email: <input id="usermail" class="mail" type="email" name="email" value=""><br>
							Password: <input id="userpass" class="code" type="password" name="pass" value=""><br>
							<input class="button" type="submit" value="Login">
						</form>
					</div>';
				break;
				case "medicines":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("classes/Medicines.php");
						require_once("welcome.php");
						$medicineObj = new Medicines();
						if(isset($_GET["page"]) && $_GET["page"]>0){
							$medicines = $medicineObj->getMedicines($_GET["page"]);
						}
						else{
							$medicines = $medicineObj->getMedicines(1);
						}
						if($medicines != false) 
						{
							echo '<div class="medicines">';
							echo '<form method="post" action="">
								<table class="med_list" cellpadding="0" cellspacing="0">
									<tr>
										<td class="head">Κωδικός</td>
										<td class="head">Ονομασία</td>
										<td class="head">Συσκευασία</td>
										<td class="head">Οδηγίες</td>
										<td class="head">Τιμή</td>
										<td class="head">ΦΠΑ</td>
									</tr>';
							for($i=0; $i<sizeof($medicines["code"]); $i++)
							{
								echo '<tr>
										<td class="data"><a href="?option=medicinePage&id='.$medicines["code"][$i].'">'.$medicines["code"][$i].'</a></td>
										<td>'.$medicines["name"][$i].'</td>	
										<td>'.$medicines["pack"][$i].'</td>
										<td>'.$medicines["instructions"][$i].'</td>
										<td>'.$medicines["price"][$i].'</td>
										<td>'.$medicines["fpa"][$i].'</td>
									</tr>';	 
							}
							echo '</table></form></div>';
							if(isset($_GET["page"]) && $_GET["page"]>0)
							{
								$pagination = $medicineObj->getPagination($_GET["page"]);
							}
							else
							{
								$pagination = $medicineObj->getPagination(1);
							}
							
							$previous = '';
							if(!is_null($pagination[0]))
							{
								$previous = '<div><a href="?option=medicines&page='.$pagination[0].'"> << </a></div>';
							}
							
							$current = '';
							if(!is_null($pagination[1]))
							{
								$current = '<div class="current">'.$pagination[1].'</div>';
							}
								
							$next = '';
							if(!is_null($pagination[2]))
							{
								$next = '<div><a href="?option=medicines&page='.$pagination[2].'"> >> </a></div>';
							}
							
							echo '<div class="pagination">'.$previous.' '.$current.' '.$next.'</div>';
							
						}
					}
				break;
				case "medicinePage":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("classes/Medicines.php");
						require_once("welcome.php");
						$medicineObj = new Medicines();
						$medicine = $medicineObj->getSingleMedicine("code");
						if($medicine != false) 
						{
							echo '<div class="details">';
									
								if (isset($_GET['id'])){
									$medCode=$_GET['id'];
									echo "<p>Επιλέξατε να δείτε πληροφορίες για το φάρμακο με κωδικό: ".$medCode."</p>";
								}
							echo '<table class="med_list" cellpadding="0" cellspacing="0">
									<tr>
										<td class="head">Κωδικός</td>
										<td class="head">Ονομασία</td>
										<td class="head">Συσκευασία</td>
										<td class="head">Οδηγίες</td>
										<td class="head">Αρχική Τιμή</td>
										<td class="head">ΦΠΑ</td>
										<td class="head">Τιμή με ΦΠΑ</td>
										<td class="head">Ποσότητα</td>
										<td class="head">Προσθήκη στο καλάθι</td>
									</tr>';
									$query =  "SELECT code,name,pack,instructions,price,fpa FROM medications WHERE code='".$medCode."';";
									$results = mysql_query($query);
									if (!$results) {
										die(mysql_error());
									}
									$rows=mysql_num_rows($results);
									while ($column=mysql_fetch_row($results)){
										foreach ($column as $field){
											echo "<td>".$field."</td>";
										}
									$_SESSION['medicines'] = $column[0];
									$tot=$column[4]+($column[4]*$column[5]);	
									$total=round($tot,2);
									echo '<td>'.$total.'</td>';
									echo '<td class="basket">
												<select name="quantity" id="pieces">
												   <option value="1">1</option>
												   <option value="2">2</option>
												   <option value="3">3</option>
												   <option value="4">4</option>
												   <option value="5">5</option>
												</select>
											</td>';
									
									echo '<td class="basket">
											<a href="?option=addtocart&medicineCode='.$column[0].'"class="addToCart">
												<img src="images/basket.png" width="50" height="50" alt="basket" title="basket" >
											</a>
										  </td>';								
									}
								echo "</table></div>";
								echo '<div class="return"><a href="?option=medicines">Επιστροφή στη λίστα φαρμάκων</p></a></div>';
						}	
					}		
				break;
				case "addtocart":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("classes/Cart.php");
						require_once("welcome.php");
						$cartObj = new Cart();
						$cart = $cartObj->addToCart($_GET["medicineCode"],$_GET["quantity"]);	
						
						echo '<script>window.location = "?option=showCart"</script>';	
					}		
					echo '	
						<div class="return">
								<a href="?option=medicines">Επιστροφή στη λίστα φαρμάκων</p></a>
						</div>';	
				break;
				case "showCart":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("welcome.php");
						if (!empty ($_SESSION["cartItems"]))	
						{						
							require_once("classes/Cart.php");
							$cartObj = new Cart();
							$userCart=$_SESSION["cartItems"];
							$TotalQuantity=0;
							$CartTotal =0;
							echo '<div class="pageTitle">
									<div class="basketFull"><img src="images/basket_full.png" width="64" height="64" alt="basket" title="basket"></div>
									<div class="cartContainer">Περιεχόμενα Καλαθιού</div>
								 </div>';	
							echo '<div class="details" id="cartDetails">';
								echo '<table id="cart" class="med_list" cellpadding="0" cellspacing="0">
										<tr>
											<td class="head">Κωδικός</td>
											<td class="head">Ονομασία</td>
											<td class="head">Συσκευασία</td>
											<td class="head">Οδηγίες</td>
											<td class="head">Τιμή με ΦΠΑ</td>
											<td class="head">Ποσότητα</td>
											<td class="head">Συνολική Τιμή</td>
											<td class="head">Αφαίρεση</td>
										</tr>';
							foreach ($userCart as $key => $value) {
								$PriceWithFpa = ($value[5] + ($value[5] * $value[6]));	
								$TotalAmountperMedicine=$value[7]*$PriceWithFpa;
								$QuantityPerMedicine=$value[7];
								$TotalQuantity +=$QuantityPerMedicine;
								$CartTotal +=$TotalAmountperMedicine;
								echo '<tr>
										<td class="dataCart">'.$value[1].'</td>
										<td class="dataCart">'.$value[2].'</td>	
										<td class="dataCart">'.$value[3].'</td>
										<td class="dataCart">'.$value[4].'</td>
										<td class="dataCart">
											<input type="text" name="firstPrice" id="firstPrice'.$value[1].'" class="firstPrice" size="6" value="'.round($PriceWithFpa,2).'"disabled>
										</td>
										<td class="dataCart">
											<a onclick="" href="?option=removeQuan&medicineCode='.$value[1].'" id="subtraction" class="addremove">-</a>
												<input type="text" name="quantity" id="'.$value[1].'" class="quantity" value="'.$value[7].'"disabled>
											<a onclick="" href="?option=addQuan&medicineCode='.$value[1].'" id="add" class="addremove">+</a>
										</td>
										<td class="dataCart">
												<input type="text" name="totalAmount" id="totalAmount'.$value[1].'" class="totalAmount" size="6" value="'.round($TotalAmountperMedicine,2).'"disabled>
										</td>
										<td class="delete"><a onclick="" href="?option=deletefromcart&medicineCode='.$value[1].'">
											<img src="images/delete.png" width="50" height="50" alt="delete" class="delete" ></a></td>
									</tr>';
							}
							echo '</table>
								  <div class="eraseCart"><a href="?option=erasecart">Αδειασμα καλαθιου</a></div>
								</div>';
							echo '<div class="totals">
									<div class="totalQuantity">Τεμάχια στο καλάθι: <input type="text" name="quantity2" id="quantity2" class="quantity2" value="'.$TotalQuantity.'"disabled></div>
									<div class="totalAmount">Συνολικό ποσό: <input type="text" name="totalAmount2" id="totalAmount2" class="totalAmount2" size="6" value=" '.round($CartTotal,2).' &euro;"disabled></div>
									<div id="finish" class="finish">
										<a href="?option=orderCheck" class="orderCheck">
										<input id="buy" class="buy" type="button" value="Ολοκλήρωση Αγορών"/></a>
									</div>
								</div>';
							echo '<div class="return"><a href="?option=medicines">Συνέχεια Αγορών</p></a></div>';
						}		
						else
						{
							echo '<div class="emptyCart">Το καλάθι σας είναι άδειο.</div>';
							echo '<div class="return"><a href="?option=medicines">Επιστροφή στη λίστα φαρμάκων</p></a></div>';
						}
					}	
				break;
				case "addQuan":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("classes/Cart.php");
						$cartObj = new Cart();
						$cart = $cartObj->addQuan($_GET["medicineCode"]);
						echo '<script>window.location = "?option=showCart"</script>';
					}
				break;
				case "removeQuan":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("classes/Cart.php");
						$cartObj = new Cart();
						$cart = $cartObj->removeQuan($_GET["medicineCode"]);
						echo '<script>window.location = "?option=showCart"</script>';
					}
				break;
				case "deletefromcart":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("classes/Cart.php");
						$cartObj = new Cart();
						$cart = $cartObj->deleteFromCart($_GET["medicineCode"]);
						echo '<script>window.location = "?option=showCart"</script>';
					}
				break;
				case "erasecart":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("welcome.php");
						require_once("classes/Cart.php");
						$cartObj = new Cart();
						$cart = $cartObj->eraseCart();
						echo '<div class="emptyCart">Το καλάθι σας είναι άδειο.</div>';
						echo '<div class="return"><a href="?option=medicines">Επιστροφή στη λίστα φαρμάκων</p></a></div>';
					}
				break;	
				case "orderCheck":
					if(empty($_SESSION["logged"]))
					{
						echo '<script>window.location = "index.php?option=error"</script>';
					}
					else
					{
						require_once("welcome.php");
						require_once("classes/Cart.php");
						$cartObj = new Cart();
						$cart = $cartObj->orderCheck();
						echo '<div class="emptyCart">Η παραγγελία καταχωρήθηκε.</div>';
						echo '	
						<div class="return">
							<a href="?option=medicines">Επιστροφή στη λίστα φαρμάκων</p></a>
						</div>';						
					}
				break;	
			}
		} 
	require_once("footer.php");
?>