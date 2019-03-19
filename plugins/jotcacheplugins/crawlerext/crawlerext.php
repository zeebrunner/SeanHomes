<?php
/*
 * @version $Id: crawlerext.php,v 1.7 2013/12/09 05:35:39 Jot Exp $
 * @package JotCachePlugins
 * @category Joomla 2.5
 * @copyright (C) 2011-2014 Vladimir Kanich
 * @license GPL2
 */
defined('_JEXEC') or die;
include_once JPATH_ADMINISTRATOR . '/components/com_jotcache/helpers/browseragents.php';
class plgJotcachepluginsCrawlerext extends JPlugin {
private $baseUrl;
private $logging;
private $hits;
function onJotcacheRecache($starturl, $jcplugin, $jcparams, $jcstates) {
$plgParams = $this->params;
if ($jcplugin != 'crawlerext') {
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
$ret = $this->crawl_page($starturl, $browser, $agent, $depth);
if ($ret == 'STOP') {
break;
}}return array("crawlerext", $ret, $this->hits);
}function crawl_page($url, $browser, $agent, $depth = 5) {
$seen = array();
$this->hits[$browser] = 0;
$hrefs = array(array());
$hrefs[0][0] = $url;
for ($i = 0; $i < $depth; $i++) {
if ($this->logging && $i > 0) {
JLog::add(sprintf('....for browser %s returned %d links on level %d', $browser, count($hrefs[$i]), $i), JLog::INFO, 'jotcache.recache');
}foreach ($hrefs[$i] as $href) {
$href = htmlspecialchars_decode($href);
$html = RecacheRunner::getData($href, $agent);
preg_match_all('~<a.*?href="(.*?)".*?>~', $html, $matches);
foreach ($matches[1] as $link) {
$this->hits[$browser]++;
if (!file_exists(JPATH_CACHE . '/page/jotcache_recache_flag_tmp.php')) {
return 'STOP';
}if (strpos($link, '#') !== FALSE) {
continue;
}if (0 !== strpos($link, 'http')) {                   $path = '/' . ltrim($link, '/');                   $parts = parse_url($url);
$link = $parts['scheme'] . '://';
$link .= $parts['host'];
if (isset($parts['port'])) {
$link .= ':' . $parts['port'];
}$link .= $path;
}if (stripos($link, $this->baseUrl) !== 0) {
continue;
}$hash = md5(strtolower($link) . $browser);
if (isset($seen[$hash])) {
continue;
}$seen[$hash] = true;
$hrefs[$i + 1][] = $link;
}}}return 'DONE';
}}?>