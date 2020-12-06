<?php if (
	session()->has('message') ||
	session()->has('error') ||
	session()->has('errors')
) : ?>
	<div class="message">
		<?php if (session()->has('message')) : ?>
			<div class="feedback info" role="alert">
				<?= \nl2br(session('message')) ?>
			</div>
		<?php endif ?>
		<?php if (session()->has('error')) : ?>
			<div class="feedback alert" role="alert">
				<?= \nl2br(session('error')) ?>
			</div>
		<?php endif ?>
		<?php if (session()->has('errors')) : ?>
			<ul class="feedback alert" role="alert">
				<?php foreach (session('errors') as $error) : ?>
					<li><?= \nl2br($error) ?></li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>
	</div>
<?php endif ?>
