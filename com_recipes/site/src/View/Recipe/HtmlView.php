<?php

/**
 * @package     Acme.Site
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Site\View\Recipe;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $params;

    public function display($tpl = null): void
    {
        $this->item   = $this->get('Item');
        $this->params = Factory::getApplication()->getParams();

        if (\count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->prepareDocument();

        parent::display($tpl);
    }

    protected function prepareDocument(): void
    {
        $document = $this->getDocument();

        $document->setTitle($this->item->title);

        if (!empty($this->item->metadesc)) {
            $document->setDescription($this->item->metadesc);
        }

        if (!empty($this->item->metakey)) {
            $document->setMetaData('keywords', $this->item->metakey);
        }
    }
}
