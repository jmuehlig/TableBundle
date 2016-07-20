Changelog
===========
v1.3
===
* New: The TableBundle is now symfony3 compatible.
* New: Support for [Propel ORM library](http://propelorm.org/) via `PropelQueryBuilderDataSource`. Thanks to [purrucker](https://github.com/purrucker)!
* New: Added options `html_asc` and `html_desc` to order component to create own html views for ordered columns.
* New: Added possibility to give the choice of lines per page to the user in form of a select input.
* New: The used version is visible at the toolbar.

v1.2.3
===
* Bugfix on filter component: Boolean Filter `false` option is not working.

v1.2.2
===
* Improvements for the WebProfiler: Finer measurement of render times (for table and for filters).
* Bugfix: Table did not set filter values.

v1.2.1
===
* Bugfix: Support for EntityDataSource::__contruct with entity and callback as parameters, without alias (alias added in 1.2).

v1.2
===
* New: Profiler Component! Called tables and their duration of building, fetching data and rendering can be viewed at the symfony toolbar and the profiler. Further will details about the table (options and columns), the components (filter, filter options, pagination options and order options) be displayed at the profiler view. 
* New: Create tables without table type from controller, by calling the new table type builder.
* New: Added parameter for table options, passed to TableFactory::createTable, when creating a table from table type.
* New: Added method Table::handleRequest for passing manipulated requests to tables.
* New: Added option **template** for declare the name of the template, the table is rendered by. This options gives you more control for creating own templates.
* Removed deprecation calls from options resolver, called in filter and table component.

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