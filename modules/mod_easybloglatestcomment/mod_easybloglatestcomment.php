<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );
$path	= JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'constants.php';

if( !JFile::exists( $path ) )
{
	return;
}

// Include the syndicate functions only once
require_once( $path );
require_once( EBLOG_HELPERS . DS . 'helper.php' );
require_once( dirname(__FILE__).DS.'helper.php' );

JTable::addIncludePath( EBLOG_TABLES );	

$document	= JFactory::getDocument();
$document->addStyleSheet( rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/css/module.css' );

$config		= EasyBlogHelper::getConfig();

$jCommentFile	= JPATH_ROOT . DS . 'components' . DS . 'com_jcomments' . DS . 'jcomments.php';
if( $config->get( 'comment_jcomments') && JFile::exists( $jCommentFile ) )
{
	$db 		= JFactory::getDBO();
	$query 		= 'SELECT * FROM ' . $db->nameQuote( '#__jcomments' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. 'AND ' . $db->nameQuote( 'object_group' ) . '=' . $db->Quote( 'com_easyblog' ) . ' '
				. 'ORDER BY `date` '
				. 'LIMIT 0,' . $params->get( 'count' );
	$db->setQuery( $query );
	$rows 		= $db->loadObjectList();
	$comments 	= array();

	if( $rows )
	{
		// Assign necessary variables on the object
		foreach( $rows as $row )
		{
			$row->author 	= EasyBlogHelper::getTable( 'Profile' );
			$row->author->load( $row->userid);
			$row->created_by	= $row->userid;
			$row->post_id 		= $row->object_id;

			$blog 			= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $row->object_id );

			$row->blog_title 	= $blog->title;
			$row->created 		= $row->date;
			$comments[]		= $row;
		}
	}
}
elseif( $config->get( 'comment_jomcomment') )
{
	$db 		= JFactory::getDBO();
	$query 		= 'SELECT * FROM ' . $db->nameQuote( '#__jomcomment' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'option' ) . ' = ' . $db->Quote( 'com_easyblog' ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 ) . ' '
				. 'ORDER BY `date` '
				. 'LIMIT 0,' . $params->get( 'count' );
	$db->setQuery( $query );
	$rows 		= $db->loadObjectList();
	$comments 	= array();

	if( $rows )
	{
		// Assign necessary variables on the object
		foreach( $rows as $row )
		{
			$row->author 	= EasyBlogHelper::getTable( 'Profile' );
			$row->author->load( $row->user_id);
			$row->created_by	= $row->user_id;
			$row->post_id 		= $row->contentid;

			$blog 			= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $row->contentid );

			$row->blog_title 	= $blog->title;
			$row->created 		= $row->date;
			$comments[]		= $row;
		}
	}
}
else
{
	// Use default comments
	$comments	= modEasyBlogLatestCommentHelper::getLatestComment($params);
}

$showavatar		= $params->get('showavatar', true);
$showtitle		= $params->get('showtitle', true);
$showprivate	= $params->get('showprivate', true);
$maxCharacter   = $params->get('maxcommenttext', 100);
$maxTitleLen	= $params->get('maxtitletext', 30);

require(JModuleHelper::getLayoutPath('mod_easybloglatestcomment'));