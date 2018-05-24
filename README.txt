README.txt
==========

Paragraph is a module to create paragraphs in your content.
You can create bundles(with own display and fields) as paragraph types.

When you add a paragraph field to your node/entity, you can select the allowed bundles, and when using the field, you can select a paragraph type from the allowed bundles to use different fields/display per paragraph.

This module module might be considered a clone of field_collection (on which it is based), but this module has some advantages:

* Different fields per paragraph bundle
* Using different paragraph bundles in a single paragraph field
* Displays per paragraph bundle

Bundles are exportable with features.

Using paragraphs with entity translation
-----------------------------------------------

 * Paragraph items must be selected as a translatable entity type at
    Admin -> Config -> Regional -> Entity Translation.
