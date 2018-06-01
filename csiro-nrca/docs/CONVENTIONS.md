# Coding Conventions

The code style used in this theme is intended to be compatible with the CollectiveAccess code style, with some 
improvements to code structure, readability and reuse.  This could be improved further but would require further 
changes to CA outside of the theme layer.

[Back to documentation index](../README.md)

## View structure

### Header PHP block

Each view (except the most trivial) should have a PHP block that sets up variables used within the view.  Where 
possible, *all* variables should be assigned in this block.  This block should also contain calls to configure the
`AssetManager` and `TooltipManager`.

```php
<?php
AssetManager::load(...);

$vs_some_var = 'Do not indent here';

if (some_test()) {
    // Normal indentation and {curly braces} here.
}

TooltipManager::add(...);
?>
```

### View container

Following this header block should be a single container element with all the HTML content of the view, except for
JavaScript blocks.  This container element should have either a unique `id` (for page-level views) or a `class` (for 
reuseable component / widget views) attribute, and this attribute should be used as the scope of the relevant LESS file.

For example, with a page view, which should look like this:

```html
<div id="page-example">
    <!-- all content inside this element -->
</div>
```

The corresponding LESS file would be structured like this:

```less
#page-example {
    /* all rules scoped by the parent selector */
}
```

For components, such as widgets, the `id` attribute can be swapped out for `class="widget-example"` and the top-level
scope selector in the LESS file becomes `.widget-example`.

### JavaScript

Where required, JavaScript for a view should be contained in a single `<script>` element after the container element.  
The code should be wrapped in an immediately-invoked anonymous function to protect the global scope, however in some 
cases there is bleed into the global scope that has not been corrected.  This is the correct form:

```html
<script>
    (function ($) {
        'use strict';
        
        $(function () {
            // Initialise views here, e.g.
            caUI.initialisePanel({...});
        });
    }(jQuery));
</script>
```

## General code style

### Printing text (with translations)

When printing text, use a single `print` statement in its own `php` block per block of text:

```html
<div class="well">
    <?php print _t('Message here'); ?>
</div>
```

The HTML itself provides the whitespace.  The PHP should never directly print any HTML elements.

### PHP control structures in mixed HTML mode

When mixing PHP and HTML (i.e. outside of the header block) and control structures are required, use the
following form:

```html
<?php if ($vb_some_condition): ?>
    <div class="alert alert-info">
        This is only displayed if <code>$vb_some_condition</code> is true.
    </div>
<?php endif; ?>
```

This also applies to `for` and `foreach`.  This also applies when conditionally writing JavaScript.

### Glyphicons

In general, instead of the `__CA_NAV_ICON_*__` icons, the standard Bootstrap glyphicons are used:

```html
<span class="glyphicon glyphicon-ok"></span>
```
