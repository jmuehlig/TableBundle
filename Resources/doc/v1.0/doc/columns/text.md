# text Column Type

The `text` column type shows a simple text. 
Options:
* `nl2br` (type: `boolean`, default: `false`): If `true`, the php function `nl2br` will be applied.
* `maxlength` (type: `int`, default: `null`): Max length of the text. The remaining text will not be rendered.
* `after_maxlength` (type: `string`, default: `...`): Text to show, after maxlength is reached.
* `empty_value` (type: `string`, default: null): Value to show, if the field is null.