<?php if (empty($content)): ?>
	<div class="mt-4">
	<?php $language->p('No pages found') ?>
	</div>
<?php endif ?>

<?php if ($user): ?>
<div class="card my-2 p-2 bg-light-blue">
	<div>
		<div class="form-group">
			<textarea id="jspostText" class="form-control" rows="3" placeholder="What's up?"></textarea>
		</div>
		<div class="form-group text-right mb-0">
			<button id="jspostButton" type="button" class="btn btn-primary btn-sm">Post</button>
		</div>
	</div>
</div>
<script>

var apiToken = "<?php echo $apiToken ?>"
var userToken = "<?php echo $user->tokenAuth() ?>"

function insertPostInTimeline(args) {
	const postTemplate = `
	<div class="card my-2 p-2">
		<div class="card-body">
			<img class="float-left rounded-circle" style="width: 48px" src="${args.srcProfilePicture}" />
			<div style="padding-left: 56px">
				<p class="mb-2 text-muted">
					@${args.nickname} - ${args.date}
				</p>
				${args.content}
			</div>
		</div>
	</div>
	`;
	console.log(postTemplate);
	var listOfPosts = document.getElementById("jslistOfPosts");
	listOfPosts.innerHTML = postTemplate + listOfPosts.innerHTML;
}

function getPost(key) {
	console.log("Getting post.");
	fetch("http://localhost:8000/api/pages/"+key+"?token="+apiToken, {
		method: "GET"
	}).then(function(response) {
		return response.json();
	}).then(function(data) {
		console.log("Getting post. Response >");
		console.log(data);
		if (data.status=="0") {
			data.data.nickname = "<?php echo $user->nickname() ?>";
			data.data.srcProfilePicture = "<?php echo $user->profilePicture() ?>";
			insertPostInTimeline(data.data);
		}
	});
}

function createPost(content) {
	console.log("Creating new post.");
	fetch("http://localhost:8000/api/pages/", {
		method: "POST",
		body: JSON.stringify({
			token: apiToken,
			authentication: userToken,
			content: content
		})
	}).then(function(response) {
		return response.json();
	}).then(function(data) {
		console.log("Creating new post. Response >");
		console.log(data);
		if (data.status=="0") {
			document.getElementById("jspostText").value = "";
			var newPostKey = data.data["key"];
			getPost(newPostKey);
		}
	});
}

// Event for click on button jspostButton
document.getElementById("jspostButton").onclick = function(event) {
	var postContent = document.getElementById("jspostText").value;
	createPost(postContent);
}


</script>
<?php endif; ?>

<div id="jslistOfPosts">
	<?php foreach ($content as $page): ?>
	<!-- Post -->
	<div class="card my-2 p-2">

		<!-- Load Bludit Plugins: Page Begin -->
		<?php Theme::plugins('pageBegin') ?>

		<div class="card-body">
			<!-- Profile picture -->
			<img class="float-left rounded-circle" style="width: 48px" src="<?php echo $page->user('profilePicture') ?>" />

			<div style="padding-left: 56px">
				<!-- Post's author and date -->
				<p class="mb-2 text-muted">
					@<?php echo $page->user('nickname') ?> -
					<?php echo $page->date() ?>
				</p>

				<!-- Post's content -->
				<?php echo $page->contentBreak() ?>

				<!-- <div class="share text-right">
					<a target="_blank" class="twitter" href="https://twitter.com/share?text=<?php echo urlencode($page->title()) ?>&amp;url=<?php echo urlencode ($page->permalink()) ?>">
						<i class="fa fa-twitter"></i>
					</a>
					<a target="_blank" class="facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode ($page->permalink()) ?>">
						<i class="fa fa-facebook"></i>
					</a>
					<a target="_blank" class="reddit" href="https://www.reddit.com/submit?url=<?php echo urlencode ($page->permalink()) ?>&amp;title=<?php echo urlencode($page->title()) ?>">
						<i class="fa fa-reddit"></i>
					</a>
				</div> -->
			</div>
		</div>

		<!-- Load Bludit Plugins: Page End -->
		<?php Theme::plugins('pageEnd') ?>

	</div>
	<?php endforeach ?>
</div>
