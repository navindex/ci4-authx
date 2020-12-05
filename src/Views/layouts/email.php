<?= doctype('html4-trans'); ?>
<html lang="en">

<head>
	<title><?= $company['name'] ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>

<body style="margin:0;padding:0;background-color:#ffffff;" dir="ltr">
	<table cellspacing="0" cellpadding="0" id="email-wrapper" style="border-collapse:collapse;border:none;margin:0 auto;">
		<thead>
			<tr style="display:table-row;margin:0;padding:0;">
				<th style="display:table-cell;margin:0;padding:0;background-color:#ffffff;">
					<img alt="<?= $company['name'] ?>" title="<?= $company['name'] ?>" width="145" height="41" style="display:block;border:none;margin:15px auto;padding:0;" src="<?php base_url('assets/images/logo.svg') ?>" />
				</th>
			</tr>
		</thead>
		<tbody>
			<tr style="display:table-row;margin:0;padding:0;">
				<td id="content" style="display:table-cell;margin:0;padding:0;background-color:#ffffff;font-family:ArialMT,Arial,sans-serif;background:#ffffff;font-size:16px;font-weight:400;font-style:none;text-transform:none;max-width:720px;padding:0;border:none;">
					<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:none;">
						<thead>
							<tr style="display:table-row;margin:0;padding:0;">
								<th style="display:table-cell;margin:0;padding:15px 0;background-color:#ffffff;color:#231f20;border:none;font-size:16px;font-weight:400;font-style:none;text-transform:none;">
									<?= $this->renderSection('greeting') ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr style="display:table-row;margin:0;padding:0;">
								<td style="display:table-cell;margin:0;padding:15px 0;background-color:#ffffff;color:#231f20;border:none;font-size:16px;font-weight:400;font-style:none;text-transform:none;">
									<?= $this->renderSection('body') ?>
								</td>
							</tr>
							<tr style="display:table-row;margin:0;padding:0;">
								<td style="display:table-cell;margin:0;padding:15px 0;background-color:#ffffff;color:#231f20;border:none;font-size:16px;font-weight:400;font-style:none;text-transform:none;">
									<?= $this->renderSection('signature') ?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</body>

</html>
