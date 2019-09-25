<?php if (empty($content)): ?>
	<div class="mt-4">
	<?php $language->p('No pages found') ?>
	</div>
<?php endif ?>

<?php if ($user): ?>
<div class="card my-2 p-2 bg-light-blue">
	<div>
		<div class="form-group mb-1">
			<textarea id="jspostText" class="form-control" rows="3" placeholder="What's up?"></textarea>
		</div>
		<div id="jspreviewImages" class="mb-1">

		</div>
		<div>
		<input type="file" id="jsimageInputFile">
		<script>
			var jsimageInputFile = document.getElementById('jsimageInputFile');
			jsimageInputFile.onchange = function(event) {
				uploadImage(jsimageInputFile.files[0]);
			}
		</script>
		</div>
		<div class="form-group text-right mb-0">
			<button id="jspostButton" type="button" class="btn btn-primary btn-sm">Post</button>
		</div>
	</div>
</div>
<script>

// Global variables
var _apiToken = "<?php echo $apiToken ?>";
var _userToken = "<?php echo $user->tokenAuth() ?>";
var _pageUUID = Date.now().toString(36) + Math.random().toString(36).substr(2, 15);
var _pageNumber = 1;
var _itemsPerPage = <?php echo $site->itemsPerPage() ?>;
var _users = {}

function getUsers() {
	console.log("Getting users.");
	fetch("http://localhost:8000/api/users?token="+_apiToken, {
		method: "GET"
	}).then(function(response) {
		return response.json();
	}).then(function(data) {
		console.log("Getting user. Response >");
		console.log(data);
		if (data.status=="0") {
			_users = data.data;
		}
	});
}

function getPosts() {
	console.log("Getting posts.");
	_pageNumber = _pageNumber + 1;
	fetch("http://localhost:8000/api/pages?token="+_apiToken+"&pageNumber="+_pageNumber+"&numberOfItems="+_itemsPerPage, {
		method: "GET"
	}).then(function(response) {
		return response.json();
	}).then(function(data) {
		console.log("Getting posts. Response >");
		console.log(data);
		if (data.status=="0") {
			var posts = data.data;
			if (posts.length > 0) {
				for (var i = 0; i < posts.length; i++) {
					insertPostInTimeline(posts[i], false);
				}
			}
		}
	}).then(function() {
		loadGallery();
	});
}

function insertPostInTimeline(args, beginning=true) {
	console.log("Insert post in timeline.");
	const postTemplate = `
	<div class="card my-2 p-2">
		<div class="card-body">
			<img class="float-left rounded-circle" style="width: 48px" src="${_users[args.username].profilePicture}" />
			<div style="padding-left: 56px">
				<p class="mb-2 text-muted">
					@${_users[args.username].nickname} - ${args.date}
				</p>
				${args.content}
			</div>
		</div>
	</div>
	`;
	var listOfPosts = document.getElementById("jslistOfPosts");
	if (beginning) {
		listOfPosts.innerHTML = postTemplate + listOfPosts.innerHTML;
	} else {
		listOfPosts.innerHTML = listOfPosts.innerHTML + postTemplate;
	}
}

function getPost(key) {
	console.log("Getting post.");
	fetch("http://localhost:8000/api/pages/"+key+"?token="+_apiToken, {
		method: "GET"
	}).then(function(response) {
		return response.json();
	}).then(function(data) {
		console.log("Getting post. Response >");
		console.log(data);
		if (data.status=="0") {
			insertPostInTimeline(data.data);
		}
	});
}

function createPost(content) {
	console.log("Creating new post.");
	fetch("http://localhost:8000/api/pages/", {
		method: "POST",
		body: JSON.stringify({
			token: _apiToken,
			authentication: _userToken,
			uuid: _pageUUID,
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

function uploadImage(file) {
	console.log("Uploading image.");
	var data = new FormData();
	data.append("image", file);
	data.append("uuid", _pageUUID);
	data.append("token", _apiToken);
	data.append("authentication", _userToken);

	fetch("http://localhost:8000/api/images", {
		method: "POST",
		body: data
	}).then(function(response) {
		return response.json();
	}).then(function(data) {
		console.log("Uploading image. Response >");
		console.log(data);
		var img = document.createElement("img");
		img.src = data.thumbnail;
		img.className = "img-thumbnail";
		img.dataset.original = data.image;
		var previewImages = document.getElementById("jspreviewImages");
		previewImages.appendChild(img);
	});
}

// Event for click on button jspostButton
document.getElementById("jspostButton").onclick = function(event) {
	// Get the post content from the textarea
	var postContent = document.getElementById("jspostText").value;

	// Insert all uploaded images to the post content
	var images = document.getElementById("jspreviewImages").querySelectorAll("img");
	if (images.length > 0) {
		postContent += "<div class=image-gallery>";
		for (var i = 0; i < images.length; i++) {
			postContent += "<a href="+images[i].dataset.original+"><img src="+images[i].src+"/></a>";
		}
		postContent += "</div>";
	}
	createPost(postContent);

	// Clean up
	postContent.value = "";
	document.getElementById("jspreviewImages").innerHTML = "";
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
				<div id="post-content">
				<?php echo $page->contentBreak() ?>
				</div>

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

<div id="jsloadMorePosts" onclick="getPosts()" class="card my-2 p-2">
	<div class="card-body text-center">
	<p class="m-0">Load more posts</p>
	</div>
</div>