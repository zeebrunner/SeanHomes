<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * @version 1.2
 * @package MortgagePaymentcalculator 
 * @copyright 2012 OrdaSoft
 * @author 2012 Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru)
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @description Mortgage Payment calculator
 * Homepage: http://www.ordasoft.com
*/


$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
global $mosConfig_lang; // for 1.6

$title = $params->get( 'title', '' );
$table_header_color = $params->get( 'table_header_color', '#FFFFFF' );
$table_header_text = $params->get( 'table_header_text', '#000000' );
$currency = $params->get( 'currency', '' );

$mainframe = JFactory::getApplication(); // for 1.6
$GLOBALS['mainframe'] = $mainframe;

// load language
$lang_def_en=0;
$lang = JFactory::getLanguage();
foreach ($lang->getLocale() as $locale){
    $mosConfig_lang=$locale;
    if (file_exists(dirname(__FILE__)."/../language/{$mosConfig_lang}.php" )){  
        include_once dirname(__FILE__)."/../language/{$mosConfig_lang}.php";
        $lang_def_en=1;
        break;
    }
}
if($lang_def_en!=1){
    $mosConfig_lang = "english";
    include_once dirname(__FILE__)."/../language/english.php";
}


?>


<noscript>Javascript is required to use Mortgage Payment Calculator</noscript>

<script type="text/javascript" language="javascript">

var d = document;

var amount;
var period;
var cadence;
var rate;

function addRow()
{
    amount = d.getElementById('amount').value;
    period = d.getElementById('period').value;
    cadence = d.getElementById('cadence').value;
    rate = d.getElementById('rate').value;

    var mortgage_payment_calculator = d.getElementById('mortgage_payment_calculator');
    var stab1 = "<table id=\"tabrez\"><tr><td class=\"tab_hed\"><span><?php echo JText::_('_MOD_CALC_MOR_NUMBER'); ?></span></td><td class=\"tab_hed\"><span><?php echo JText::_('_MOD_CALC_MOR_MONTHLY_PAYMENTS'); ?><?php echo ", " .$currency; ?></span></td><td class=\"tab_hed\"><span><?php echo JText::_('_MOD_CALC_MOR_PRINCIPAL_PAID'); ?><?php echo ", " .$currency; ?></span></td><td class=\"tab_hed\"><span><?php echo JText::_('_MOD_CALC_MOR_INTEREST_PAID'); ?><?php echo ", " .$currency; ?></span></td><td class=\"tab_hed\"><span><?php echo JText::_('_MOD_CALC_MOR_BALANCE'); ?><?php echo ", " .$currency; ?></span></td></tr>";
    
    var ir = amount*Math.pow((1+((rate/100)/cadence)),cadence*period)*(((rate/100)/cadence)/(Math.pow(1+((rate/100)/cadence),cadence*period)-1));
    var sir = ir*cadence*period;
    var tmpamount = amount;
    var balance = amount;

    for (var i = 0; i < (period*cadence); i++) {

	var qi = tmpamount*((rate/cadence)/100);
	var qc = ir - qi;
	tmpamount = tmpamount-qc;
	balance = balance - qc;

	stab1 = stab1 + "<tr><td>"+(i+1)+"</td><td>$"+(ir).toFixed(2)+"</td><td>$"+(qc).toFixed(2)+"</td><td>$"+(qi).toFixed(2)+"</td><td>$"+(balance).toFixed(2)+"</td></tr>";
    
    }
    
    mortgage_payment_calculator.innerHTML = stab1+"</table>";

    var scadence = "";

    switch (cadence) {

    case "12":
      scadence = "<?php echo JText::_('_MOD_CALC_MOR_MONTHLY'); ?>";
      break;
    case "6":
      scadence = "<?php echo JText::_('_MOD_CALC_MOR_TWO_MONTH'); ?>";
      break;
    case "4":
      scadence = "<?php echo JText::_('_MOD_CALC_MOR_QUARTERLY'); ?>";
      break;
    case "2":
      scadence = "<?php echo JText::_('_MOD_CALC_MOR_SEMI_ANNUAL'); ?>";
      break;
    case "1":
      scadence = "<?php echo JText::_('_MOD_CALC_MOR_ANNUAL'); ?>";
      break;
    }

    var tab2 = d.getElementById('tab2');

    var stab2 = "<ul>";
    stab2 = stab2 + "<li><b><?php echo JText::_('_MOD_CALC_MOR_LOAN_AMOUNT'); ?><?php echo ", " .$currency; ?>: </b>$"+number_format(parseFloat(amount))+"</li>";
    stab2 = stab2 + "<li><b><?php echo JText::_('_MOD_CALC_MOR_LOAN_TERM'); ?>: </b>"+period+"</li>";
    stab2 = stab2 + "<li><b><?php echo JText::_('_MOD_CALC_MOR_AMORTIZATION'); ?>: </b>"+scadence+"</li>";
	stab2 = stab2 + "<li><b><?php echo JText::_('_MOD_CALC_MOR_MONTHLY_PAYMENTS'); ?>: </b>$"+number_format(ir)+"</li>";
    stab2 = stab2 + "<li><b><?php echo JText::_('_MOD_CALC_MOR_INTEREST_RATE')."(%)"; ?>: </b>"+rate+"</li>";
   /* stab2 = stab2 + "<li><b><?php echo JText::_('_MOD_CALC_MOR_TOTAL_PAID'); ?><?php echo ", " .$currency; ?>: </b>$"+number_format(parseFloat(sir))+"</li>";
    stab2 = stab2 + "<li><b><?php //echo JText::_('_MOD_CALC_MOR_INTEREST_PAID'); ?><?php ?><?php // echo ", " .$currency; ?>: </b>$"+number_format(parseFloat(sir - amount))+"</li>";*/

    tab2.innerHTML = stab2 + "</ul>";
    
}
function number_format(number, decimals, dec_point, thousands_sep) {
  //  discuss at: http://phpjs.org/functions/number_format/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: davook
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Theriault
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Michael White (http://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (http://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  //  revised by: Luke Smith (http://lucassmith.name)
  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (http://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56);
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ');
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '');
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.');
  //   returns 4: '67,00'
  //   example 5: number_format(1000);
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2);
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1);
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.');
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0);
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2);
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4);
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3);
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ');
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '');
  //  returns 14: '0.00000001'

  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}
</script>

<div id="mortgage_payment_calculator_main" >


<table id="tabMain" border="0">
	<tr>
		<td colspan="2" class="lbc">
			<h2><?php echo $title; ?></h2>
		</td>
	</tr>
	<tr>
		<td class="lb">
			<label><?php echo JText::_('_MOD_CALC_MOR_LOAN_AMOUNT'); ?>:</label>
		</td>
		<td>
			<input name="amount" id="amount" size="8" type=text value="" /> <?php echo $currency; ?>
		</td>
	</tr>
	<tr>
		<td class="lb">
			<label><?php echo JText::_('_MOD_CALC_MOR_LOAN_TERM'); ?>:</label>
		</td>
		<td>
			<input name="period" id="period" size="3" type="text" value="" />
		</td>
	</tr>
	<tr>
		<td class="lb">
			<label><?php echo JText::_('_MOD_CALC_MOR_AMORTIZATION'); ?>:</label>
		</td>
		<td>
			<select name="cadence" id="cadence">
				<option value=12><?php echo JText::_('_MOD_CALC_MOR_MONTHLY'); ?></option>
				<option value=6><?php echo JText::_('_MOD_CALC_MOR_TWO_MONTH'); ?></option>
				<option value=4><?php echo JText::_('_MOD_CALC_MOR_QUARTERLY'); ?></option>
				<option value=2><?php echo JText::_('_MOD_CALC_MOR_SEMI_ANNUAL'); ?></option>
				<option value=1><?php echo JText::_('_MOD_CALC_MOR_ANNUAL'); ?></option>
			</select>
		<td>
	</tr>
	<tr>
		<td class="lb">
			<label><?php echo JText::_('_MOD_CALC_MOR_INTEREST_RATE'); ?> (%):</label>
		</td>
		<td>
			<input type=text name="rate" id="rate" value="3" size="1" />
		</td>
	</tr> 
	<tr>
		<td></td>
		<td>
			<input type="submit" name="submit" class="button" value="<?php echo JText::_('_MOD_CALC_MOR_CALCULATE'); ?>" id="subm" onclick="addRow();return false;" ></input>
		</td>
	</tr>
</table>


<div id="mortgage_payment_calculator">

</div>

<br />

<div id="tab2">

</div>



</div>