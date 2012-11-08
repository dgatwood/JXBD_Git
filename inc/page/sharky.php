<?
$PAGE->JS("softurl");
$PAGE->JS("script",'if(!window.sharkcounter){window.sharkcounter=0;
setInterval(function(){
window.sharkcounter++;
document.body.style.background="url(\'http://jaxboards.com/junk/sharky.jpg\') "+(window.sharkcounter*10%1600)+"px 0 fixed ";
},100)}');
$PAGE->JS("playsound","bgsound","http://jaxboards.com/Sounds/jaws.mp3",true);
?>