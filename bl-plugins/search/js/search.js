var options = {
	url: "http://localhost:8000/search",
	getValue: "title",
	template: {
		type: "custom",
		method: function(value, item) {
			return '<a href="'+item.permalink+'">'+value+'</a>'+item.content;
		}
	}
};
$( document ).ready(function() {

	$("#plugin-search-input").easyAutocomplete(options);

});