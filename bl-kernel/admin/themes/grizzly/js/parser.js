class Parser {
	// Returns an array with tags
	// The tags are parser from hash-tags
	// text = Hello this is a #test of #the function
	// returns ['test', 'the']
	tags(text) {
		var rgx = /#(\w+)\b/gi;
		var tag;
		var tags = [];
		while (tag = rgx.exec(text)){
			tags.push(tag[1])
		}
		// tags is an array, implode with comma ,
		return tags.join(",");
	}

	// Returns all characters after the hash # and space
	// Onlt the first match
	// text = # Hello World
	// returns "Hello World"
	title(text) {
		var rgx = /# (.*)/;
		let title = rgx.exec(text);
		if (title) {
			return title[1].trim();
		}
		return "";
	}

	// Returns the text without the first line
	// The first line is removed only if the first line has a # Headline1
	removeFirstLine(text) {
		var lines = text.split("\n");
		if (lines) {
			// If the first line included # Headline1 then the line is removed
			if (lines[0].includes("# ")) {
				lines.splice(0,1);
			}
		}
		return lines.join("\n");
	}
}