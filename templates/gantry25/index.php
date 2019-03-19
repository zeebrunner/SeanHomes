<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
/**
* @version   $Id: index.php 15529 2013-11-13 22:04:39Z kevin $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );



session_start();
$counter_name = "counter.txt";

// Check if a text file exists. If not create one and initialize it to zero.
if (!file_exists($counter_name)) {
  $f = fopen($counter_name, "w");
  fwrite($f,"0");
  fclose($f);
}

// Read the current value of our counter file
// $f = fopen($counter_name,"r");
// $counterVal = fread($f, filesize($counter_name));
// fclose($f);

// Has visitor been counted in this session?
// If not, increase counter value by one
if(!isset($_SESSION['hasVisited'])){
	$_SESSION['hasVisited']=$counterVal;
	$counterVal++;
	$f = fopen($counter_name, "w");
	fwrite($f, $counterVal);
	fclose($f);
}

//echo "You are visitor number $counterVal to this site";


// load and inititialize gantry class
require_once(dirname(__FILE__) . '/lib/gantry/gantry.php');
$gantry->init();

// get the current preset
$gpreset = str_replace(' ','',strtolower($gantry->get('name')));

?>
<!doctype html>
<html xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
<head>
	<script>document.cookie='resolution='+Math.max(screen.width,screen.height)+'; path=/';</script>
	<?php if ($gantry->get('layout-mode') == '960fixed') : ?>
	<meta name="viewport" content="width=960px, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<?php elseif ($gantry->get('layout-mode') == '1200fixed') : ?>
	<meta name="viewport" content="width=1200px, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<?php else : ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php //if ($gantry->get('layout-mode') == 'responsivefull') 	$gantry->addLess('grid-responsive-full.less','grid-responsive-full.css', 5); ?>
		<?php $gantry->addLess('grid-responsive.less','grid-responsive.css', 5); ?>
	<?php endif; ?>
<?php if ($gantry->browser->name == 'ie') : ?>
	<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<?php endif; ?>
<?php
	$gantry->displayHead();
	if ($gantry->get('layout-mode') != 'responsivefull') 	$gantry->addStyle('grid-responsive.css', 5);
	$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
	if ($gantry->browser->name == 'ie'){
		if ($gantry->browser->shortversion == 9){
			$gantry->addInlineScript("if (typeof RokMediaQueries !== 'undefined') window.addEvent('domready', function(){ RokMediaQueries._fireEvent(RokMediaQueries.getQuery()); });");
		}
		if ($gantry->browser->shortversion == 8){
			$gantry->addScript('html5shim.js');
		}
	}
	if ($gantry->get('layout-mode', 'responsive') == 'responsive') $gantry->addScript('rokmediaqueries.js');
	if ($gantry->get('loadtransition')) {
	$gantry->addScript('load-transition.js');
	$hidden = ' class="rt-hidden"';}
?>


<?php if(JRequest::getVar("utm_campaign")) setcookie("UTM_CAMPAIGN", JRequest::getVar("utm_campaign"), 0,'/','.seanhomes.ca'); ?>
<?php if(JRequest::getVar("utm_source")) setcookie("UTM_SOURCE", JRequest::getVar("utm_source"), 0,'/','.seanhomes.ca'); ?>
<?php if(JRequest::getVar("utm_medium")) setcookie("UTM_MEDIUM", JRequest::getVar("utm_medium"), 0,'/','.seanhomes.ca'); ?>
<?php if(JRequest::getVar("utm_term")) setcookie("UTM_TERM", JRequest::getVar("utm_term"), 0,'/','.seanhomes.ca'); ?>
<?php if(JRequest::getVar("utm_content")) setcookie("UTM_CONTENT", JRequest::getVar("utm_content"), 0,'/','.seanhomes.ca'); ?>
<?php if(JRequest::getVar("gclid")) setcookie("ADWORDS_VISITOR", "Yes", 0,'/','.seanhomes.ca');	?>



<?php
	$gantry->addScript('sean.js');
	$gantry->addScript('jquery.placeholder.min.js');


	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$menuItem = $menu->getActive();
	$jinput = $app->input;

	if ($menuItem->id == 155) {
		$gantry->addScript('https://maps.google.ca/maps/api/js?v=3.x&language=en&sensor=false');
		$gantry->addScript('images/amenities/amenities.js');
		$gantry->addLess('amenities.less', 'amenities.css', 22);
	} else if ($menuItem->id == 302) {
		$gantry->addLess('makeiteasy.less', 'makeiteasy.css', 22);

	} else if($menuItem->id == 426) { // Appontment Booking
		//header("Location: ".JURI::root(),TRUE,301);
		//exit();

		$gantry->addLess('appt.less', 'appt.css', 21);
		$gantry->addScript('appt.js');
		$gantry->addScript('components/com_rsappt_pro3/date.js');

	}
	$pageclass = "";

	if (is_object( $menuItem )) {
		$pageclass = $menuItem->params->get('pageclass_sfx');
	}

	$permission_granted = FALSE;




	if(isset($_COOKIE['UBAM'])||$_SESSION['UBAM']) {
		$permission_granted = TRUE;
	}




	if(!$permission_granted) {
		if($_SESSION['hasVisited']%2==0) {
			$testing = 'testA-Left';
		} else {
			$testing = 'testB-Right';
		}
	}

	//$pageclass == "restricted"
  //?from='.$_SERVER['REQUEST_URI']
	if(($pageclass == "restricted") && !$permission_granted) {
		header("Location: ".JURI::root().'register.html?from='.$_SERVER['REQUEST_URI'],TRUE,303); /* Redirect browser to registration page */
		exit();
	}



	$useremail = urldecode($jinput->getString('email',''));

	if($useremail!='') {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
		->select($db->quoteName(array('cookie_value')))
		->from($db->quoteName('#__bamuserdata'))
		->where($db->quoteName('email')." = ".$db->quote($useremail));
		$db->setQuery($query);
		$record_exists = $db->loadAssoc();
		if($record_exists) {

			setcookie("UBAM", $record_exists['cookie_value'], time()+3600*24*365*5);
			$page               = $_SERVER['REQUEST_URI'];
			$pattern_to_exclude = 'email='.$useremail;

			$redirect_url = str_replace($pattern_to_exclude, '', $page);
			$redirect_url = str_replace('&&', '&', $redirect_url);
			$redirect_url = str_replace('?&', '?', $redirect_url);

			header("Location: ".$redirect_url);
			exit();
		}
	}



	$bampageview = $jinput->getString('view');


?>

<script>
	//jQuery('input, textarea').placeholder();
</script>


<script>
window.gaLoaded = false;
jQuery(window).on('load', function(){
  if(!window.gaLoaded){
    window.gaLoaded = true;
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PQC83Q4');
  }
});
</script>
</head>
<body <?php echo $gantry->displayBodyTag(); ?> id="back<?php echo rand (1, 2);?>">

  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQC83Q4"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <?php /** Begin Top Surround **/ if ($gantry->countModules('top') or $gantry->countModules('header')) : ?>
    <header id="rt-top-surround">
		<?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
		<div id="rt-top" <?php echo $gantry->displayClassesByTag('rt-top'); ?>>
			<div class="rt-container">
				<?php echo $gantry->displayModules('top','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Top **/ endif; ?>
		<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
		<div id="rt-header">
			<div class="rt-container">
				<?php echo $gantry->displayModules('header','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Header **/ endif; ?>
	</header>
	<?php /** End Top Surround **/ endif; ?>
	<?php /** Begin Drawer **/ if ($gantry->countModules('drawer')) : ?>
    <div id="rt-drawer">
        <div class="rt-container">
            <?php echo $gantry->displayModules('drawer','standard','standard'); ?>
            <div class="clear"></div>
        </div>
    </div>
    <?php /** End Drawer **/ endif; ?>
	<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
	<div id="rt-showcase">
		<div class="rt-showcase-pattern">
			<div class="rt-container">
				<?php echo $gantry->displayModules('showcase','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php /** End Showcase **/ endif; ?>
	<div id="rt-transition"<?php if ($gantry->get('loadtransition')) echo $hidden; ?>>
		<div id="rt-mainbody-surround" <?php if($testing) echo "class=\"".$testing."\""; ?>>
			<?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
			<div id="rt-feature">
				<div class="rt-container">
					<?php echo $gantry->displayModules('feature','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Feature **/ endif; ?>
			<?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
			<div id="rt-utility">
				<div class="rt-container">
					<?php echo $gantry->displayModules('utility','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Utility **/ endif; ?>
			<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
			<div id="rt-breadcrumbs">
				<div class="rt-container">
					<?php echo $gantry->displayModules('breadcrumb','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Breadcrumbs **/ endif; ?>
			<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
			<div id="rt-maintop">
				<div class="rt-container">
					<?php echo $gantry->displayModules('maintop','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Main Top **/ endif; ?>
			<?php /** Begin Full Width**/ if ($gantry->countModules('fullwidth')) : ?>
			<div id="rt-fullwidth">
				<?php echo $gantry->displayModules('fullwidth','basic','basic'); ?>
					<div class="clear"></div>
				</div>
			<?php /** End Full Width **/ endif; ?>
			<?php /** Begin Main Body **/ ?>
			<div class="rt-container">
		    		<?php echo $gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard'); ?>
						<?php /** Begin Learn or Shop Sidebar **/ if ($gantry->countModules('learnshop')) : ?>
						<?php echo $gantry->displayModules('learnshop','basic','basic'); ?>
						<?php /** End Learn or Shop **/ endif; ?>

		   	</div>
			<?php /** End Main Body **/ ?>
			<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
			<div id="rt-mainbottom">
				<div class="rt-container">
					<?php echo $gantry->displayModules('mainbottom','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Main Bottom **/ endif; ?>
			<?php /** Begin Extension **/ if ($gantry->countModules('extension')) : ?>
			<div id="rt-extension">
				<div class="rt-container">
					<?php echo $gantry->displayModules('extension','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Extension **/ endif; ?>
		</div>
	</div>
	<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
	<div id="rt-bottom">
		<div class="rt-container">
			<?php echo $gantry->displayModules('bottom','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End Bottom **/ endif; ?>
	<?php /** Begin Footer Section **/ if ($gantry->countModules('footer') or $gantry->countModules('copyright')) : ?>
	<footer id="rt-footer-surround">
		<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
		<div id="rt-footer">
			<div class="rt-container">
				<?php echo $gantry->displayModules('footer','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Footer **/ endif; ?>
		<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
		<div id="rt-copyright">
			<div class="rt-container">
				<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Copyright **/ endif; ?>
	</footer>
	<?php /** End Footer Surround **/ endif; ?>
	<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
	<div id="rt-debug">
		<div class="rt-container">
			<?php echo $gantry->displayModules('debug','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End Debug **/ endif; ?>
	<?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
	<?php //echo $gantry->displayModules('analytics','basic','basic'); ?>
	<?php /** End Analytics **/ endif; ?>

	<?php
		$u = $_SERVER['HTTP_USER_AGENT'];

		$isIE7  = (bool)preg_match('/msie 7./i', $u );
		$isIE8  = (bool)preg_match('/msie 8./i', $u );
		$isIE9  = (bool)preg_match('/msie 9./i', $u );
		$isIE10 = (bool)preg_match('/msie 10./i', $u );

	if (!$isIE9) { // the below code breaks layout in ie9, fix needed !!! This should work for real (not emulated IE 9)
	?>


	<noscript>
		<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/974373338/?value=0&amp;guid=ON&amp;script=0"/>
		</div>
	</noscript>
<?php
}
?>
  <style>
  .gf-menu{
    text-transform: lowercase;
  }
  </style>

	</body>
</html>
<?php
$gantry->finalize();
?>
