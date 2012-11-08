
<?php
header("Content-type:image/png");
imagecolorallocate($i=imagecreate(100,100),0,0,0);
for($x=1;$x<=7;$x++) imagepolygon($i,Array(rand(0,100),rand(0,100),rand(0,100),rand(0,100),rand(0,100),rand(0,100)),3,imagecolorallocate($i,($x&1)*255,($x>>1&1)*255,($x>>2&&1)*255));
imagepng($i);
?>
