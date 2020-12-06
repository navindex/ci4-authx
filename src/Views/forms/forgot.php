<?= $this->extend($auth->viewLayout) ?>

<?= $this->section('form') ?>
<?= form_open(route_to('forgot')) ?>
<?= $this->include('partials/auth/logo') ?>
<h1 class="item"><?= lang('Auth.form.forgot.title') ?></h1>
<p class="item detail"><?= lang('Auth.form.enterEmailForInstructions') ?></p>
<?= $this->include('partials/auth/session_messages') ?>
<div class="item">
	<?= form_input([
		'name'     => 'email',
		'id'       => 'email',
		'type'     => 'email',
		'class'    => session('errors.email') ? 'is-invalid' : '',
		'required' => 'required',
	]) ?>
	<?= form_label(lang('Auth.form.email'), 'email') ?>
	<div class="badges">
		<?php if (!empty(session('errors.email'))) : ?>
			<div class="icon alert" tabindex="0">
				<p class="bubble"><?= session('errors.email') ?></p>
			</div>
		<?php endif ?>
	</div>
</div>
<?php if (isset($keys['recaptcha'])) : ?>
	<?= form_input([
		'type'         => 'hidden',
		'name'         => 'recaptcha',
		'class'        => 'recaptcha',
		'data-sitekey' => $keys['recaptcha'] ?? '',
	]) ?>
<?php endif ?>
<?= form_button([
		'name'  => 'submit',
		'type'  => 'submit',
		'class' => 'item g-recaptcha',
	], lang('Auth.form.forgot.action')) ?>
<?= form_close() ?>
<?= $this->endSection() ?>
