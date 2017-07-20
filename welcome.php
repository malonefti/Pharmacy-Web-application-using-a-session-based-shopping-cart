<?php 
	echo '<div class="welcome">
		<p> Καλώς ήρθατε στο Medical Shop '.$name.'</p>
		<form method="post" action="login.php">
			<input type="hidden" name="logout" value="true">
			<p class="logout"><a href="index.php?option=logout">Αποσύνδεση</a></p>
		</form>';
		if (isset ($_SESSION["cartItems"])){
			echo '<p class="logout"><a href="index.php?option=showCart" class="myCart">Το καλάθι μου</a></p>';
		}
	echo '</div>';
?>