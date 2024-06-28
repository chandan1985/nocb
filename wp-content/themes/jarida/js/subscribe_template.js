function dets1(){
if(document.getElementById("sub_c_1").classList.contains("open") == false){
	document.getElementById("sub_c_1").classList.add("open");
}else{
	document.getElementById("sub_c_1").classList.remove("open");
}
}
function dets2(){
if(document.getElementById("sub_c_2").classList.contains("open") == false){
	document.getElementById("sub_c_2").classList.add("open");
}else{
	document.getElementById("sub_c_2").classList.remove("open");
}
}
function dets3(){
if(document.getElementById("sub_c_3").classList.contains("open") == false){
	document.getElementById("sub_c_3").classList.add("open");
}else{
	document.getElementById("sub_c_3").classList.remove("open");
}
}
window.onload = function() {
document.getElementById("dets1").onclick = dets1;
document.getElementById("dets2").onclick = dets2;
document.getElementById("dets3").onclick = dets3;
}