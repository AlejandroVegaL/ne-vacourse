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

defined('_JEXEC') or die('Restricted access');

//require_once(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'models'.DS.'tags.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'helper.php');

class modEasyBlogLatestCommentHelper
{
	function getLatestComment(&$params)
	{
		$mainframe		= JFactory::getApplication();
		$db 			= JFactory::getDBO();
		$config			= EasyBlogHelper::getConfig();
		
		$count			= (INT)trim($params->get('count', 5));
		$showavatar		= $params->get('showavatar', true);
		$showtitle		= $params->get('showtitle', true);
		$showprivate	= $params->get('showprivate', true);
		
		$query	= 'select b.`title` as `blog_title`, b.`created_by` as `author_id`, b.`category_id` as `category_id`, a.*';
		$query	.= ' from `#__easyblog_comment` as a';
		$query	.= '   left join `#__easyblog_post` as b';
		$query	.= '   on a.`post_id` = b.`id`';
		$query	.= ' where b.`published` = ' . $db->Quote('1');
		$query	.= ' and a.`published`=' . $db->Quote( '1' );
		$query	.= ' and b.`issitewide` = ' . $db->Quote('1');
		if(!$showprivate)
			$query	.= ' and b.`private` = ' . $db->Quote('0');
		$query .= ' order by a.`created` desc';
		$query .= ' limit ' . $count;
		
		$db->setQuery($query);		
		$result = $db->loadObjectList();

		if( count($result) > 0 )
		{
			for( $i = 0; $i < count( $result ); $i++ )
			{
				$row		=& $result[ $i ];
				$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
				$profile->load( $row->created_by );
				$row->author	= $profile;

				$date					= EasyBlogDateHelper::dateWithOffSet($row->created);
				//$row->dateString		= EasyBlogDateHelper::toFormat( $date , $config->get('layout_dateformat', '%A, %d %B %Y') );
				$row->dateString		= EasyBlogDateHelper::toFormat( $date , $params->get( 'dateformat', '%A, %d %B %Y') );
			}
		}
		return $result;
	}

	function _getMenuItemId( $post, &$params)
	{
		$itemId                 = '';
		$routeTypeCategory		= false;
		$routeTypeBlogger		= false;
		$routeTypeTag			= false;

		$routingType            = $params->get( 'routingtype', 'default' );

		if( $routingType != 'default' )
		{
			switch ($routingType)
			{
				case 'menuitem':
					$itemId					= $params->get( 'menuitemid' ) ? '&Itemid=' . $params->get( 'menuitemid' ) : '';
					break;
				case 'category':
					$routeTypeCategory  = true;
					break;
				case 'blogger':
					$routeTypeBlogger  = true;
					break;
				case 'tag':
					$routeTypeTag  = true;
					break;
				default:
					break;
			}
		}

		if( $routeTypeCategory )
		{
			$xid    = EasyBlogRouter::getItemIdByCategories( $post->category_id );
		}
		else if($routeTypeBlogger)
		{
			$xid    = EasyBlogRouter::getItemIdByBlogger( $post->created_by );
		}
		else if($routeTypeTag)
		{
			$tags	= self::_getPostTagIds( $post->id );
			if( $tags !== false )
			{
				foreach( $tags as $tag )
				{
					$xid    = EasyBlogRouter::getItemIdByTag( $tag );
					if( $xid !== false )
						break;
				}
			}
		}

		if( !empty( $xid ) )
		{
			// lets do it, do it, do it, lets override the item id!
			$itemId = '&Itemid=' . $xid;
		}

		return $itemId;
	}

	function _getPostTagIds( $postId )
	{
		static $tags	= null;

		if( ! isset($tags[$postId]) )
		{
			$db = JFactory::getDBO();

			$query  = 'select `tag_id` from `#__easyblog_post_tag` where `post_id` = ' . $db->Quote($postId);
			$db->setQuery($query);

			$result = $db->loadResultArray();

			if( count($result) <= 0 )
				$tags[$postId] = false;
			else
				$tags[$postId] = $result;

		}

		return $tags[$postId];
	}
}
