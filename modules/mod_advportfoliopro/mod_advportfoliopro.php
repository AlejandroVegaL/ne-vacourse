<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

// include the syndicate functions only once
require_once(dirname(__FILE__) . '/helper.php');

$id					= $module->id;
$items				= modAdvPortfolioProHelper::getList($params);
$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));

require(JModuleHelper::getLayoutPath('mod_advportfoliopro', $params->get('layout', 'default')));