Lightbox
==========

Lightbox and gallery based on the lightbox script by Lokesh Dhakar.

Lightbox
--------

Add a data-lightbox attribute to any image link to enable Lightbox. For the value of the attribute, use a unique name for each image. For example:

`<a href="/bl-content/uploads/image-1.jpg" data-lightbox="image-1" data-title="My caption"><img src="image-1.jpg"></a>`

Important: The link to the image must contain the path to the directory (/bl-content/uploads/).

Gallery
-------

Add a data-title attribute if you want to show a caption.
    
If you have a group of related images that you would like to combine into a set, use the same data-lightbox attribute value for all of the images. For example:

`<a href="/bl-content/uploads/image-2.jpg" data-lightbox="gallery"><img src="image-2.jpg"></a>`

`<a href="/bl-content/uploads/image-3.jpg" data-lightbox="gallery"><img src="image-3.jpg"></a>`

`<a href="/bl-content/uploads/image-4.jpg" data-lightbox="gallery"><img src="image-4.jpg"></a>`

Important: The link to the image must contain the path to the directory (/bl-content/uploads/).

Website
-------

Website of the script with more information:

http://lokeshdhakar.com/projects/lightbox2/

Versions
--------

1.0, February 21, 2018
- Compatibility with Bludit v2.
- Some minor code changes.
- German languge files and liesmich.txt.

0.2, August 23, 2016
- Fixed "Lightbox script end tag missing" at line 28.

0.1, July 30, 2016
- Release.
