<?php

/**
 * @package     Acme.Site
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Acme\Component\Recipes\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class DisplayController extends BaseController
{
    protected $default_view = 'recipes';
}
