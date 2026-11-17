<?php

/**
 * @package     Acme.Site
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Site\View\Recipes;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $params;

    public function display($tpl = null): void
    {
        $this->items  = $this->get('Items');
        $this->params = $this->getModel()->getState('params');

        if (\count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        parent::display($tpl);
    }
}
