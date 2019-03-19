<?php
/**
 * @version   $Id: AbstractStorageService.php 11264 2013-06-05 15:54:08Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

abstract class RokUpdater_AbstractStorageService extends RokUpdater_AbstractService implements RokUpdater_IStorageService
{
	/**
	 *
	 */
	const ACCESS_TOKEN_QUERY_KEY = 'access_token';
	/**
	 *
	 */
	const IGNORED_QUERY_KEY = 'ignore';
	/**
	 *
	 */
	const PLATFORM_QUERY_KEY = 'platform';
	const SITE_ID_QUERY_KEY  = 'site_id';

	/**
	 * @param RokUpdater_AccessTokenInfo $subscriber_info
	 */
	public function storeSubscriberInfo(RokUpdater_Subscriber_Info $subscriber_info)
	{
		/** @var $table JTableExtension */
		$table = JTable::getInstance('Extension');
		$table->load($table->find(array('type' => 'plugin', 'folder' => 'system', 'element' => 'rokupdater')));
		$table->custom_data = json_encode($subscriber_info);
		$table->store();
	}

	/**
	 * @return RokUpdater_AccessTokenInfo|null
	 */
	public function getSubscriberInfo()
	{
		$access_token_info = null;
		/** @var $table JTableExtension */
		$table = JTable::getInstance('Extension');
		$table->load($table->find(array('type' => 'plugin', 'folder' => 'system', 'element' => 'rokupdater')));
		if (!empty($table->custom_data)) {
			$access_token_info = RokUpdater_Subscriber_Factory::createFromJSON($table->custom_data);
		}
		return $access_token_info;
	}

	/**
	 *
	 */
	public function removeSubscriberInfo($notice = null)
	{
		$subscriber_info = RokUpdater_Subscriber_Factory::createEmpty($notice);
		$this->storeSubscriberInfo($subscriber_info);
	}

	/**
	 * @param $token
	 *
	 * @throws RokUpdater_Exception
	 */
	public function updateAccessToken($site_id, $token = null)
	{
		// get the RT update items
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('update_site_id, location')->from('#__update_sites');
		$query->where('location like ' . $query->quote($this->container->updaters_server_regex_pattern));
		$db->setQuery($query);
		try {
			$update_items = $db->loadAssocList('update_site_id');
			if ($db->getErrorNum()) {
				throw new RokUpdater_Exception(sprintf('Database error - %s', $db->getErrorMsg(true)));
			}
		} catch (Exception $e) {
			throw new RokUpdater_Exception(sprintf('Database error - %s', $e->getMessage()));
		}

		$jversion = new JVersion();

		// Append the access token to any RT update URL
		foreach ($update_items as $id => $row_info) {
			$uri = new RokUpdater_Uri(trim($row_info['location']));
			$uri->addQueryParam(self::SITE_ID_QUERY_KEY, $site_id);
			if (null !== $token) {
				$uri->addQueryParam(self::ACCESS_TOKEN_QUERY_KEY, $token);
				$uri->removeQueryParam(self::IGNORED_QUERY_KEY);
			} else {
				$uri->removeQueryParam(self::ACCESS_TOKEN_QUERY_KEY);
				$uri->removeQueryParam(self::IGNORED_QUERY_KEY);
			}
			$location = $uri->getAbsoluteUri();
			$location .= (!count($uri->getQueryParams())) ? '?' : '&';
			$location .= self::IGNORED_QUERY_KEY . '=update.xml';

			$update_query = $db->getQuery(true);
			$update_query->update('#__update_sites')->set(sprintf('location = %s', $update_query->quote($location)));
			$update_query->where(sprintf('update_site_id = %d', (int)$id));
			$db->setQuery($update_query);
			try {
				if ((method_exists($db, 'execute') && !$db->execute() || (method_exists($db, 'query') && !$db->query()))) {
					throw new RokUpdater_Exception(sprintf('Database error - %s', $db->getErrorMsg(true)));
				}
			} catch (Exception $e) {
				throw new RokUpdater_Exception(sprintf('Database error - %s', $e->getMessage()));
			}
		}
	}

	public function mergeUpdateSites()
	{
		// get the RT update items
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('update_site_id')->from('#__update_sites');
		$query->where('location like ' . $query->quote($this->container->updaters_server_regex_pattern));
		$db->setQuery($query);
		try {
			$update_items = $db->loadColumn();
			if ($db->getErrorNum()) {
				throw new RokUpdater_Exception(sprintf('Database error - %s', $db->getErrorMsg(true)));
			}

		} catch (Exception $e) {
			throw new RokUpdater_Exception(sprintf('Database error - %s', $e->getMessage()));
		}


		$extensions_query = $db->getQuery(true);
		$extensions_query->select('extension_id')->from('#__update_sites_extensions')->where('update_site_id = ' . $update_items[0]);
		$db->setQuery($extensions_query);
		try {
			$extension_items = $db->loadColumn();
			if ($db->getErrorNum()) {
				throw new RokUpdater_Exception(sprintf('Database error - %s', $db->getErrorMsg(true)));
			}

		} catch (Exception $e) {
			throw new RokUpdater_Exception(sprintf('Database error - %s', $e->getMessage()));
		}

		if (!empty($update_items) && count($update_items) > 1) {
			$update_query = $db->getQuery(true);
			$update_query->update('#__update_sites_extensions')->set(sprintf('update_site_id = %d', $update_items[0]));
			array_shift($update_items);
			$update_query->where(sprintf('update_site_id in (%s)', implode(',', $update_items)));
			if (count($extension_items) > 0){
				$update_query->where(sprintf('extension_id not in (%s)', implode(',', $extension_items)));
			}
			$db->setQuery($update_query);
			try {
				if ((method_exists($db, 'execute') && !$db->execute() || (method_exists($db, 'query') && !$db->query()))) {
					throw new RokUpdater_Exception(sprintf('Database error - %s', $db->getErrorMsg(true)));
				}
			} catch (Exception $e) {
				throw new RokUpdater_Exception(sprintf('Database error - %s', $e->getMessage()));
			}

			$del_extensions_query = $db->getQuery(true);
			$del_extensions_query->delete('#__update_sites_extensions')->where(sprintf('update_site_id in (%s)', implode(',', $update_items)));
			$db->setQuery($del_extensions_query);
			try {
				if ((method_exists($db, 'execute') && !$db->execute() || (method_exists($db, 'query') && !$db->query()))) {
					throw new RokUpdater_Exception(sprintf('Database error - %s', $db->getErrorMsg(true)));
				}
			} catch (Exception $e) {
				throw new RokUpdater_Exception(sprintf('Database error - %s', $e->getMessage()));
			}

			$del_site_query = $db->getQuery(true);
			$del_site_query->delete('#__update_sites')->where(sprintf('update_site_id in (%s)', implode(',', $update_items)));
			$db->setQuery($del_site_query);
			try {
				if ((method_exists($db, 'execute') && !$db->execute() || (method_exists($db, 'query') && !$db->query()))) {
					throw new RokUpdater_Exception(sprintf('Database error - %s', $db->getErrorMsg(true)));
				}
			} catch (Exception $e) {
				throw new RokUpdater_Exception(sprintf('Database error - %s', $e->getMessage()));
			}

		}


	}
}
