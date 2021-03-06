<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * User plugin that performs necessary clean up on users.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class plgUserEasySocial extends JPlugin
{
	/**
	 * Triggered when user logs into the site
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function onUserLogin( $user, $options = array() )
	{
		// Include main file.
		jimport( 'joomla.filesystem.file' );

		$path	= JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		if( !JFile::exists( $path ) )
		{
			return;
		}

		// Include the foundry engine
		require_once( $path );

		// Load the language string.
		Foundry::language()->load( 'plg_user_easysocial' , JPATH_ADMINISTRATOR );

		// Check if Foundry exists
		if( !Foundry::exists() )
		{
			Foundry::language()->load( 'com_easysocial' , JPATH_ROOT );
			echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
			return;
		}

		if( isset( $user[ 'status' ] ) && $user['status'] && $user['type'] == 'Joomla' )
		{
			//successful logged in.
			$my = JUser::getInstance();

			if ($id = intval(JUserHelper::getUserId( $user['username'] )))  {
				$my->load($id);
			}

			$config = Foundry::config();
			$addStream = $config->get( 'users.stream.login' );

			// do not add stream when user login from backend.
			$mainframe 	= JFactory::getApplication();
			if( $mainframe->isAdmin() )
			{
				$addStream = false;
			}

			// getting joomla session lifetime config.
			$jConfig 		= JFactory::getConfig();
			$sessionLimit 	= $jConfig->get( 'lifetime', '0' );

			if( $addStream )
			{
				$db 	= Foundry::db();
				$sql 	= $db->sql();

				$curDateTime = Foundry::date()->toMySQL();

				$query = 'select `time`, UNIX_TIMESTAMP( date_add( ' . $db->Quote( $curDateTime ) . ' , INTERVAL -' . $sessionLimit . ' MINUTE ) ) as `limit`';
				$query .= ', count(1) as `count`';
				$query .= ' from `#__session` where `userid` = ' . $db->Quote( $my->id );
				$query .= ' order by `time` desc limit 1';

				$sql->raw( $query );

				$db->setQuery( $sql );
				$lastLogin = $db->loadObject();

				if( $lastLogin )
				{
					$lastLogin->count = ( Foundry::isJoomla25() ) ? $lastLogin->count + 1 : $lastLogin->count;

					if( $lastLogin->count >= 2 )
					{
						if( $lastLogin->limit < $lastLogin->time )
						{
							$addStream = false;
						}
					}
				}
			}


			if( $addStream )
			{
				$myUser = Foundry::user( $my->id );
				$stream = Foundry::stream();

				$template = $stream->getTemplate();
				$template->setActor( $my->id, SOCIAL_TYPE_USER );
				$template->setContext( $my->id, SOCIAL_TYPE_USERS );
				$template->setVerb( 'login' );
				$template->setType( SOCIAL_STREAM_DISPLAY_MINI );

				$userProfileLink = '<a href="'. $myUser->getPermalink() .'">' . $myUser->getName() . '</a>';
				$title 	 = JText::sprintf( 'PLG_USER_EASYSOCIAL_LOGIN_SITE', $userProfileLink );

				$template->setTitle( $title );
				$template->setAggregate( false );

				$template->setPublicStream( 'core.view' );


				$stream->add( $template );
			}

		}
	}

	/**
	 * Performs various clean ups when a user is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onUserBeforeDelete( $user )
	{
		// Include main file.
		jimport( 'joomla.filesystem.file' );

		$path 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

		if( !JFile::exists( $path ) )
		{
			return false;
		}

		// Include the foundry engine
		require_once( $path );

		$success 	= true;

		// Check if Foundry exists
		if( !Foundry::exists() )
		{
			Foundry::language()->load( 'com_easysocial' , JPATH_ROOT );
			echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
			return;
		}

		if( !$success )
		{
			return false;
		}

		$model 	= Foundry::model( 'Users' );

		$state 	= $model->delete( $user[ 'id' ] );

		if( !$state )
		{
			Foundry::logError( __FILE__ , __LINE__ , 'USER_PLUGIN: Error when deleting a user' );
		}

		// Internal Trigger for onUserBeforeDelete
		$dispatcher 	= Foundry::dispatcher();
		$args 			= array( &$user );

		$dispatcher->trigger( SOCIAL_APPS_GROUP_USER, __FUNCTION__ , $args );

		return true;
	}

	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if( $isnew )
		{
			// Initialise EasySocial's Foundry Framework

			// Include main file.
			jimport( 'joomla.filesystem.file' );

			$path 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

			if( !JFile::exists( $path ) )
			{
				return false;
			}

			// Include the foundry engine
			require_once( $path );

			$success 	= true;

			// Check if Foundry exists
			if( !Foundry::exists() )
			{
				Foundry::language()->load( 'com_easysocial' , JPATH_ROOT );
				echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
				return;
			}

			if( !$success )
			{
				return false;
			}

			// Things that need to do here
			// 1. Insert user record into #__social_users
			// 2. Get the default profile
			// 3. Insert mapping into #__social_profiles_maps

			$userTable = Foundry::table( 'users' );
			$state = $userTable->load( $user['id'] );

			// If no user is found in #__social_users, then only we insert
			// If user is found, means the registration is coming from EasySocial itself.
			// The purpose here is to insert the user data if the registration is handled by other services
			if( !$state )
			{
				// Assign the user id
				$userTable->user_id = $user['id'];

				// Filter the username so that it becomes a valid alias
				$alias 			= JFilterOutput::stringURLSafe( $user['username'] );

				// Check if the alias exists.
				$userModel 			= Foundry::model( 'Users' );

				// Keep the original state of the alias
				$tmp 			= $alias;

				while( $userModel->aliasExists( $alias , $user['id'] ) )
				{
					// Generate a new alias for the user.
					$alias	= $tmp . '-' . rand( 1 , 150 );
				}

				$userTable->alias = $alias;

				$userTable->state = $user['block'] === SOCIAL_JOOMLA_USER_BLOCKED ? SOCIAL_USER_STATE_PENDING : SOCIAL_USER_STATE_ENABLED;

				$userTable->type = 'joomla';

				$userTable->store();

				$profileModel = Foundry::model( 'Profiles' );

				$defaultProfile = $profileModel->getDefaultProfile();

				if( $defaultProfile )
				{
					$defaultProfile->addUser( $user['id'] );
				}
			}
		}

		return true;
	}
}
