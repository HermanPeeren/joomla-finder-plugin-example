<?php

/**
 * @package     Acme.Site
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Acme\Component\Recipes\Site\View\Recipes\HtmlView $this */
?>
<div class="com-recipes">
	<h1><?php echo Text::_('COM_RECIPES_RECIPES'); ?></h1>

	<?php if (empty($this->items)) : ?>
		<p><?php echo Text::_('COM_RECIPES_NO_RECIPES'); ?></p>
	<?php else : ?>
		<ul class="com-recipes__list list-unstyled">
			<?php foreach ($this->items as $item) : ?>
				<li class="com-recipes__item mb-4">
					<h2 class="com-recipes__title h4">
						<a href="<?php echo Route::_('index.php?option=com_recipes&view=recipe&id=' . (int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?>
						</a>
					</h2>
					<p class="com-recipes__meta text-muted small">
						<?php echo Text::plural('COM_RECIPES_N_MINUTES', (int) $item->prep_time); ?>
						&middot;
						<?php echo Text::plural('COM_RECIPES_N_SERVINGS', (int) $item->servings); ?>
						&middot;
						<?php echo Text::_('COM_RECIPES_DIFFICULTY_' . strtoupper($item->difficulty)); ?>
					</p>
					<?php echo $item->description; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
