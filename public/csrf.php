<?php

$email = ['outlook', 'hotmail', 'gmail', 'yahoo'];

session_start();

if (empty($_SESSION['key'])) {
	$_SESSION['key'] = bin2hex(random_bytes(32));
	print 'SESSION[\'KEY\'] es igual a: ' . $_SESSION['key'];
}

$csrf = hash_hmac('sha256', 'csrf.php', $_SESSION['key']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if (!empty($_POST['csrftoken']) && hash_equals($csrf, $_POST['csrftoken'])) {
		print 'Todo en orden';
	} else {
		print 'CSRF ATTACK';
	}
}

?>
<!DOCTYPE html>
<!-- https://html.spec.whatwg.org/ -->
<!-- https://www.w3.org/Style/Examples/007/center.en.html -->
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cross-Site Request Forgery</title>
</head>

<body>

	<section style="border: 2px solid #eb2188;
		background: #080a52;
        color: white;
        border-radius: 1em;
        padding: 1em;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -50%;
        transform: translate(-50%, -50%)">
		<h1>Nicely centered</h1>
		<p>This text block is vertically centered.
			<p>Horizontally, too, if the window is wide enough.
	</section>

	<div style="
		border: 1px solid crimson;
		height: 10em;
		position: relative">
		<p style="margin: 0;
			position: absolute;
			top: 50%;
			transform: translate(0, -50%)">
			Hola Mundo!</p>
	</div>

	<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
		<div><label for="">Usuario: <input type="text" name="username"></label></div>
		<div><label for="">Contraseña: <input type="password" name="password"></label></div>
		<input type="text" name="csrftoken" value="<?= $csrf ?>">
		<p><button type="submit">Enviar</button></p>
	</form>

</body>

</html>

<body>
	<header>
		<h1>Space Teddy Inc.</h1>
	</header>
	<nav aria-labelledby="mainnavheader">
		<h2 id="mainnavheader">Navigation Menu</h2>
		…
	</nav>
	<main>
		<article>
			<h2>An inside look at the new Space Teddy 6</h2>
			<nav aria-labelledby="tocheader">
				<h3 id="tocheader">Table of Contents</h3>
				…
			</nav>
			<p>…</p>
			<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			<p>…</p>
			<ul>
				<li>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
				<li>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</li>
				<li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</li>
			</ul>
			<h3>Cotton Fur</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			<p>…</p>
			<aside aria-labelledby="relatedheader">
				<h3 id="relatedheader">Related Articles</h3>
				<ul>
					<li><a href="#">Related Article Title 1</a></li>
					<li><a href="#">Related Article Title 2</a></li>
					<li><a href="#">Related Article Title 3</a></li>
				</ul>
			</aside>
		</article>
		<aside aria-labelledby="latestheader">
			<h3 id="latestheader">Latest Articles</h3>
			<ul>
				<li><a href="#">Latest Article Title 1</a></li>
				<li><a href="#">Latest Article Title 2</a></li>
				<li><a href="#">Latest Article Title 3</a></li>
			</ul>
		</aside>
	</main>
	<footer>
		<p>© SpaceTeddy Inc.</p>
	</footer>


	<form method="post" enctype="application/x-www-form-urlencoded" action="https://pizza.example.com/order.cgi">
		<p><label>Customer name: <input name="custname" required autocomplete="shipping name"></label></p>
		<p><label>Telephone: <input type=tel name="custtel" autocomplete="shipping tel"></label></p>
		<p><label>Buzzer code: <input name="custbuzz" inputmode="numeric"></label></p>
		<p><label>E-mail address: <input type=email name="custemail" autocomplete="shipping email"></label></p>
		<fieldset>
			<legend> Pizza Size </legend>
			<p><label> <input type=radio name=size required value="small"> Small </label></p>
			<p><label> <input type=radio name=size required value="medium"> Medium </label></p>
			<p><label> <input type=radio name=size required value="large"> Large </label></p>
		</fieldset>
		<fieldset>
			<legend> Pizza Toppings </legend>
			<p><label> <input type=checkbox name="topping" value="bacon"> Bacon </label></p>
			<p><label> <input type=checkbox name="topping" value="cheese"> Extra Cheese </label></p>
			<p><label> <input type=checkbox name="topping" value="onion"> Onion </label></p>
			<p><label> <input type=checkbox name="topping" value="mushroom"> Mushroom </label></p>
		</fieldset>
		<p><label>Preferred delivery time: <input type=time min="11:00" max="21:00" step="900" name="delivery" required></label></p>
		<p><label>Delivery instructions: <textarea name="comments" maxlength=1000></textarea></label></p>
		<p><button>Submit order</button></p>
	</form>
</body>