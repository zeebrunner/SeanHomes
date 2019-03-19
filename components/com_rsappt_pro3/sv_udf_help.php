<?php
					echo $udf_help_icon." id='opener".$i."' title='".JText::_('RS1_INPUT_SCRN_CLICK_FOR_HELP')."'>";		
					echo "<div id=\"udf_help".$i."\" title=\"".JText::_(stripslashes($udf_row->udf_label))."\">".JText::_(stripslashes($udf_row->udf_help))."</div>";	
						echo "<script>";
						echo "jQuery( \"#udf_help".$i."\" ).dialog({ autoOpen: false, ";
						//echo "  closeText:\"your close text\",";						
						echo "  position:{";
						echo "    my: \"left+10 bottom+5\",";
  						echo "    of: \"#opener".$i."\",";
						echo "    collision: \"fit\"";
						echo "  }";
						echo "});";
						
						echo "jQuery( \"#opener".$i."\" ).click(function() { ";
					  	echo "   jQuery( \"#udf_help".$i."\" ).dialog( \"open\" );";
						if($udf_row->udf_help_format == "Link"){					
							echo "jQuery( \"#udf_help".$i."\" ).load(\"".JText::_(stripslashes($udf_row->udf_help))."\", function() {});";
						}
						echo "});";

						echo "</script>";
?>