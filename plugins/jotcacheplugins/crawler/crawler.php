<?php
/*
 * @version $Id: crawler.php,v 1.13 2013/12/09 05:35:39 Jot Exp $
 * @package JotCachePlugins
 * @category Joomla 2.5
 * @copyright (C) 2011-2014 Vladimir Kanich
 * @license GPL2
 */
defined('_JEXEC') or die;
include_once JPATH_ADMINISTRATOR . '/components/com_jotcache/helpers/browseragents.php';
class plgJotcachepluginsCrawler extends JPlugin {
private $baseUrl;
private $logging;
private $hits;
function onJotcacheRecache($starturl, $jcplugin, $jcparams, $jcstates) {
$plgParams = $this->params;
if ($jcplugin != 'crawler') {
return;
}$this->baseUrl = $starturl;
$params = JComponentHelper::getParams('com_jotcache');
$database = JFactory::getDBO();
/* @var $query JDatabaseQuery */$query = $database->getQuery(true);
$query->update($database->quoteName('#__jotcache'))
->set($database->quoteName('agent') . ' = ' . $database->quote(0));
$database->setQuery($query)->query();
$this->logging = $params->get('recachelog', 0) == 1 ? true : false;
if ($this->logging) {
JLog::add(sprintf('....running in plugin %s', $jcplugin), JLog::INFO, 'jotcache.recache');
}$noHtmlFilter = JFilterInput::getInstance();
$depth = $noHtmlFilter->clean($jcstates['depth'], 'int');
$depth++;
$activeBrowsers = BrowserAgents::getActiveBrowserAgents();
$this->hits = array();
$ret = '';
foreach ($activeBrowsers as $browser => $def) {
$agent = $def[1] . ' jotcache \r\n';
$this->hits[$browser] = 0;
$ret = $this->crawl_page($starturl, $browser, $agent, $depth);
if ($ret == 'STOP') {
break;
}}return array("crawler", $ret, $this->hits);
}function crawl_page($url, $browser, $agent, $depth = 5) {
static $seen = array();
$url = htmlspecialchars_decode($url);
if ($this->hits[$browser] == 0) {
$seen = null;
}$hash = md5(strtolower($url) . $browser);
if (isset($seen[$hash]) || $depth === 0 || (stripos($url, $this->baseUrl) !== 0)) {
return;
}$seen[$hash] = true;
$html = RecacheRunner::getData($url, $agent);
preg_match_all('~<a.*?href="(.*?)".*?>~', $html, $matches);
foreach ($matches[1] as $href) {
$this->hits[$browser]++;
if (!file_exists(JPATH_CACHE . '/page/jotcache_recache_flag_tmp.php')) {
return 'STOP';
}if (strpos($href, '#') !== FALSE) {
continue;
}if (0 !== strpos($href, 'http')) {               $path = '/' . ltrim($href, '/');               $parts = parse_url($url);
$href = $parts['scheme'] . '://';
$href .= $parts['host'];
if (isset($parts['port'])) {
$href .= ':' . $parts['port'];
}$href .= $path;
}$this->crawl_page($href, $browser, $agent, $depth - 1);     }
return 'DONE';
}}?>