<h2 class="title"><i class="fa fa-rocket"></i> Plugins</h2>
<p>Not implemented...</p>

<?php
	foreach($plugins['all'] as $Plugin)
	{
		echo '<p>'.$Plugin->title().'</p>';
	}
?>