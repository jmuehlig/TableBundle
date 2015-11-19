Changelog
===========

v1.1
===
* The filter options can now set from twig template by passing a second parameter to **filter_widget**, **filter_row** and **filter_label** methods.
* Methods using the **OptionsResolverInterface** are deprecated now. The TableType, PaginationTypeInterface, OrderTypeInterface and FilterTypeInterface have new methods, working with the **OptionsResolver**.
* There is a new column named **TwigColumn**, which will render a twig template.
* The **UrlColumn** will now display the content of the given property for default.
* Options for all table components (table, order, pagination, filter) can be global configured at the **config.yml**. Each table type can overwrite these config, of course.
* The TableType is now specified by an interface (TableTypeInterface), not just an abstract class (AbstractTableType).

v1.0.2
===
* Updated the README

v1.0.1
===
* Fixed a bug at the **UrlColumn**