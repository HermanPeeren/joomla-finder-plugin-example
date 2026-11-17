<?php

/**
 * @package     Acme.Administrator
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Administrator\View\Recipe;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null): void
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

        if (\count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $this->getDocument()->getWebAssetManager()->useScript('keepalive')->useScript('form.validate');

        $isNew   = empty($this->item->id);
        $toolbar = $this->getDocument()->getToolbar();

        ToolbarHelper::title($isNew ? Text::_('COM_RECIPES_MANAGER_RECIPE_NEW') : Text::_('COM_RECIPES_MANAGER_RECIPE_EDIT'), 'utensils');

        $toolbar->apply('recipe.apply');
        $toolbar->save('recipe.save');
        $toolbar->save2new('recipe.save2new');
        $toolbar->cancel('recipe.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
