<?php

/**
 * @package     Acme.Administrator
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Administrator\Table;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\User\CurrentUserInterface;
use Joomla\CMS\User\CurrentUserTrait;
use Joomla\Database\DatabaseInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\String\StringHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Recipe table.
 */
class RecipeTable extends Table implements CurrentUserInterface
{
    use CurrentUserTrait;

    protected $_supportNullValue = true;

    /**
     * Ensure these are stored as JSON strings.
     */
    protected $_jsonEncode = ['params', 'metadata'];

    public function __construct(DatabaseInterface $db, ?DispatcherInterface $dispatcher = null)
    {
        // Lets com_finder (and com_tags, com_fields) address our rows.
        $this->typeAlias = 'com_recipes.recipe';

        parent::__construct('#__recipes', 'id', $db, $dispatcher);

        $this->setColumnAlias('published', 'published');
    }

    /**
     * Data integrity checks, plus alias generation.
     */
    public function check()
    {
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        $this->title = htmlspecialchars_decode(trim($this->title), ENT_QUOTES);

        if ($this->title === '') {
            $this->setError(Text::_('COM_RECIPES_ERR_TABLE_TITLE'));

            return false;
        }

        $this->alias = trim($this->alias);

        if ($this->alias === '') {
            $this->alias = $this->title;
        }

        $this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

        if (trim(str_replace('-', '', $this->alias)) === '') {
            $this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
        }

        if (!\in_array($this->difficulty, ['easy', 'medium', 'hard'], true)) {
            $this->difficulty = 'easy';
        }

        $this->prep_time = max(0, (int) $this->prep_time);
        $this->servings  = max(0, (int) $this->servings);

        if ($this->metadesc !== null) {
            $this->metadesc = StringHelper::substr(trim($this->metadesc), 0, 1024);
        }

        return true;
    }

    /**
     * Maintain created/modified bookkeeping.
     */
    public function store($updateNulls = true)
    {
        $date = Factory::getDate()->toSql();
        $user = $this->getCurrentUser();

        if ($this->id) {
            $this->modified    = $date;
            $this->modified_by = $user->id;
        } else {
            if (!(int) $this->created) {
                $this->created = $date;
            }

            if (empty($this->created_by)) {
                $this->created_by = $user->id;
            }
        }

        return parent::store($updateNulls);
    }
}
