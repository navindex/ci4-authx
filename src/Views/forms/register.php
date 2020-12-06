<?= $this->extend($auth->viewLayout) ?>

<?= $this->section('form') ?>
<?= form_open(route_to('register')) ?>
<?= $this->include('partials/auth/logo') ?>
<h1 class="item"><?= lang('Auth.form.register.title') ?></h1>
<?= $this->include('partials/auth/session_messages') ?>
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
		'name'     => 'username',
		'id'       => 'username',
		'type'     => 'text',
		'class'    => session('errors.username') ? 'is-invalid' : '',
		'value'    => old('username') ?? '',
		'required' => 'required',
	]) ?>
	<?= form_label(lang('Auth.form.username'), 'username') ?>
	<div class="badges">
		<?php if (!empty(session('errors.username'))) : ?>
			<div class="icon alert" tabindex="0">
				<p class="bubble"><?= session('errors.username') ?></p>
			</div>
		<?php endif ?>
	</div>
</div>
<div class="item">
	<?= form_input([
		'name'         => 'password',
		'id'           => 'password',
		'type'         => 'password',
		'class'        => session('errors.password') ? 'is-invalid' : '',
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
	<?= form_label(lang('Auth.form.repeatPassword'), 'pass-confirm') ?>
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
	], lang('Auth.form.register.action')) ?>
<ul class="item">
	<li><?= lang('Auth.form.alreadyRegistered') ?></li>
	<li><?= anchor(route_to('login'), lang('Auth.form.signIn')) ?></li>
</ul>
<?= form_close() ?>
<?= $this->endSection() ?>
