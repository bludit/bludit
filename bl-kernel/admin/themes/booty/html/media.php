<?php
// Preload the first 10 files to not call via AJAX the first time the user open the media
$listOfFiles = Filesystem::listFiles(PATH_UPLOADS, '*', '*', $sortByDate=true, $chunk=false);
$listOfFilesByPage = array_chunk($listOfFiles, 10);
$preLoadFiles = array();
foreach ($listOfFilesByPage[0] as $file) {
	array_push($preLoadFiles, basename($file));
}
// Amount of pages of files, for the paginator
$amountOfPages = count($listOfFilesByPage);
?>

<div id="bluditMediaModal" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="container-fluid">
<div class="row">
	<div class="col p-3">
		<h3>Bludit Media Manager</h3>
		<table id="bluditMediaTable" class="table">
			<tbody>
				<tr>
					<td style="width:120px">
						<img class="img-thumbnail" alt="200x200" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1627e1b2b7e%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1627e1b2b7e%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.65%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true" style="width: 100px; height: 100px;">
					</td>
					<td>
						<div>example.jpg</div>
						<div>
							<a href="#" class="mr-2">Insert</a>
							<a href="#" class="mr-2">Delete</a>
						</div>
					</td>
				</tr>

			</tbody>
		</table>

		<nav>
			<ul class="pagination justify-content-center">
			<?php for ($i=1; $i<=$amountOfPages; $i++): ?>
			<li class="page-item"><a class="page-link" href="#"><?php echo $i ?></a></li>
			<?php endfor; ?>
			</ul>
		</nav>

	</div>
</div>
</div>
</div>
</div>
</div>

<script>

<?php
echo 'var preLoadFiles = '.json_encode($preLoadFiles).';';
?>

function showFiles(files) {
	$.each(files, function(key, filename) {
		tableRow = '<tr>'+
				'<td><img class="img-thumbnail" alt="200x200" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1627e1b2b7e%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1627e1b2b7e%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.65%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true" style="width: 100px; height: 100px;"></td>'+
				'<td>'+
					'<div>'+filename+'</div>'+
					'<div>'+
						'<a class="mr-2" href="#">Insert</a>'+
						'<a class="mr-2" href="#">Delete</a>'+
					'</div>'+
				'</td>'+
			'</tr>';
		$('#bluditMediaTable').append(tableRow);
	});
}

$.getJSON("<?php echo HTML_PATH_ADMIN_ROOT ?>ajax/list-files",
	{ pageNumber: 1 },
	function(data) {
		showFiles(data.files);
});

showFiles(preLoadFiles);

</script>