<?php

HTML::title(array('title'=>$L->g('About'), 'icon'=>'support'));

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th class="uk-width-1-5"></th>
	<th class="uk-width-3-5"></th>
	</tr>
</thead>
<tbody>
';
	echo '<tr>';
	echo '<td>Bludit Edition</td>';
	if (defined('BLUDIT_PRO')) {
		echo '<td>PRO - '.$L->g('Thanks for support Bludit').'</td>';
	} else {
		echo '<td>Standard - <a target="_blank" href="https://pro.bludit.com">'.$L->g('Upgrade to Bludit PRO').'</a></td>';
	}
	echo '</tr>';

	echo '<tr>';
	echo '<td>Bludit Version</td>';
	echo '<td>'.BLUDIT_VERSION.'</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>Bludit Codename</td>';
	echo '<td>'.BLUDIT_CODENAME.'</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>Bludit Build Number</td>';
	echo '<td>'.BLUDIT_BUILD.'</td>';
	echo '</tr>';

echo '
</tbody>
</table>
';
