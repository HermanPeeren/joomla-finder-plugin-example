<?php

/**
 * @package     Acme.Administrator
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Admin model for a single recipe.
 *
 * Everything the finder plugin relies on comes from AdminModel: save() fires
 * the before/after save events, publish() fires onContentChangeState and
 * delete() fires onContentAfterDelete - all with the context
 * $this->option . '.' . $this->getName() == 'com_recipes.recipe'.
 */
class RecipeModel extends AdminModel
{
    protected $text_prefix = 'COM_RECIPES';

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_recipes.recipe', 'recipe', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_recipes.edit.recipe.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_recipes.recipe', $data);

        return $data;
    }
}
