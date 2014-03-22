<section class="login-wrapper">
	<header>
		<div class="logo"><img src="<?= $logoURL ?>" alt=""></div>
	</header>
	<form action="" method="post">
		<?php if($_POST && !$response->result) { ?>
		<div class="error"><?= $response->message ?></div>
		<?php } else if($_POST && $response->result) { ?>
		<div class="valid"><?= $response->message ?></div>
		<?php } ?>
		<ul>
			<li>
				<input type="text" name="email" autofocus value="<?= $_POST['email'] ?>" placeholder="Email address">
			</li>
			<li>
				<button type="submit">RESET MY PASSWORD</button>
			</li>
			<li class="forgot">
				<a href="/mightycms/">Back to login page</a>
			</li>
		</ul>
	</form>
</section>