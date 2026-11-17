<?php

/**
 * @package     Acme.Site
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Site list model for recipes.
 */
class RecipesModel extends ListModel
{
    protected function populateState($ordering = 'a.title', $direction = 'asc')
    {
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db     = $this->getDatabase();
        $user   = Factory::getApplication()->getIdentity();
        $levels = $user->getAuthorisedViewLevels();

        $query = $db->getQuery(true)
            ->select(
                $db->quoteName(
                    ['a.id', 'a.title', 'a.alias', 'a.description', 'a.image',
                     'a.prep_time', 'a.servings', 'a.difficulty', 'a.created']
                )
            )
            ->from($db->quoteName('#__recipes', 'a'))
            ->where($db->quoteName('a.published') . ' = 1')
            ->whereIn($db->quoteName('a.access'), $levels)
            ->order($db->quoteName('a.title') . ' ASC');

        return $query;
    }
}
