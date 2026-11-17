<?php

/**
 * @package     Acme.Site
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var \Acme\Component\Recipes\Site\View\Recipe\HtmlView $this */
?>
<div class="com-recipes-recipe">
	<h1 class="com-recipes-recipe__title"><?php echo $this->escape($this->item->title); ?></h1>

	<?php if (!empty($this->item->image)) : ?>
		<figure class="com-recipes-recipe__image">
			<?php echo HTMLHelper::_('image', $this->item->image, $this->escape($this->item->title), [], false, -1); ?>
		</figure>
	<?php endif; ?>

	<ul class="com-recipes-recipe__meta list-inline text-muted">
		<li class="list-inline-item"><?php echo Text::plural('COM_RECIPES_N_MINUTES', (int) $this->item->prep_time); ?></li>
		<li class="list-inline-item"><?php echo Text::plural('COM_RECIPES_N_SERVINGS', (int) $this->item->servings); ?></li>
		<li class="list-inline-item"><?php echo Text::_('COM_RECIPES_DIFFICULTY_' . strtoupper($this->item->difficulty)); ?></li>
	</ul>

	<div class="com-recipes-recipe__description">
		<?php echo $this->item->description; ?>
	</div>

	<h2 class="h4"><?php echo Text::_('COM_RECIPES_FIELD_INSTRUCTIONS_LABEL'); ?></h2>
	<div class="com-recipes-recipe__instructions">
		<?php echo $this->item->instructions; ?>
	</div>
</div>
