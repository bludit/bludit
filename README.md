[Bludit](https://www.bludit.com/)
================================

Dear developers
-------

## Frameworks and libraries included in Bludit v4
Bludit will include the following frameworks, please use them with they native functions.

Frontend:
- Bootstrap v5.
- Boostrap icons.
- jQuery, you can use vanilla Javascript but for events in the views please use jQuery.

Backend:
- `bl-kernel/functions.php` provides the global function for Bludit; These functions provide connectivity between different objects and databases; These functions should provide different checks and logic before add/edit/delete into the databases.
- PHP SimpleImage for processing images: https://github.com/claviska/SimpleImage

## Comments for functions and methods
Please add the following structure commenting what it does the function, also add the stamp `=== Bludit v4` so I know what is new.
```
/*	Delete a page === Bludit v4

	@key			string			Array => (key: string)
	@return			string/bool		Returns the page key on successful delete, FALSE otherwise
*/
function deletePage($args) {
   ...
}
```

Documentation for Bludit v4
-------
There is a new branch for the Documentation in english for Bludit v4.

https://github.com/bludit/documentation-english/tree/v4.0