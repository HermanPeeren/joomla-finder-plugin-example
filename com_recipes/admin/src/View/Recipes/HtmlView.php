<?php

/**
 * @package     Acme.Administrator
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Administrator\View\Recipes;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    public $filterForm;
    public $activeFilters;

    public function display($tpl = null): void
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if (\count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->filterForm
            ->addControlField('task')
            ->addControlField('boxchecked', '0');

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $user    = $this->getCurrentUser();
        $toolbar = $this->getDocument()->getToolbar();

        ToolbarHelper::title(Text::_('COM_RECIPES_MANAGER_RECIPES'), 'utensils');

        if ($user->authorise('core.create', 'com_recipes')) {
            $toolbar->addNew('recipe.add');
        }

        if ($user->authorise('core.edit.state', 'com_recipes')) {
            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            $childBar->publish('recipes.publish')->listCheck(true);
            $childBar->unpublish('recipes.unpublish')->listCheck(true);
        }

        if ($user->authorise('core.delete', 'com_recipes')) {
            $toolbar->delete('recipes.delete', 'JTOOLBAR_DELETE')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if ($user->authorise('core.admin', 'com_recipes')) {
            $toolbar->preferences('com_recipes');
        }
    }
}
