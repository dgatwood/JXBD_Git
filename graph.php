<html>
<head>
</head>
<body>
<canvas id="out" height="500px" width="500px" style='border:1px solid #000' onmousemove="getcoordinates(event)">
</canvas>
<form>
<div id="coords"></div>
<br />
X-min: <input type="text" name="xmin" value="-10"/><br />
X-max: <input type="text" name="xmax" value="10"/><br />
Y-min: <input type="text" name="ymin" value="-10"/><br />
Y-max: <input type="text" name="ymax" value="10"/><br />
<input type="button" onclick="drawaxis(parseFloat(this.form.xmin.value),parseFloat(this.form.xmax.value),parseFloat(this.form.ymin.value),parseFloat(this.form.ymax.value))" value="Set Window" />
<br />
<input type="text" id="func" />
<input type="button" onclick="plot(function(x){return eval(document.getElementById('func').value);})" value="Plot" />
</form>
<script type='text/javascript'>
Math.oldsqrt=Math.sqrt
Math.sqrt=function(a){
 if(a<0) {
  a*=-1
  setColor('#ff0000')
 } else {
  setColor('#0000ff')
 }
 return Math.oldsqrt(a)
}

var xmin=-10,ymin=-10,xmax=10,ymax=10,f=false
function drawaxis(xmin2,xmax2,ymin2,ymax2){

 if(xmin2!=undefined) {
  xmin=xmin2
  xmax=xmax2
  ymin=ymin2
  ymax=ymax2
 }

 var c=document.getElementById('out'),ctx=c.getContext('2d'),h=parseFloat(c.height),w=parseFloat(c.width),
 yratio=h/(ymax-ymin),xratio=w/(xmax-xmin),
 yaxis=-1*(0-ymin)*yratio+h,
 xaxis=(0-xmin)*xratio,
 i=0,tmp

 //clear everything
 ctx.clearRect(0,0,w,h)

 ctx.beginPath()
 ctx.strokeStyle="#CCC"

 //draw x-axis
 ctx.moveTo(0,yaxis)
 ctx.lineTo(w,yaxis)

 //draw ticks on x-axis
 for(i=xmin;i<xmax;i++) {
  tmp=(i-xmin)*xratio
  ctx.moveTo(tmp,yaxis-5)
  ctx.lineTo(tmp,yaxis+5)
 }

 //draw y-axis
 ctx.moveTo(xaxis,0)
 ctx.lineTo(xaxis,h)

 //draw ticks on y-axis
 for(i=ymin;i<ymax;i++){
  tmp=(i-ymin)*yratio
  ctx.moveTo(xaxis-5,tmp)
  ctx.lineTo(xaxis+5,tmp)
 }
 
 ctx.stroke()
 ctx.closePath()
}
function plot(func){
 f=func
 var c=document.getElementById('out'),w=parseFloat(c.width),h=parseFloat(c.height),ctx=c.getContext('2d'),
 xinc=(xmax-xmin)/w,xratio=w/(xmax-xmin),yratio=h/(ymax-ymin),inc=0,fx=0
 ctx.moved=false
 ctx.strokeStyle='#0000ff'
 ctx.graphing=true
 for(x=xmin,inc=0;inc<w;x+=xinc,inc++) {
  fx=f(x)
  if(!ctx.moved) {
   ctx.beginPath();
   if(!isNaN(fx)) {
    ctx.moveTo(inc,-1*(f(x)-ymin)*yratio+h)
    ctx.moved=true;
   }
  } else {
   if(!isNaN(fx)) ctx.lineTo(inc,-1*(fx-ymin)*yratio+h)
  }
 }
 ctx.stroke()
 ctx.graphing=false
}
function getcoordinates(e){
 var c=document.getElementById('out'),
 coords=document.getElementById('coords'),
 xpos=e.clientX-c.offsetLeft,
 ypos=e.clientY-c.offsetTop,
 x=(xpos/c.width)*(xmax-xmin)+xmin,
 y=-1*(ypos/c.height)*(ymax-ymin)-ymin
 coords.innerHTML="("+x.toFixed(2)+","+y.toFixed(2)+")"+(f?" &nbsp; &nbsp; &nbsp; f(x)="+f(x).toFixed(2):"")
}
function setColor(a){
 var ctx=document.getElementById('out').getContext('2d')
 if(!ctx.graphing||ctx.strokeStyle==a) return
 if(ctx.moved) ctx.stroke()
 ctx.strokeStyle=a
 ctx.moved=false
}
drawaxis()
</script>
</body>
</html>