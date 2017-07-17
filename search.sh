#!/bin/bash
# Execute this script in the root directory of the project
# Ex: /search.sh bl-kernel/dbpages.class.php

CLASS_FILENAME=$1

echo "Class name"
CLASS_NAME=`grep "class " $CLASS_FILENAME | awk '{print $2}' | xargs`

echo "Search Object"
OBJECT_NAME=`find -name "*.php" -exec grep "new $CLASS_NAME" {} \; | awk '{print $1}' | xargs`

echo "List of methods"
grep "public function" $CLASS_FILENAME | awk '{print $3}' | tr "(" " " | awk '{print $1}' > /tmp/methods.list

while read -r METHOD
do
	echo ""
	echo "-------------------------------------"
	echo "Searching for $CLASS_NAME->$METHOD("
	echo "-------------------------------------"

	grep -r -w "$CLASS_NAME->$METHOD(" *
	let STATUS=$?
	if [ $STATUS -eq 1 ]
	then
		echo "Searching for this->$METHOD( inside $CLASS_FILENAME"
		echo "-------------------------------------"

		grep -r -w "this->$METHOD(" $CLASS_FILENAME
		let STATUS=$?
		if [ $STATUS -eq 1 ]
		then
			echo "Not found"
		fi
	fi

	echo "-------------------------------------"

done < /tmp/methods.list