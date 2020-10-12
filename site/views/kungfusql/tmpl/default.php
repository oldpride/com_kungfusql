<p>Query</p>
<form method="get">
<button type="submit" name="display" value="result">Display on screen</button>
<button type="submit" name="export" value="result">Export to CSV</button>
</form>

<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_kungfusql
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<?php

$msg = $this->msg;

$id             = $msg[0];
$filename       = $msg[1];

$params         = array();
$params[0]      = $msg[2];
$params[1]      = $msg[3];
$params[2]      = $msg[4];
$params[3]      = $msg[5];
$params[4]      = $msg[6];
$queryAgainTime = $msg[7];
$debug          = $msg[8];

if ($debug) {
   $json_string = json_encode($msg);
   echo "<pre>{$json_string}</pre>"; 
   // $msg is an array
   // [2,"poll_answer_all_without_link.sql","2",null,null]

   echo "<p></p>"; 
}


// https://docs.joomla.org/Accessing_the_current_user_object
use Joomla\CMS\Factory;
$user = Factory::getUser();
if ($debug) {
   echo "<pre>user->id={$user->id}</pre>"; 
   echo "<p></p>"; 
}

$path = JPATH_ADMINISTRATOR . "/uploads/com_kungfusql/$filename";
if ($debug) {
   echo "<pre>$path</pre>"; 
   echo "<p></p>"; 
}

// https://www.php.net/manual/en/function.file-get-contents.php
$sql = file_get_contents($path);
if ($debug) {
   echo "<pre>original: $sql</pre>"; 
}

$sql = str_replace('$user->id', $user->id, $sql);

for ($i=0; $i<5; $i++) {
   if (! is_null($params[$i])) {
      $form_i = $i + 1;

      // preg_replace(), regex replace,  https://www.php.net/manual/en/function.preg-replace.php
      // str_replace(), string replace: https://www.php.net/manual/en/function.str-replace.php
      $sql = str_replace("kungfusql_param$form_i", $params[$i], $sql);
   } 
}

if ($debug) {
   echo "<pre>replaced: $sql</pre>"; 
   echo "<p></p>"; 
}

// we can print the form from here
// echo '
// <p>Query my votes</p>
// <form method="get">
// <button type="submit" name="display" value="poll">Display on screen</button>
// <button type="submit" name="export" value="poll">Export to CSV</button>
// </form>
// ';
 
# joomla php does not allow two functions in articles of the same category share the same name.
# therefore, the following function names are prefixed with kungfusql
function kungfusql_display_data($rows) {
   ## If count rows is nothing show o records.
   if (count( $rows ) == 0) {
      echo "\n(0) Records Found!\n";
      return;
   }

   $output = '<table border="1">';
   $count = 1;
   foreach($rows as $key => $var) {
      if ($count == 1) {
         $output .= '<tr>';
         foreach($var as $k => $v) {
            $output .= '<td><strong>' . $k . '</strong></td>';
         }
      }
      $output .= '<tr>';
      foreach($var as $k => $v) {
         $output .= '<td>' . $v . '</td>';
      }
      $count += 1;
   }
   $output .= '</table>';
   echo $output;
}

function kungfusql_export_to_csv($rows, $filename) {
   ## If count rows is nothing show o records.
   if (count( $rows ) == 0) {
      echo "\n(0) Records Found!\n";
      return;
   }
   // https://forum.joomla.org/viewtopic.php?t=411903
   // Empty data vars
   $data = "" ;

   // separator
   $sep = ",";

   $fields = (array_keys($rows[0]));

   // Count all fields(will be the collumns
   $columns = count($fields);

   // Put the name of all fields to $out.
   $line = '';
   for ($i = 0; $i < $columns; $i++) {
      $line .= $fields[$i].$sep;
   }

   $data .= trim($line)."\n";

   ## Counting rows and push them into a for loop
   for($k=0; $k < count( $rows ); $k++) {
      $row = $rows[$k];
      $line = '';

      ## Now replace several things for MS Excel
      foreach ($row as $value) {
         $value = str_replace('"', '""', $value);
         $line .= '"' . $value . '"' . $sep;
      }
      $data .= trim($line)."\n";
   }
   $data = str_replace("\r","",$data);

   ob_end_clean(); // Clean (erase) the output buffer and turn off output buffering
   // otherwise, other HTML code would get into the export 

   ## Push the report now!
   header("Content-type: text/csv; charset=utf-8");
   header("Content-Disposition: attachment; filename=$filename");
   header("Pragma: no-cache");
   header("Expires: 0");
   //header("Location: excel.htm?id=yes");
   print $data ;
   die();
}

if (!empty($_GET["display"]) or !empty($_GET["export"])) {
   // throttle user query frequency
   // https://stackoverflow.com/questions/1242176/how-do-i-limit-form-submission-rates-and-inputs-in-php
   $now = time();
   
   if ($debug) {
      $json_string = json_encode($_SESSION);
      echo "<pre>_SESSION = $json_string</pre>"; 
      echo "<p></p>"; 
      echo "<pre>now = $now</pre>"; 
      echo "<p></p>"; 
   }
   
   if (!isset($_SESSION['last_submit'])) {
       $_SESSION['last_submit'] = $now;
   } elseif ($now-$_SESSION['last_submit'] < $queryAgainTime) {
      echo "<p></p>"; 
      echo "<pre>Cannot query again within $queryAgainTime seconds from last query</pre>";
      echo "<p></p>"; 
      return; // don't use die() as it will stop downstream functions too and make the page ugly.
   } else {
       $_SESSION['last_submit'] = $now;
   }

   $database = JFactory::getDbo();
   $database->setQuery($sql);
   $rows = $database->loadAssocList();

   if (!empty($_GET["display"])) { 
      kungfusql_display_data($rows);
   } else {
      # !empty($_GET["export"])
      $export_csv = $_GET["export"] . ".csv";
      kungfusql_export_to_csv($rows, $export_csv);
   }
}


?>
