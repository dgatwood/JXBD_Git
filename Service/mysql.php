<?php set_time_limit(0);if($_GET['s']) {highlight_file(__FILE__);die();} ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
<center>
<?php
/*
error_reporting(E_ALL ^ E_NOTICE);
$error=@mysql_connect("localhost",$_COOKIE['user'],$_COOKIE['pass']);
if (!$error||!empty($_GET)) die("User: <input type='text' id='user'><br />Pass: <input type='password' id='pass' /><br /><input type='button' onclick='document.cookie=\"pass=\"+document.getElementById(\"pass\").value;document.cookie=\"user=\"+document.getElementById(\"user\").value;document.location=\"?\"' value='Login'>");
if ($_POST['db']) mysql_select_db($_POST['db']);
$q=mysql_query("SHOW DATABASES");
echo "<form method='post'><select name='db'>";
while($f=mysql_fetch_array($q)) echo "<option value='".$f[0]."'".($f[0]==$_POST['db']?" selected='selected'":"").">".$f[0]."</option>";
echo "</select>";
?><br />
<select onchange='if (this.value) this.form.query.value=this.value' name='command'>
<?php foreach(Array("","SHOW TABLES","SHOW DATABASES","SELECT * FROM ``","SHOW CREATE TABLE ``","EXPLAIN ``","CHECK TABLE ``","OPTIMIZE TABLE ``","REPAIR TABLE ``","BACKUP TABLES") as $v) echo "<option value='$v'>$v</option>"; ?>
</select><br />
<textarea name='query' rows='10' cols='50'><?php =stripslashes($_POST['query']);?></textarea><br /><input type='submit' name='submit' value='Run Query' /></form>
<?php
if($_POST['tables']){
 $o=gzopen("backup.gz","w9");
 foreach($_POST['tables'] as $table){
  echo $table;
  $table = mysql_real_escape_string($table);
  $q=mysql_query("SHOW CREATE TABLE '$table'");
  gzwrite($o,array_pop(mysql_fetch_array($q)).";\r\n");
  $q=mysql_query("SELECT * FROM '$table'");
  while($f=mysql_fetch_assoc($q)) {
   foreach($f as $k=>$v) $f[$k]=str_replace("'","\'",$v);
   gzwrite($o,str_replace("\n","\\n","INSERT INTO '$table'(`".implode('`,`',array_keys($f))."`) VALUES('".implode("','",array_values($f))."');")."\n");
  }
 }
 gzclose($o);
 die("<a href='backup.gz'>Backup file</a> - ".filesize("backup.gz")."B");
} */
/*
if ($_POST['submit']){
 if($_POST['command']=="BACKUP TABLES"){
  if($_POST['query']!="BACKUP TABLES") {
    if($_POST['query']) {
     $queries=explode(";\r\n",$_POST['query']);
    } else {
     $o=gzopen("backup.gz","r");
     while($f=gzread($o,4096)) $queries.=$f;
     $queries=preg_split("@;\r?\n@",$queries);
    }
    //print_r($queries);
    foreach($queries as $v) {
     $q=mysql_query(str_replace("\\n","\n",$v));
     echo mysql_error()."<br />";
     $pewpew++;
    }
   die($pewpew." queries executed");
 } else {
  $q=mysql_query("SHOW TABLES");
  while($f=mysql_fetch_array($q)) $table.="<tr><td>".$f[0]."</td><td><input type='checkbox' name='tables[]' value='".$f[0]."' /></td></tr>";
  die("<form method='post'><table>$table</table><input type='hidden' name='db' value='".$_POST['db']."' /><input type='submit' value='submit' /></form>");
 }
 }
  $time=microtime(true);
  $q=mysql_query(stripslashes($_POST['query']),$error);
  $time=microtime(true)-$time;
  if (is_resource($q)){
   echo "<table border='1'>";
   while($f=mysql_fetch_assoc($q)) {
    echo "<tr>";
    if (!$test) {foreach(array_keys($f) as $v) echo "<th>$v</th>"; echo "</tr><tr>";}
    foreach($f as $v) echo "<td>".(is_null($v)?"NULL":htmlspecialchars($v))."</td>";
    echo "</tr>";
    $test=true;
   }
   if (!$test) echo "<tr><td>None</td></tr>";
   echo "</table>";
  } elseif ($q==true) echo "Query executed successfully."; else echo mysql_error($error);
  echo "<div>Query time: $time</div>";
 } */
?>
</center>
</body>
</html>
