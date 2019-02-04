<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Helper for Advanced Portfolio Pro Module.
 *
 * @package		Joomla.Site
 * @subpakage	ExtStore.AdvPortfolioPro
 */
class ModAdvPortfolioProHelper {

	public static function getList($params) {
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_advportfoliopro/models');

		// Get an instance of the project model
		$model = JModelLegacy::getInstance('Projects', 'AdvPortfolioproModel', array('ignore_request' => true));

		// Set application parameters in model
		$app		= JFactory::getApplication();
		$appParams	= $app->getParams();
		$model->setState('params', $appParams);

		$model->setState('list.select', 'a.*');

		$model->setState('filter.state', 1);
		$model->setState('filter.access', 1);

		$catids	= $params->get('catids', array());

		$model->setState('filter.category_id', $catids);

		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('limit', 5));
		$model->setState('list.ordering', self::_buildContentOrderBy($params));
		$model->setState('list.direction', '');

		// Filter by language
		$model->setState('filter.language',$app->getLanguageFilter());

		$items = $model->getItems();

		return $items;
	}

	/**
	 * Build the orderby for the query
	 *
	 * @return    string    $orderby portion of query
	 */
	protected static function _buildContentOrderBy($params) {
		$orderby = $params->get('orderby', 'rdate');

		switch ($orderby) {
			case 'date':
				$orderby = 'a.created';
				break;

			case 'rdate':
				$orderby = 'a.created DESC ';
				break;

			case 'alpha':
				$orderby = 'a.title';
				break;

			case 'ralpha':
				$orderby = 'a.title DESC';
				break;

			case 'random':
				$orderby = 'rand()';
				break;
			case 'order':
			default :
				$orderby = 'c.lft, a.ordering';
				break;
		}

		return $orderby;
	}
}