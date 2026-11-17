<?php

/**
 * @package     Acme.Site
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Site model for a single recipe.
 */
class RecipeModel extends ItemModel
{
    protected function populateState()
    {
        $app = Factory::getApplication();

        $this->setState('recipe.id', $app->getInput()->getInt('id'));
        $this->setState('params', $app->getParams());
    }

    public function getItem($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('recipe.id'));

        if (isset($this->item) && $this->item !== null && $this->item->id === $pk) {
            return $this->item;
        }

        $db     = $this->getDatabase();
        $levels = Factory::getApplication()->getIdentity()->getAuthorisedViewLevels();

        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__recipes', 'a'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->where($db->quoteName('a.published') . ' = 1')
            ->whereIn($db->quoteName('a.access'), $levels)
            ->bind(':id', $pk, ParameterType::INTEGER);

        $db->setQuery($query);
        $item = $db->loadObject();

        if (!$item) {
            throw new \Exception(Text::_('COM_RECIPES_ERROR_RECIPE_NOT_FOUND'), 404);
        }

        $this->item = $item;

        return $this->item;
    }
}
