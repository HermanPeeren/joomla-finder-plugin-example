<?php

/**
 * @package     Acme.Administrator
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * List model for recipes.
 */
class RecipesModel extends ListModel
{
    public function __construct($config = [], $factory = null)
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'published', 'a.published',
                'access', 'a.access',
                'language', 'a.language',
                'difficulty', 'a.difficulty',
                'prep_time', 'a.prep_time',
                'created', 'a.created',
                'ordering', 'a.ordering',
            ];
        }

        parent::__construct($config, $factory);
    }

    protected function populateState($ordering = 'a.title', $direction = 'asc')
    {
        $this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', ''));
        $this->setState('filter.published', $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', ''));
        $this->setState('filter.difficulty', $this->getUserStateFromRequest($this->context . '.filter.difficulty', 'filter_difficulty', ''));

        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.difficulty');

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            $db->quoteName(
                ['a.id', 'a.title', 'a.alias', 'a.published', 'a.access', 'a.language',
                 'a.prep_time', 'a.servings', 'a.difficulty', 'a.created', 'a.ordering',
                 'a.checked_out', 'a.checked_out_time']
            )
        )
            ->select($db->quoteName('l.title', 'language_title'))
            ->select($db->quoteName('ag.title', 'access_level'))
            ->select($db->quoteName('uc.name', 'editor'))
            ->from($db->quoteName('#__recipes', 'a'))
            ->join('LEFT', $db->quoteName('#__languages', 'l'), $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.language'))
            ->join('LEFT', $db->quoteName('#__viewlevels', 'ag'), $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access'))
            ->join('LEFT', $db->quoteName('#__users', 'uc'), $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.checked_out'));

        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('a.published') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } elseif ($published === '') {
            $query->whereIn($db->quoteName('a.published'), [0, 1]);
        }

        $difficulty = (string) $this->getState('filter.difficulty');

        if ($difficulty !== '') {
            $query->where($db->quoteName('a.difficulty') . ' = :difficulty')
                ->bind(':difficulty', $difficulty);
        }

        $search = (string) $this->getState('filter.search');

        if ($search !== '') {
            $search = '%' . str_replace(' ', '%', trim($search)) . '%';
            $query->where('(' . $db->quoteName('a.title') . ' LIKE :search1 OR ' . $db->quoteName('a.alias') . ' LIKE :search2)')
                ->bind([':search1', ':search2'], $search);
        }

        $orderCol  = $this->state->get('list.ordering', 'a.title');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
