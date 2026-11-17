# Joomla finder plugin example

The example code from **Custom Plugins, part 4: Finder Plugin** in the Joomla
Community Magazine: a Smart Search adapter, a small component for it to index,
and the result layout override that displays the component's own fields.

Written against Joomla 6.1. Nothing here is meant for a production site; it is
meant to be read.

## What is in here

| Folder | What it is |
|---|---|
| `plg_finder_recipes/` | The finder plugin. The adapter, its service provider, its form and its language files. |
| `com_recipes/` | A deliberately small component holding the recipes the plugin indexes. |
| `template-override/` | The Smart Search result layout for a recipe. Not part of the plugin — a plugin manifest cannot install a template override. |

## Installing it

Zip each extension folder and install it through **System → Install → Extensions**.
`plg_finder_recipes/` and `com_recipes/` are each the root of their own package,
so zip the *contents* of the folder, not the folder itself.

Then:

1. Enable **Content – Smart Search** (`plg_content_finder`). Without it the
   content events never reach a finder plugin, and nothing is ever re-indexed
   when a recipe changes.
2. Enable **Smart Search – Recipes** (`plg_finder_recipes`).
3. Add a recipe or two in **Components → Recipes**. The component ships four.
4. Run **Components → Smart Search → Index**.

Searching for `pannenkoeken` should now find a recipe.

For the custom result layout, copy

    template-override/cassiopeia/html/com_finder/search/default_recipe.php

to

    templates/cassiopeia/html/com_finder/search/default_recipe.php

Without it Smart Search falls back to its own `default_result.php` — silently, so
the absence looks like nothing happened rather than like an error.

## The three things that make a component indexable

The component contains no Smart Search code at all. What it does have:

1. **A table whose columns the adapter can select.** `#__recipes`, with
   `description`, `instructions`, `published` and `created` aliased by the
   adapter to `summary`, `body`, `state` and `start_date`.
2. **The standard content events**, with the context `com_recipes.recipe`.
   These come free from core — `AdminModel::save()`, `AdminController::publish()`
   and `AdminModel::delete()` dispatch them — as long as the component is built
   the ordinary way.
3. **A `typeAlias`** on the table class, `com_recipes.recipe`, which is what tags
   and custom fields key off.

A component becomes searchable by being conventional. If yours is not indexed on
save, the first question is not what the finder plugin is missing but whether the
component fires the standard events.

## Licence

GNU General Public License version 2 or later.
