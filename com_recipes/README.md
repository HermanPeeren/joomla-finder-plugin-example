# com_recipes (demo)

A deliberately small Joomla 5/6 component whose only job is to be *indexable*, so
that `plg_finder_recipes` has something real to adapt.

## Why it looks like this

The finder adapter needs three things from a component, and this demo provides
exactly those:

1. **A table with the columns the adapter selects**: `#__recipes`, created by
   `admin/sql/install.mysql.utf8.sql`. The adapter's `getListQuery()` aliases
   `description → summary`, `instructions → body`, `published → state` and
   `created → start_date`; the extra `image`, `prep_time`, `servings` and
   `difficulty` columns are what the `default_recipe.php` result layout renders.

2. **Standard content events with the context `com_recipes.recipe`.** Nothing in
   this component fires them explicitly; they come free from core:
   - `AdminModel::save()` → `onContentBeforeSave` / `onContentAfterSave`
   - `AdminController::publish()` → `onContentChangeState`
   - `AdminModel::delete()` → `onContentAfterDelete`

   The context is `$this->option . '.' . $this->getName()`, hence
   `com_recipes.recipe`. `plg_content_finder` listens to those and re-dispatches
   them as `onFinder*` events, which is the only way the adapter ever hears
   about an edit.

3. **A `typeAlias`**: `RecipeTable::__construct()` sets
   `com_recipes.recipe`, which is what `Helper::addCustomFields()` and the tags
   system key off.

## Install order

1. Install `com_recipes` (this folder).
2. Install `plg_finder_recipes` and enable it.
3. Enable **Content – Smart Search** (`plg_content_finder`) if it is not already.
4. Smart Search → Index → *Index*.

Four sample recipes are inserted on install (three published, one unpublished so
you can watch the index react when you publish it).

## What is deliberately missing

No categories (which is why the adapter overrides `getStateQuery()`), no SEF
router, no tags, no batch processing, no associations. Add them and the adapter
needs the corresponding `onFinderCategoryChangeState` handler and a category
join in `getListQuery()`.
