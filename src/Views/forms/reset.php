<?= $this->extend($auth->viewLayout) ?>

<?= $this->section('form') ?>
<?= form_open(route_to('reset-password')) ?>
<?= $this->include('partials/auth/logo') ?>
<h1 class="item"><?= lang('Auth.form.reset.title') ?></h1>
<?= $this->include('partials/auth/session_messages') ?>
<p class="item detail"><?= lang('Auth.form.enterCodeEmailPassword') ?></p>
<div class="item">
	<?= form_input([
		'name'     => 'token',
		'id'       => 'token',
		'type'     => 'text',
		'class'    => session('errors.token') ? 'is-invalid' : '',
		'value'    => old('token') ?? '',
		'required' => 'required',
	]) ?>
	<?= form_label(lang('Auth.form.token'), 'token') ?>
	<div class="badges">
		<?php if (!empty(session('errors.token'))) : ?>
			<div class="icon alert" tabindex="0">
				<p class="bubble"><?= session('errors.token') ?></p>
			</div>
		<?php endif ?>
	</div>
</div>
<div class="item">
	<?= form_input([
		'name'             => 'email',
		'id'               => 'email',
		'type'             => 'email',
		'class'            => session('errors.email') ? 'is-invalid' : '',
		'aria-describedby' => 'email-help',
		'value'            => old('email') ?? '',
		'required'         => 'required',
	]) ?>
	<?= form_label(lang('Auth.form.email'), 'email') ?>
	<div class="badges">
		<div class="icon info" tabindex="0">
			<p id="email-help" class="bubble"><?= lang('Auth.form.weNeverShare') ?></p>
		</div>
		<?php if (!empty(session('errors.email'))) : ?>
			<div class="icon alert" tabindex="0">
				<p class="bubble"><?= session('errors.email') ?></p>
			</div>
		<?php endif ?>
	</div>
</div>
<div class="item">
	<?= form_input([
		'name'         => 'password',
		'id'           => 'password',
		'type'         => 'password',
		'class'        => session('errors.newPassword') ? 'is-invalid' : '',
		'autocomplete' => 'off',
		'required'     => 'required',
	]) ?>
	<?= form_label(lang('Auth.form.password'), 'password') ?>
	<div class="badges">
		<?php if (!empty(session('errors.password'))) : ?>
			<div class="icon alert" tabindex="0">
				<p class="bubble"><?= session('errors.password') ?></p>
			</div>
		<?php endif ?>
	</div>
</div>
<div class="item">
	<?= form_input([
		'name'         => 'pass_confirm',
		'id'           => 'pass-confirm',
		'type'         => 'password',
		'class'        => session('errors.pass_confirm') ? 'is-invalid' : '',
		'autocomplete' => 'off',
		'required'     => 'required',
	]) ?>
	<?= form_label(lang('Auth.form.repeatNewPassword'), 'pass-confirm') ?>
	<div class="badges">
		<?php if (!empty(session('errors.pass_confirm'))) : ?>
			<div class="icon alert" tabindex="0">
				<p class="bubble"><?= session('errors.pass_confirm') ?></p>
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
], lang('Auth.form.reset.action')) ?>
<?= form_close() ?>
<?= $this->endSection() ?>
