<?php
class MySQL{
 var $queryList=Array();
 var $queryRuntime=Array();
 var $connected=false;
 var $engine="MySQL";

 function connect($a,$b,$c,$d='',$e=''){
  $r=mysql_connect($a,$b,$c);
  $this->prefix=$e;
  if ($r&&$d) $this->select_db($d);
  return $this->connected=$r;
 }
 
 function debug_mode(){
  $this->debugMode=true;
 }
 function nolog(){
  $this->nolog=true;
 }
 
 function prefix($a){
  $this->prefix=$a;
 }

 function ftable($a){return '`'.$this->prefix.$a.'`';}

 function error(){return mysql_error();}
 function affected_rows(){return mysql_affected_rows();}

 function select_db($a){
  if (mysql_select_db($a)) $this->db=$a;
  return $this->db;
 }

 function select($a,$b,$c='',$over=1){
  return $this->query('SELECT '.$a.' FROM '.$this->ftable($b).($c?' '.$c:''),$over);
 }

 function insert_id(){
  return mysql_insert_id();
 }

 function insert($a,$b){
  $b=$this->buildInsert($b);
  return $this->query('INSERT INTO '.$this->ftable($a).'('.$b[0].') VALUES'.$b[1]);
 }

 function buildInsert($a){
  $r=Array(Array(),Array(Array()));
  if(!isset($a[0])||!is_array($a[0])) $a=Array($a);
  
  foreach($a as $k=>$v) {
   ksort($v);
   foreach($v as $k2=>$v2) {
    if(mb_check_encoding($v2)!="UTF-8") $v2=utf8_encode($v2);
    if($k==0) $r[0][]=$this->ekey($k2);
    $r[1][$k][]=$this->evalue($v2);
   }
  }

  $r[0]=implode(',',$r[0]);
  foreach($r[1] as $k=>$v) $r[1][$k]=implode(',',$v);
  $r[1]='('.implode('),(',$r[1]).')';
  return $r;
 }

 function update($a,$b,$c=''){
  return $this->query('UPDATE '.$this->ftable($a).' SET '.$this->buildUpdate($b).($c?' '.$c:''));
 }

 function buildUpdate($a){
  $r='';
  foreach($a as $k=>$v) $r.=$this->eKey($k).'='.$this->evalue($v).',';
  return substr($r,0,-1);
 }

 function delete($a,$b){
  return $this->query("DELETE FROM ".$this->ftable($a).($b?" $b":''));
 }

 function row($a=null){
  global $PAGE;
  $a=$a?$a:$this->lastQuery;
  return $a?mysql_fetch_array($a):false;
 }

 function arow($a=null) {
  global $PAGE;
  $a=$a?$a:$this->lastQuery;
  if($a) {
   $q=@mysql_fetch_assoc($a);
  } else $q=false;
  return $q;
 }
 
 function num_rows($a=null){
  $a=$a?$a:$this->lastQuery;
  return mysql_num_rows($a);
 }

 function query($a,$over=1){
  global $USER;
  if($this->debugMode) return print($a."<br />");
  if(!$this->connected) return false;
  if(!$this->nolog) {
   $this->queryList[]=$a;
   $time=microtime(true);
  }
  $query=mysql_query($a);
  if(!$this->nolog) $this->queryRuntime[]=round(microtime(true)-$time,5);
  if($over) $this->lastQuery=$query;
  return $query;
 }

 function ekey($key){
  return '`'.$this->escape($key).'`';
 }

 function evalue($value,$forsprintf=0){
  if(is_array($value)) $value=$value[0];
  elseif(is_null($value)) $value='NULL';
  else {
   $value=is_integer($value)?$value:'\''.$this->escape(($forsprintf?str_replace("%","%%",$value):$value)).'\'';
  }
  return $value;
 }

 function escape($a){
  return function_exists("mysql_real_escape_string")&&$this->connected?mysql_real_escape_string($a):addslashes($a);
 }

 function special(){
  $a=func_get_args();
  $b=array_shift($a);
  return $this->query(vsprintf(str_replace("%t",$this->ftable("%s"),$b),$a));
 }

 function getUsersOnline(){
  global $CFG,$USER,$SESS;
  $idletimeout=time()-$CFG['timetoidle'];
  $r=Array('guestcount'=>0);
  if(!$this->usersOnlineCache) {
  $this->special("SELECT a.id,a.uid,a.location,a.location_verbose,a.hide,a.is_bot,b.display_name AS name,b.group_id,concat(b.dob_month,' ',b.dob_day) `dob`,a.last_action,a.last_update FROM %t AS a
LEFT JOIN %t AS b ON a.uid=b.id
WHERE last_update>=".(time()-$CFG['timetologout'])." ORDER BY last_action DESC","session","members","member_groups");
  $today=date('n j');
  while($f=$this->arow()) {
   if($f['hide']) {if($USER['group_id']!=2) continue; else $f['name']='* '.$f['name'];}
   $f['birthday']=($f['dob']==$today?1:0);
   $f['status']=($f['last_action']<$idletimeout?"idle":"active");
   if($f['is_bot']) {$f['name']=$f['id'];$f['uid']=$f['id'];}
   unset($f['id']);unset($f['dob']);
   if($f['uid']) {
    if(!$r[$f['uid']]) $r[$f['uid']]=$f;
   } else $r['guestcount']++;
   
  }

  /*since we update the session data at the END of the page, we'll want to include
    the user in the usersonline */
  if($USER&&$r[$USER['id']]){
   $r[$USER['id']]=Array(
    "uid"=>$USER['id'],
    "group_id"=>$USER['group_id'],
    "last_action"=>$SESS->last_action,
    "last_update"=>$SESS->last_update,
    "name"=>($SESS->hide?'* ':'').$USER['display_name'],
    "status"=>($SESS->last_action<$idletimeout?"idle":"active"),
    "birthday"=>$USER['birthday'],
    "location"=>$SESS->location,
    "location_verbose"=>$SESS->location_verbose
   );
  }
  $this->usersOnlineCache=$r;
  }
  return $this->usersOnlineCache;
 }
 
 function fixForumLastPost($fid){
  global $PAGE;
  $this->select("lp_uid,lp_date,id,title","topics","WHERE fid=$fid ORDER BY lp_date DESC LIMIT 1");
  $d=$this->row();
  $this->update("forums",Array("lp_uid"=>$d['lp_uid'],"lp_date"=>$d['lp_date'],"lp_tid"=>$d['id'],"lp_topic"=>$d['title']),"WHERE id=".$fid);
 }
 
 function fixAllForumLastPosts(){
 	$query=$this->select("id","forums");
 	while($fid=$this->row($query)) {
 		$this->fixForumLastPost($fid);
 	}
 }
 
 function getRatingNiblets(){
  if($this->ratingNiblets) return $this->ratingNiblets;
  $this->select("*","ratingniblets");
  $r=Array();
  while($f=$this->row()) $r[$f['id']]=Array('img'=>$f['img'],'title'=>$f['title']);
  return $this->ratingNiblets=$r;
 }
 
 function debug(){
  return '<div>'.implode("<br />",$this->queryList).'</div>';
  $this->queryList=Array();
 }
}
?>