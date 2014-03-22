<section class="login-wrapper">
	<header>
		<div class="logo"><img src="<?= $logoURL ?>" alt=""></div>
	</header>
	<form action="" method="post">
		<?php if($response) { ?>
		<div class="error"><?= $response ?></div>
		<?php } ?>
		<ul>
			<li>
				<input type="text" name="username" autofocus value="<?= $_POST['username'] ?>" placeholder="username">
			</li>
			<li>
				<input type="password" name="password" value="" placeholder="password">
			</li>
			<!-- <li class="remember">
				<input type="checkbox" <?= (isset($_POST['remember']) ? 'checked="checked"' : '') ?> name="remember" id="remember">
				<label for="remember">Remember me for 2 weeks</label>
			</li> -->
			<li>
				<button type="submit">LOGIN</button>
			</li>
			<li class="forgot">
				<a href="/mightycms/forgotten-password">Forgot your password?</a>
			</li>
		</ul>
	</form>
</section>