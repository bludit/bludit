<div id="dashboard" class="container">
	<div class="row">
		<div class="col-md-7">

			<!-- Good message -->
			<div>
			<h2 id="hello-message" class="pt-0">
				<span class="fa fa-hand-spock-o"></span><span><?php echo $L->g('hello') ?></span>
			</h2>
			<script>
			$( document ).ready(function() {
				$("#hello-message").fadeOut(1000, function() {
					var date = new Date()
					var hours = date.getHours()
					if (hours > 6 && hours < 12) {
						$(this).html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-morning') ?>');
					} else if (hours > 12 && hours < 18) {
						$(this).html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-afternoon') ?>');
					} else if (hours > 18 && hours < 22) {
						$(this).html('<span class="fa fa-moon-o"></span><?php echo $L->g('good-evening') ?>');
					} else {
						$(this).html('<span class="fa fa-moon-o"></span><span><?php echo $L->g('good-night') ?></span>');
					}
				}).fadeIn(1000);
			});
			</script>
			</div>

			<!-- Quick Links -->
			<div class="container border-bottom pb-5" id="jsclippyContainer">

				<div class="row">
					<div class="col">
						<div class="form-group">
						<select id="jsclippy" class="clippy" name="state"></select>
						</div>
					</div>
				</div>

			<script>
			$(document).ready(function() {

				var clippy = $("#jsclippy").select2({
					placeholder: "<?php $L->p('Start typing to see a list of suggestions') ?>",
					allowClear: true,
					width: "100%",
					theme: "bootstrap4",
					minimumInputLength: 2,
					dropdownParent: "#jsclippyContainer",
					language: {
						inputTooShort: function () { return ''; }
					},
					ajax: {
						url: HTML_PATH_ADMIN_ROOT+"ajax/clippy",
						data: function (params) {
							var query = { query: params.term }
							return query;
						},
						processResults: function (data) {
							return data;
						}
					},
					templateResult: function(data) {
						var html = '';
						if (data.type=='menu') {
							html += '<a href="'+data.url+'"><div class="search-suggestion">';
							html += '<span class="fa fa-'+data.icon+'"></span>'+data.text+'</div></a>';
						} else {
							if (typeof data.id === 'undefined') {
								return '';
							}
							html += '<div class="search-suggestion">';
							html += '<div class="search-suggestion-item">'+data.text+' <span class="badge badge-pill badge-light">'+data.type+'</span></div>';
							html += '<div class="search-suggestion-options">';
							html += '<a target="_blank" href="'+DOMAIN_PAGES+data.id+'"><?php $L->p('view') ?></a>';
							html += '<a class="ml-2" href="'+DOMAIN_ADMIN+'edit-content/'+data.id+'"><?php $L->p('edit') ?></a>';
							html += '<a href="#" onclick="setKey()" class="ml-2 text-danger deletePageButton d-block d-sm-inline" data-toggle="modal" data-target="#jsdeletePageModal" data-key="'+data.id+'"><i class="fa fa-trash"></i><?php $L->p('Delete') ?></a>';
							html += '</div></div>';
						}

						return html;
					},
					escapeMarkup: function(markup) {
						return markup;
					}
				}).on("select2:closing", function(e) {
					e.preventDefault();
				}).on("select2:closed", function(e) {
					clippy.select2("open");
				});
				clippy.select2("open");

			});
			</script>
			</div>
			<div class="container mt-4">
				<div class="row">
					<div class="col">
						<a class="quick-links text-center" target="_blank" href="https://docs.bludit.com">
							<div class="fa fa-compass quick-links-icons"></div>
							<div><?php $L->p('Documentation') ?></div>
						</a>
					</div>
					<div class="col border-left border-right">
						<a class="quick-links text-center" target="_blank" href="https://forum.bludit.org">
							<div class="fa fa-support quick-links-icons"></div>
							<div><?php $L->p('Forum support') ?></div>
						</a>
					</div>
					<div class="col">
						<a class="quick-links text-center" target="_blank" href="https://gitter.im/bludit/support">
							<div class="fa fa-comments quick-links-icons"></div>
							<div><?php $L->p('Chat support') ?></div>
						</a>
					</div>
				</div>
			</div>

			<?php Theme::plugins('dashboard') ?>
		</div>
		<div class="col-md-5">

			<!-- Notifications -->
			<ul class="list-group list-group-striped b-0">
			<li class="list-group-item pt-0"><h4><?php $L->p('Notifications') ?></h4></li>
			<?php
			$logs = array_slice($syslog->db, 0, NOTIFICATIONS_AMOUNT);
			foreach ($logs as $log) {
				$phrase = $L->g($log['dictionaryKey']);
				echo '<li class="list-group-item">';
				echo $phrase;
				if (!empty($log['notes'])) {
					echo ' « <b>'.$log['notes'].'</b> »';
				}
				echo '<br><span class="notification-date"><small>';
				echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
				echo ' [ '.$log['username'] .' ]';
				echo '</small></span>';
				echo '</li>';
			}
			?>
			</ul>

		</div>
	</div>
</div>


<!-- Modal for delete page -->
<?php
	echo Bootstrap::modal(array(
		'buttonPrimary'=>$L->g('Delete'),
		'buttonPrimaryClass'=>'btn-danger deletePageModalAcceptButton',
		'buttonSecondary'=>$L->g('Cancel'),
		'buttonSecondaryClass'=>'btn-link',
		'modalTitle'=>$L->g('Delete content'),
		'modalText'=>$L->g('Are you sure you want to delete this page'),
		'modalId'=>'jsdeletePageModal'
	));
?>


<script>
var key = false;

function setKey() {
	 key = $(".deletePageButton").data('key');
}

$(document).ready(function() {

	$(".select2-dropdown").css("z-index","1040");

	// Event from button accept from the modal
	$(".deletePageModalAcceptButton").on("click", function() {

		$( "body" ).append("<iframe id='formSendingIframe' name='formSending' style='display:none;'></iframe>");

		var form = jQuery('<form>', {
			'action': HTML_PATH_ADMIN_ROOT+'edit-content/'+key,
			'method': 'post',
			'target': 'formSending',
			'id': 'requestForm'
		}).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'tokenCSRF',
			'value': tokenCSRF
		}).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'key',
			'value': key
		}).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'type',
			'value': 'delete'
		}))));

		form.hide().appendTo("body").submit();

		$("#jsdeletePageModal").modal('hide');

		// Wait for request to finish before updating search results
		$('#formSendingIframe').on( 'load', function() {
		   $('.select2-search__field').trigger("input");
		   $('#requestForm').remove();
		   $('#formSendingIframe').remove();
		});
	});
});
</script>
