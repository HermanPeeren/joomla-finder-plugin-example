<?php

/**
 * @package     Acme.Administrator
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller for a single recipe.
 *
 * FormController::save() dispatches onContentBeforeSave / onContentAfterSave
 * with the context com_recipes.recipe, which is what plg_content_finder picks
 * up and re-dispatches to the finder adapter.
 */
class RecipeController extends FormController
{
    protected $view_list = 'recipes';
}
