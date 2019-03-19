<?php 

define( 'DS', DIRECTORY_SEPARATOR );
echo "<BR/>Path for cron job: <B>".dirname(__FILE__).DS."reminders_cron.php</B>";
echo "<BR/><BR />CPanel cron command: <B>/usr/bin/php '".dirname(__FILE__).DS."reminders_cron.php'</B>";
echo "<BR/><BR/>Note: some hosts may have different path for php, ie: something other than '/usr/bin/php'";

?>