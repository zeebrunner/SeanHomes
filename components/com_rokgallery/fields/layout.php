<?php
 /**
  * @version   $Id: layout.php 27022 2015-02-25 17:35:57Z matias $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldLayout extends JFormField
{
	protected  $type = 'Layout';
	protected static $js_loaded = false;

	protected function getInput()
	{
        $doc = JFactory::getDocument();
        $version = new JVersion();

        JHtml::_('behavior.framework', true);

        if (version_compare($version->getShortVersion(), '3.0', '<')) {
            $js = "window.addEvent('domready', function() {
                var str = '".$this->value."';
                $('".$this->id."').addEvent('change', function(){
                    var sel = document.id(this.options[this.selectedIndex]).get('value'),
                        rel = document.id(this.options[this.selectedIndex]).get('rel');
                    RokGalleryFixed = rel == 'false' ? false : true;
                    $$('.layout').getParent('li').setStyle('display','none');
                    $$('.'+sel).getParent('li').setStyle('display','block');
                }).fireEvent('change');
            });";

        } else {

            $js = "
            window.addEvent('load', function() {
            var chzn = $('" . $this->id . "_chzn');
                if(chzn!=null){
                    chzn.addEvent('click', function(){
                        $$('." . $this->element['name'] . "').getParent('div.control-group').setStyle('display','none');
                        var text = $('" . $this->id . "_chzn').getElement('span').get('text');
                        var options = $('" . $this->id . "').getElements('option');
                        options.each(function(option) {
                        var optText = String(option.get('text'));
                        var optValue = String(option.get('value'));
                            if(text == optText){
                                var sel = optValue;
                            }
                            $$('.'+sel).getParent('div.control-group').setStyle('display','block');
                        });
                    }).fireEvent('click');
                }
            });";

        }

		$doc->addScriptDeclaration($js);
		$list = $options = '';
		foreach($this->element->children() as $opt){
			$options .= '<option value="'.$opt['value'].'" class="'.(string)$this->element['class'].'" rel="'.(string)$opt['fixed'].'"'.(($this->value == $opt['value']) ? $selected = ' selected="selected"':$selected = "").'>'.JText::_($opt).'</option>';
		}
		$list = '<select id="'.$this->id.'" class="inputbox" name="'.$this->name.'">'.$options.'</select>';

		return $list;

	}

}
