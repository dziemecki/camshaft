<?PHP
/*
  Writes a time-stamped message to a log
*/
function write_log ($s_msg, $s_alt_loc = NULL)
{
  $time_stamp = date("Ymd H:i:s");
  if (!isset($s_alt_loc)){$error_file = _LOG_LOC;}else{$error_file = $s_alt_loc;}
  if (strlen(trim($error_file)) == 0) {$error_file = getcwd(). "/err.log";}
  $error_msg = $time_stamp . " " . $s_msg . chr(13) . chr(10);
        if(!file_exists(dirname($error_file))) 
          mkdir(dirname($error_file));
        $fp = fopen($error_file, 'a+');
        if(!$fp) {
                trigger_error('file_put_contents cannot write in file.', E_USER_ERROR);
            return;
         }
         fputs($fp, $error_msg);
         fclose($fp);
  $s_alt_loc = NULL; 
}
?>