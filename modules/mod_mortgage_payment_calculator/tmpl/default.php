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
<style type="text/css">

.tab_hed {
background-color:<?php echo $table_header_color;?>;
color:<?php echo $table_header_text;?>;
font-weight:bold;
}

#tabMain {
   padding-bottom: 20px; 
   border: none !important;
   border-collapse: inherit !important;
}

#tabMain td { 
    padding-bottom: 10px;
    border: none !important; /* Граница вокруг ячеек */
}

.lb {
    text-align: right;
}

.lbc {
    text-align: center;
}
#tabrez {
    border-collapse: collapse !important;
    width:100%;
    text-align:center;
}
td { 
    border: 1px solid black !important; /* Граница вокруг ячеек */
}
li {
    list-style-type:none; /* Убираем маркеры */
}
 #tab2 ul {
display:block;
list-style-type:none;
margin:0 0 1px;
padding:10px;
top:0;
position:relative;
text-align:left;
}
#mortgage_payment_calculator_main {
text-align: center;
}

</style>

<noscript>Javascript is required to use Mortgage Payment Calculator<a href="http://ordasoft.com/">Joomla component for Mortgage Payment Calculator</a>
<a href="http://ordasoft.com/">Mortgage Payment Calculator Joomla component for real estate manager and vehicle manager</a></noscript>

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
    var stab1 = "<table id=\"tabrez\"><tr><td class=\"tab_hed\"><?php echo _MOD_CALC_MOR_NUMBER; ?></td><td class=\"tab_hed\"><?php echo _MOD_CALC_MOR_MONTHLY_PAYMENTS; ?><?php echo ", " .$currency; ?></td><td class=\"tab_hed\"><?php echo _MOD_CALC_MOR_PRINCIPAL_PAID; ?><?php echo ", " .$currency; ?></td><td class=\"tab_hed\"><?php echo _MOD_CALC_MOR_INTEREST_PAID; ?><?php echo ", " .$currency; ?></td><td class=\"tab_hed\"><?php echo _MOD_CALC_MOR_BALANCE; ?><?php echo ", " .$currency; ?></td></tr>";
    
    var ir = amount*Math.pow((1+((rate/100)/cadence)),cadence*period)*(((rate/100)/cadence)/(Math.pow(1+((rate/100)/cadence),cadence*period)-1));
    var sir = ir*cadence*period;
    var tmpamount = amount;
    var balance = amount;

    for (var i = 0; i < (period*cadence); i++) {

	var qi = tmpamount*((rate/cadence)/100);
	var qc = ir - qi;
	tmpamount = tmpamount-qc;
	balance = balance - qc;

	stab1 = stab1 + "<tr><td>"+(i+1)+"</td><td>"+(ir).toFixed(2)+"</td><td>"+(qc).toFixed(2)+"</td><td>"+(qi).toFixed(2)+"</td><td>"+(balance).toFixed(2)+"</td></tr>";
    
    }
    
    mortgage_payment_calculator.innerHTML = stab1+"</table>";

    var scadence = "";

    switch (cadence) {

    case "12":
      scadence = "<?php echo _MOD_CALC_MOR_MONTHLY; ?>";
      break;
    case "6":
      scadence = "<?php echo _MOD_CALC_MOR_TWO_MONTH; ?>";
      break;
    case "4":
      scadence = "<?php echo _MOD_CALC_MOR_QUARTERLY; ?>";
      break;
    case "2":
      scadence = "<?php echo _MOD_CALC_MOR_SEMI_ANNUAL; ?>";
      break;
    case "1":
      scadence = "<?php echo _MOD_CALC_MOR_ANNUAL; ?>";
      break;
    }

    var tab2 = d.getElementById('tab2');

    var stab2 = "<ul>";
    stab2 = stab2 + "<li><b><?php echo _MOD_CALC_MOR_LOAN_AMOUNT; ?><?php echo ", " .$currency; ?>: </b>"+(parseFloat(amount)).toFixed(2)+"</li>";
    stab2 = stab2 + "<li><b><?php echo _MOD_CALC_MOR_LOAN_TERM; ?>: </b>"+period+"</li>";
    stab2 = stab2 + "<li><b><?php echo _MOD_CALC_MOR_AMORTIZATION; ?>: </b>"+scadence+"</li>";
    stab2 = stab2 + "<li><b><?php echo _MOD_CALC_MOR_INTEREST_RATE."(%)"; ?>: </b>"+rate+"</li>";
    stab2 = stab2 + "<li><b><?php echo _MOD_CALC_MOR_TOTAL_PAID; ?><?php echo ", " .$currency; ?>: </b>"+(parseFloat(sir)).toFixed(2)+"</li>";
    stab2 = stab2 + "<li><b><?php echo _MOD_CALC_MOR_INTEREST_PAID; ?><?php ?><?php echo ", " .$currency; ?>: </b>"+(parseFloat(sir - amount)).toFixed(2)+"</li>";

    tab2.innerHTML = stab2 + "</ul>";
    
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
			<label><?php echo _MOD_CALC_MOR_LOAN_AMOUNT; ?>:&nbsp;&nbsp;</label>
		</td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input name="amount" id="amount" size="8" type=text value="" /> <?php echo $currency; ?>
		</td>
	</tr>
	<tr>
		<td class="lb">
			<label><?php echo _MOD_CALC_MOR_LOAN_TERM; ?>:&nbsp;&nbsp;</label>
		</td>
		<td>
			<input name="period" id="period" size="3" type="text" value="" />
		</td>
	</tr>
	<tr>
		<td class="lb">
			<label><?php echo _MOD_CALC_MOR_AMORTIZATION; ?>:&nbsp;&nbsp;</label>
		</td>
		<td>
			<select name="cadence" id="cadence">
				<option value=12><?php echo _MOD_CALC_MOR_MONTHLY; ?></option>
				<option value=6><?php echo _MOD_CALC_MOR_TWO_MONTH; ?></option>
				<option value=4><?php echo _MOD_CALC_MOR_QUARTERLY; ?></option>
				<option value=2><?php echo _MOD_CALC_MOR_SEMI_ANNUAL; ?></option>
				<option value=1><?php echo _MOD_CALC_MOR_ANNUAL; ?></option>
			</select>
		<td>
	</tr>
	<tr>
		<td class="lb">
			<label><?php echo _MOD_CALC_MOR_INTEREST_RATE; ?> (%):&nbsp;&nbsp;</label>
		</td>
		<td>
			<input type=text name="rate" id="rate" value="5" size="1" />
		</td>
	</tr> 
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="submit" name="submit" value="<?php echo _MOD_CALC_MOR_CALCULATE; ?>" id="subm" onclick="addRow();return false;" ></input>
		</td>
	</tr>
</table>


<div id="mortgage_payment_calculator">

</div>

<br />

<div id="tab2">

</div>

<div style="text-align: center;"><a href="http://ordasoft.com" style="font-size: 10px;">Powered by OrdaSoft!</a></div>

</div>