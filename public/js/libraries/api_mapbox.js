const fecha = new Date();
const hoy_map_box = fecha.getDate();
const API_MAPBOX_PAR='pk.eyJ1Ijoic2FyYWkxIiwiYSI6ImNsNGlrZHloZTAwdnkzcHFpdWs0OGJhZGIifQ.iw9QhsRS2EYcYif8dvFhdQ';
const API_MAPBOX_IMPAR='pk.eyJ1Ijoic2FyYWkyIiwiYSI6ImNsNTZ5dXkyNDEyMTIzZ2w4MGFxbHA2OW4ifQ.nq8ybyK0Oo99sANkkzypAg';
var API_MAPBOX="";
if(hoy_map_box%2===0){
    var API_MAPBOX=API_MAPBOX_PAR;
}
else{
    var API_MAPBOX=API_MAPBOX_IMPAR;
}