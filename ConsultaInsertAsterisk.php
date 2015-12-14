<?php
function generaPass(){
        //Se define una cadena de caractares. Te recomiendo que uses esta.
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        //Obtenemos la longitud de la cadena de caracteres
        $longitudCadena=strlen($cadena);

        //Se define la variable que va a contener la contraseña
        $pass = "";
        //Se define la longitud de la contraseña
        $longitudPass=16;

        //Creamos la contraseña
        for($i=1 ; $i<=$longitudPass ; $i++){
                //Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
                $pos=rand(0,$longitudCadena-1);

                //Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
                $pass .= substr($cadena,$pos,1);
        }
        return $pass;
}



$NOMBRE="NOMBRE DE LA EXTENSION";
$ID="63";
$DESC="DESCRIPCION DE LA EXTENSION";

$PASS=generaPass();
$IP="IP";

PRINT "INSERT INTO sipusers(id,vpbx_id,name,ipaddr,port,regseconds,defaultuser,fullcontact,useragent,lastms,host,context,mailbox,fromdomain,fromuser,qualify,sippasswd,description) 
VALUES($ID,3,'$NOMBRE','$IP',5060,1380876926,'$NOMBRE','sip:$NOMBRE@$IP:5060','kamailio (4.0.3 (x86',1,'dynamic','from-CAMBIAR','$NOMBRE','SERVIDOR','$NOMBRE','yes','$PASS','$DESC')"

?>
