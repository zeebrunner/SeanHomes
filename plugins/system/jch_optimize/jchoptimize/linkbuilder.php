<?php

/**
 * JCH Optimize - Aggregate and minify external resources for optmized downloads
 * 
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
defined('_JCH_EXEC') or die('Restricted access');

class JchOptimizeLinkBuilderBase
{

        /**
         * 
         * @return string
         */
        protected function getAsyncAttribute()
        {
                return '';
        }

        /**
         * 
         * @param type $sUrl
         */
        protected function loadCssAsync($sUrl)
        {
                
        }

}

/**
 * 
 * 
 */
class JchOptimizeLinkBuilder extends JchOptimizelinkBuilderBase
{

        /** @var JchOptimizeParser Object       Parser object */
        public $oParser;

        /** @var string         Document line end */
        protected $sLnEnd;

        /** @var string         Document tab */
        protected $sTab;

        /** @var string cache id * */
        protected $params;

        /**
         * Constructor
         * 
         * @param JchOptimizeParser Object  $oParser
         */
        public function __construct($oParser = null)
        {
                $this->oParser = $oParser;
                $this->params  = $this->oParser->params;
                $this->sLnEnd  = $this->oParser->sLnEnd;
                $this->sTab    = $this->oParser->sTab;
        }

        /**
         * Prepare links for the combined files and insert them in the processed HTML
         * 
         */
        public function insertJchLinks()
        {
                $aLinks = $this->oParser->getReplacedFiles();
                
                if (!JchOptimizeHelper::isMsieLT10() && $this->params->get('combine_files_enable', '1'))
                {
                        if ($this->params->get('htaccess', 2) == 2)
                        {
                                JchOptimizeHelper::checkModRewriteEnabled($this->params);
                        }

                        $replace_css_links = FALSE;

                        if ($this->params->get('css', 1) && !empty($aLinks['css']))
                        {
                                $sCssCacheId = $this->getCacheId($aLinks['css']);
                                $sCssCache   = $this->getCombinedFiles($aLinks['css'], $sCssCacheId, 'css');

                                if ($this->params->get('pro_optimizeCssDelivery_enable', '0'))
                                {
                                        $sCriticalCss = '<style type="text/css">' . $this->sLnEnd .
                                                $sCssCache['criticalcss'] . $this->sLnEnd .
                                                '</style>' . $this->sLnEnd .
                                                '</head>';

                                        $sHeadHtml = str_replace('</head>', $sCriticalCss, $this->oParser->getHeadHtml());
                                        $this->oParser->setSearchArea($sHeadHtml, 'head');

                                        $sUrl = $this->buildUrl($sCssCacheId, 'css');
                                        $sUrl = str_replace('JCHI', '0', $sUrl);

                                        $this->loadCssAsync($sUrl);
                                }
                                else
                                {
                                        $replace_css_links = TRUE;
                                }
                        }

                        if ($this->params->get('javascript', 1) && !empty($aLinks['js']))
                        {
                                $sJsCacheId = $this->getCacheId($aLinks['js']);
                                $this->getCombinedFiles($aLinks['js'], $sJsCacheId, 'js');

                                $this->replaceLinks($sJsCacheId, 'js');
                        }

                        if ($replace_css_links)
                        {
                                $this->replaceLinks($sCssCacheId, 'css');
                        }
                }

                if (!empty($aLinks['img']))
                {
                        $this->addImgAttributes($aLinks['img']);
                }

                $this->runCronTasks();
        }

        /**
         * 
         * @return type
         */
        protected function getNewJsLink()
        {
                $sLink = $this->params->get('bottom_js', '0') == '2' ? '</title>' . $this->sLnEnd . $this->sTab : '';
                $sLink .= '<script type="application/javascript" src="URL"';
                $sLink .= $this->params->get('defer_js', 0) ?
                        ($this->isXhtml() ? ' defer="defer"' : ' defer' ) :
                        '';
                $sLink .= $this->getAsyncAttribute();
                $sLink .= '></script>';

                $sNewJsLink = ($this->params->get('bottom_js') == '1') ? $this->sTab . $sLink . $this->sLnEnd . '</body>' : $sLink;

                return $sNewJsLink;
        }

        /**
         * 
         * @return string
         */
        protected function getNewCssLink()
        {
                $sNewCssLink = $this->params->get('bottom_js', '0') ? '</title>' . $this->sLnEnd . $this->sTab : '';
                $sNewCssLink .= '<link rel="stylesheet" type="text/css" ';
                $sNewCssLink .= 'href="URL"/>';

                return $sNewCssLink;
        }

        /**
         * Use generated id to cache aggregated file
         *
         * @param string $sType           css or js
         * @param string $sLink           Url for aggregated file
         */
        protected function getCombinedFiles($aLinks, $sId, $sType)
        {
                JCH_DEBUG ? JchPlatformProfiler::start('GetCombinedFiles - ' . $sType) : null;

                $aArgs = array($aLinks, $sType);

                $oCombiner = new JchOptimizeCombiner($this->params, $this->oParser);
                $aFunction = array(&$oCombiner, 'getContents');

                $bCached = $this->loadCache($aFunction, $aArgs, $sId);

                if ($bCached === FALSE)
                {
                        throw new Exception('Error creating cache file');
                }

                JCH_DEBUG ? JchPlatformProfiler::stop('GetCombinedFiles - ' . $sType, TRUE) : null;

                return $bCached;
        }

        /**
         * 
         * @param type $aImgs
         */
        protected function addImgAttributes($aImgs)
        {
                JCH_DEBUG ? JchPlatformProfiler::start('AddImgAttributes') : null;

                $sHtml = $this->oParser->getBodyHtml();
                $sId   = md5(serialize($aImgs));

                $aImgAttributes = $this->loadCache(array($this, 'getCachedImgAttributes'), array($aImgs), $sId);

                if ($aImgAttributes === false)
                {
                        return;
                }

                $this->oParser->setSearchArea(str_replace($aImgs[0], $aImgAttributes, $sHtml), 'body');

                JCH_DEBUG ? JchPlatformProfiler::stop('AddImgAttributes', true) : null;
        }

        /**
         * 
         * @param type $aImgs
         */
        public function getCachedImgAttributes($aImgs)
        {
                $aImgAttributes = array();
                $total          = count($aImgs[0]);

                for ($i = 0; $i < $total; $i++)
                {
                        $sUrl = !empty($aImgs[1][$i]) ? $aImgs[1][$i] : (!empty($aImgs[2][$i]) ? $aImgs[2][$i] : $aImgs[3][$i]);

                        if (empty($sUrl) || !$this->oParser->isHttpAdapterAvailable($sUrl) || preg_match('#^https#', $sUrl) && !extension_loaded('openssl') ||
                                preg_match('#^data:#', $sUrl))
                        {
                                $aImgAttributes[] = $aImgs[0][$i];
                                continue;
                        }

                        $sPath = JchOptimizeHelper::getFilePath($sUrl);
                        $aSize = getimagesize($sPath);

                        if ($aSize === false || empty($aSize) || ($aSize[0] == '1' && $aSize[1] == '1'))
                        {
                                $aImgAttributes[] = $aImgs[0][$i];
                                continue;
                        }

                        $sImg             = preg_replace('#(?:width|height)\s*+=(?:\s*+"([^">]*+)"|\s*+\'([^\'>]*+)\'|([^\s>]++))#i', '',
                                                         $aImgs[0][$i]);
                        $aImgAttributes[] = preg_replace('#\s*+/?>#', ' ' . $aSize[3] . ' />', $sImg);
                }

                return $aImgAttributes;
        }

        /**
         * 
         */
        protected function runCronTasks()
        {
                JCH_DEBUG ? JchPlatformProfiler::start('RunCronTasks') : null;

                $sId = md5($this->oParser->sFileHash . 'CRONTASKS');

                $aArgs = array($this->oParser);

                $oCron     = new JchOptimizeCron($this->params);
                $aFunction = array(&$oCron, 'runCronTasks');

                $bCached = $this->loadCache($aFunction, $aArgs, $sId);

                JCH_DEBUG ? JchPlatformProfiler::stop('RunCronTasks', TRUE) : null;
        }

        /**
         * 
         * @param type $aUrlArrays
         * @return type
         */
        private function getCacheId($aUrlArrays)
        {
                $id = md5(serialize($aUrlArrays));

                return $id;
        }

        /**
         * Returns url of aggregated file
         *
         * @param string $sFile		Aggregated file name
         * @param string $sType		css or js
         * @param mixed $bGz		True (or 1) if gzip set and enabled
         * @param number $sTime		Expire header time
         * @return string			Url of aggregated file
         */
        protected function buildUrl($sId, $sType)
        {
                $iTime = (int) $this->params->get('cache_lifetime', '1');
                $bGz   = $this->isGZ();

                $htaccess = $this->params->get('htaccess', 0);

                if ($htaccess == 1 || $htaccess == 3)
                {
                        $sPath = $this->cookieLessDomain($sType);
                        $sPath = $htaccess == 3 ? $sPath . '3' : $sPath;
                        $sUrl  = $sPath . JchPlatformPaths::rewriteBase()
                                . ($bGz ? 'gz/' : 'nz/') . $iTime . '/JCHI/' . $sId . '.' . $sType;
                }
                else
                {
                        $oUri = clone JchPlatformUri::getInstance(JchPlatformPaths::assetPath());

                        $oUri->setPath($oUri->getPath() . '2/jscss.php');

                        $aVar         = array();
                        $aVar['f']    = $sId;
                        $aVar['type'] = $sType;
                        $aVar['gz']   = $bGz ? 'gz' : 'nz';
                        $aVar['d']    = $iTime;
                        $aVar['i']    = 'JCHI';

                        $oUri->setQuery($aVar);

                        $sUrl = htmlentities($oUri->toString());
                }

                return ($sUrl);
        }

        /**
         * 
         * @return type
         */
        protected function cookieLessDomain($sType)
        {
                $pro_staticfiles = $this->params->get('pro_staticfiles', array('css', 'js', 'jpe?g', 'gif', 'png', 'ico', 'bmp', 'pdf'));

                if ($this->params->get('pro_cookielessdomain_enable', '0') && in_array($sType, $pro_staticfiles))
                {
                        return JchOptimizeHelper::cookieLessDomain($this->params, JchPlatformPaths::assetPath(TRUE));
                }

                return JchPlatformPaths::assetPath();
        }

        /**
         * Insert url of aggregated file in html
         *
         * @param string $sNewLink   Url of aggregated file
         */
        protected function replaceLinks($sId, $sType)
        {
                JCH_DEBUG ? JchPlatformProfiler::start('ReplaceLinks - ' . $sType) : null;

                $sSearchArea = $this->oParser->getHeadHtml();
                $sSearchArea .= $this->oParser->getBodyHtml();

                $sLink = $this->{'getNew' . ucfirst($sType) . 'Link'}();
                $sUrl  = $this->buildUrl($sId, $sType);

                $sNewLink = str_replace('URL', $sUrl, $sLink);

                if ($sType == 'js' && $this->params->get('bottom_js', '0') == '1')
                {
                        $sNewLink             = str_replace('JCHI', '0', $sNewLink);
                        $sSearchArea          = str_replace('</body>', $sNewLink, $sSearchArea);
                        $this->oParser->sHtml = str_replace('</body>', $sNewLink, $this->oParser->sHtml);
                }
                elseif ($this->params->get('bottom_js', '0'))
                {
                        $sNewLink    = str_replace('JCHI', '0', $sNewLink);
                        $sSearchArea = str_replace('</title>', $sNewLink, $sSearchArea);
                }
                else
                {
                        $sSearchArea = preg_replace_callback('#<JCH_' . strtoupper($sType) . '([^>]++)>#',
                                                                                   function($aM) use ($sNewLink)
                        {
                                return str_replace('JCHI', $aM[1], $sNewLink);
                        }, $sSearchArea);
                }

                $this->oParser->setSearchArea(preg_replace($this->oParser->getBodyRegex(), '', $sSearchArea, 1), 'head');
                $this->oParser->setSearchArea(preg_replace($this->oParser->getHeadRegex(), '', $sSearchArea, 1), 'body');

                JCH_DEBUG ? JchPlatformProfiler::stop('ReplaceLinks - ' . $sType, TRUE) : null;
        }

        /**
         * Create and cache aggregated file if it doesn't exists.
         *
         * @param array $aFunction    Name of function used to aggregate files
         * @param array $aArgs        Arguments used by function above
         * @param string $sId         Generated id to identify cached file
         * @return boolean           True on success
         */
        public function loadCache($aFunction, $aArgs, $sId)
        {
                $iLifeTime = (int) $this->params->get('cache_lifetime', '1') * 24 * 60 * 60;
                $bCached   = JchPlatformCache::getCallbackCache($sId, $iLifeTime, $aFunction, $aArgs);

                return $bCached;
        }

        /**
         * Check if gzip is set or enabled
         *
         * @return boolean   True if gzip parameter set and server is enabled
         */
        public function isGZ()
        {
                return ($this->params->get('gzip', 0) && extension_loaded('zlib') && !ini_get('zlib.output_compression')
                        && (ini_get('output_handler') != 'ob_gzhandler'));
        }

        /**
         * Determine if document is of XHTML doctype
         * 
         * @return boolean
         */
        public function isXhtml()
        {
                return (bool) preg_match('#^\s*+(?:<!DOCTYPE(?=[^>]+XHTML)|<\?xml.*?\?>)#i', trim($this->oParser->sHtml));
        }

        /**
         * 
         * @param type $sScript
         * @return type
         */
        protected function cleanScript($sScript)
        {
                if (!$this->isXhtml())
                {
                        $sScript = str_replace(array('<script type="text/javascript"><![CDATA[', ']]></script>'),
                                               array('<script type="text/javascript">', '</script>'), $sScript);
                }

                return $sScript;
        }

        ##<procode>##

        /**
         * 
         * @param type $sUrl
         */
        protected function loadCssAsync($sUrl)
        {
                $sScript = '<script type="text/javascript">/*<![CDATA[*/
                (function() {
                        var lastTime = 0;
                        var vendors = [\'ms\', \'moz\', \'webkit\', \'o\'];
                        for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
                            window.requestAnimationFrame = window[vendors[x]+\'RequestAnimationFrame\'];
                        }
                        if (!window.requestAnimationFrame)
                            window.requestAnimationFrame = function(callback, element) {
                                var currTime = new Date().getTime();
                                var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                                var id = window.setTimeout(function() { callback(currTime + timeToCall); }, 
                                  timeToCall);
                                lastTime = currTime + timeToCall;
                                return id;
                            };
                }());        

                var callback = function() {
                        var link = document.createElement("link");
                        var head = document.getElementsByTagName("head")[0];
                        link.type = "text/css";
                        link.rel = "stylesheet";
                        link.href = "' . html_entity_decode($sUrl) . '";
                        head.appendChild(link);
//                        head.parentNode.insertBefore(link, head);
                };
//                window.addEventListener("load", function(){
                        window.requestAnimationFrame(callback);
//                });
/*]]>*/</script>
<noscript><link type="text/css" rel="stylesheet" property="stylesheet" href="' . $sUrl . '" /></noscript>'
                        . $this->sLnEnd . $this->sTab . '</body>';

                $sScript              = $this->cleanScript($sScript);
                $sBodyHtml            = $this->oParser->getBodyHtml();
                $sBodyHtml            = str_replace('</body>', $sScript, $sBodyHtml);
                $this->oParser->sHtml = str_replace('</body>', $sScript, $this->oParser->sHtml);

                $this->oParser->setSearchArea($sBodyHtml, 'body');
        }

        /**
         * Adds the async attribute to the aggregated js file link
         * 
         * @return string
         */
        protected function getAsyncAttribute()
        {
                if ($this->params->get('pro_loadAsynchronous', '0'))
                {
                        $sAsyncAttribute = $this->isXhtml() ? ' async="async"' : ' async';

                        return $sAsyncAttribute;
                }
                else
                {
                        return parent::getAsyncAttribute();
                }
        }

        ##</procode>##
}
