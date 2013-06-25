<?php
 $PAGE->JS("playsound","bgsound",SOUNDSURL."Buckwheat Boyz - Peanut Butter Jelly Time.mp3",true);
 $PAGE->JS("script","document.body.style.backgroundImage='url(misc/peanut-butter-jelly-time.gif)';document.body.style.backgroundColor='#000';
 window.pbjt=0
 setInterval(function(){
 window.pbjt++
 document.body.style.backgroundPosition=window.pbjt+'px '+window.pbjt+'px';
 },100)");
 $PAGE->JS("softurl");
?>
