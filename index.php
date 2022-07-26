<?php
$title ="Probando Matemáticas";
$description ="Probando Matemáticas";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once "includes/headerComunes.php"; ?>

<style>
    .operacion{
        font-size: 75px;
        font-weight: 200;
        width: 100%;
        position: relative;
        left: 0;

    }
    .conjunto_res{
        font-size: 55px;
        font-weight: 700;
        text-align: center;
    }
    .conjunto_res{
        display: none;
    }
    .userResponse{
        text-align: center;
        width: 50%;
        border: 4px solid orange;
        border-radius: 10px 0px 0px 10px;
    }
    .mensaje{
        margin-top: 100px;
    }
   .validar{
        position: relative;
        top:-7px;
        padding-right: 25px;
   }
    .validar, .userResponse{
        padding-top: 10px;
        padding-bottom: 10px;
        height: 75px;
        font-size: 30px;
    }
    .cronometer{
        text-align: center;
        font-size: 75px;
        color: red;
        border: 5px solid black;
        border-radius: 20px;
        width: 200px;
        height: 120px;
        position: relative;
        z-index: 2;
        left: 50px;
        background-color: white;
        

    }
    #myProgress{
        height: 50px;
        width: 80%;
        border: 5px solid black;
        overflow:hidden;
        left: 10%;
        position: absolute;
        top: 50px;
        z-index: 1;

    }
    #myBar{
        width: 100%;
    }
    .contadorConjunto{
        position: relative;
        margin-top: 50px;
    }

    @media(max-width: 757px){
        .operacion{
                font-size: 40px;
        }
        .cronometer {
            font-size: 45px;
            width: 100px;
            height: 80px;
           
        }
    }

</style>

</head>
<body>
    <div class="contadorConjunto"> <!-- CRONOMETRO Y BARRA REGRESIVA -->
        <div class="cronometer"></div>
        <div id="myProgress">
                    <div id="myBar"></div>
        </div>
    </div>

    <div class="container">
            <div class="text-center">
                    <div class="mensaje">
                    </div>
                    <div class="operacion">
                    </div>
                    <div class="conjunto_res text-center">
                        <span>Resultado: </span><span class="resultado"></span>
                    </div>
                    <input type="text" class="userResponse mt-5" ><button class="btn btn-success validar">Validar !</button>
            </div>
    </div>

<script>
$(document).ready(function(){
 //1. Definir cantidad de conjuntos globales

        //NOTA: Los atributos del objeto de abajo son variables acorde o cambian conforme a los niveles que va avanzando

        // NOTA: Ejecutar paréntesis primero, exponentes, multiplicación, división

        // NOTA:  Abajo definimos que operaciones disponibles hay, la idea es que sea todo relacionado al cálculo de las operaciones



        var screenOperations = {
                operacionesDisponibles: ["+","-","*","/"], // Hacemo una funcion que arroje un número del 0 - 3
                operacionesGlobales: [], // Se asginan de manera automática en la función procesarArrGral
                qtyCGs: 0,
                qtyOpCGs: 0, //Depende de qtyCGs, el número de operaciones que va a ser para los conjuntos globales
                rangoMinValSubs: 0, // Estos son los valores que puede tener cada conjunto
                rangoMaxValSubs: 0, // Estos son los valores que puede tener cada conjunto 
                numMinDigitos: 0, //Cero comienza en 1
                numMaxDigitos:0,  //Número de digitos que puede tener en un subconjunto
                allowDecimales: 0, // 0 y 1
                arregloGeneral: [],
                deshabilitarOpsCG: [], //Deshabilitar así ["suma"] , ["multiplicacion","division"]
                resultado: 0,
                getQtyOpCgs: function(){ //SE TIENE QUE EJECUTAR DESPUÉS DE ASIGNAR ---> qtyCGs
                    var res = this.qtyCGs-1; // nota: la cuestión si tenemos 3 conjuntos en realidad el procedimiento sería seguir orden de operaciones (2 operaciones)
                    return res;
                },
                randNumWithDecimals: function(num){ //Agregamos a un número decimales, esta función la ocupamos cuando this.allowDecimals está activa ,o sea, 1
                        var decimals = getRandomInt(0, 99); // Siempre será este rango dado que son decimales
                        var sum = num;
                        if(decimals===0){
                            return num;
                        }else{
                            sum+=decimals/100;
                            return sum;
                        }
                },
                getRandomInt: function(min, max) {
                    min = Math.ceil(min);
                    max = Math.floor(max);
                    return Math.floor(Math.random() * (max - min + 1)) + min;
                },
                displayNumbers: function(maxDigits){ //Máximo número que cumple con el máximo número de digitos
                    var max; // El máximo valor que podemos obtener dentro de una condición de máximo de digitos
                    if(maxDigits==0){
                        max = 1;
                    }else{
                        max = (Math.pow(10, maxDigits))-1; 
                    }
                    return max;
                },
                getRandomOperations: function(arrOpsDisponibles,numOperaciones){ // input: array, int;  | Esta funcion retorna un arreglo de STRINGS de operaciones
                    // Esta función se va a ocupar en la función de iterar el arreglo general
                    var count = arrOpsDisponibles.length;
                    var setOfOps = [];
                    for(var i=0; i<numOperaciones; i++){
                        var opIndex = this.getRandomInt(0, count-1); 
                        //setOfOps[i] = screenOperations.operacionesDisponibles[opIndex]; //Si queremos regresar el nombre de la operacion
                        setOfOps[i] = arrOpsDisponibles[opIndex];
                    }
                    return setOfOps;
                },
                generador: function(qtyCGlobales,minSubs, maxSubs,minDigitos,maxDigitos,opsAllowed=["+","-"]){ //Esta función regresará conjuntos globales y subconjuntos ya con valores

                        this.operacionesDisponibles = opsAllowed;
                        //Primer ciclo iterar con qtyCGlobales
                        var multiArr = []; // Se convertirá en algo así [[123,56,77],[123,99,21,15]...] el primer arreglo correponde al conjunto #1, y así sucesivamente
                       
                        for(var i=0; i<qtyCGlobales; i++){
                            //Obtener número de subconjuntos para este conjunto global
                            multiArr[i] = [];
                            var numSubs = this.getRandomInt(minSubs, maxSubs); 
                            for(var h=0; h<numSubs; h++){
                                    var randNumber = this.getRandomInt(this.displayNumbers(minDigitos), this.displayNumbers(maxDigitos)); 
                                    multiArr[i][h] = randNumber;
                            }
                        }
                        return multiArr;
                },
                procesarArregloGral: function(arrGral){
                        var conjuntos = arrGral.length;
                        var ope = "";
                        var operaciones = screenOperations.getRandomOperations(screenOperations.operacionesDisponibles,(conjuntos)-1); // ["+","+","*"] random
                        this.operacionesGlobales = operaciones;
                        //Evaluamos el primer conjunto
                        for(var x=0; x<conjuntos; x++){
                                    //Necesitamos iterar los números del primer conjunto
                                    ope+="(";
                                    
                                    var qtyNums = arrGral[x].length;
                                    qtyOps = qtyNums-1;
                                    var ops = this.getRandomOperations(this.operacionesDisponibles,qtyOps);
                                    for(var y=0; y<qtyNums;y++){
                                            ope+=arrGral[x][y];
                                            if(y!=(qtyNums-1)){
                                                ope+=ops[y];
                                            }
                                    }
                                    ope+=")"+((conjuntos!=x+1)?operaciones[x]:"");
                        }
                        return ope;

                },
                doWith: function(qtyCGs,rangMinSubs,rangMaxSubs,minDigitosSubs,maxDigitosSubs,allowDecs=0){ //Llamamos esta función al inicio para asignar valores del objeto
                        //LA ÚNICA FUNCIÓN QUE LLAMAREMOS
                        this.qtyCGs = qtyCGs;
                        this.qtyOpCGs = this.getQtyOpCgs(this.qtyCGs);
                        this.rangoMinValSubs = rangMinSubs;
                        this.rangoMaxValSubs = rangMaxSubs;
                        this.numMinDigitos = minDigitosSubs;
                        this.numMaxDigitos = maxDigitosSubs;
                        this.allowDecimales = allowDecs;
                        this.arregloGeneral = this.generador(this.qtyCGs,this.rangoMinValSubs,this.rangoMaxValSubs,this.numMinDigitos,this.numMaxDigitos,1);

                }
        }
        
         //OBJETIVO DE FUNCIÓN: Checamos de cuátos dígitos deben de ser los conjuntos o subconjuntos y con esta función regresamos el número máximo

        function displayNumbers(maxDigits){ //Máximo número que cumple con el máximo número de digitos
            var max; // El máximo valor que podemos obtener dentro de una condición de máximo de digitos
            if(maxDigits==0){
                max = 1;
            }else{
                max = (Math.pow(10, maxDigits))-1; 
            }
           
            return max;
        }

        //2. Dependiendo de la cantidad de conjuntos tomamos la cantidad de operaciones
        //3. Entonces podemos hacer una función que retorne NÚMEROS ENTEROS en un rango de operaciones disponibles ["suma","resta"...] equivalente a la cantidad 
        function getRandomOperations(arrOpsDisponibles,numOperaciones){ // input: array, int;  | Esta funcion retorna un arreglo de STRINGS de operaciones
            var count = arrOpsDisponibles.length;
            var setOfOps = [];
            for(var i=0; i<numOperaciones.length; i++){
                var opIndex = getRandomInt(0, count-1); 
                setOfOps[i] = screenOperations.operacionesDisponibles[opIndex];
            }
            return setOfOps;
        }


        /* FUNCIONES DE PATRONES */
            //Encontrar conjuntos con parentesis
                //1. Primero tenemos que encontrar los números dentro los paréntesis
                //2. Encontrar los símbolos de operacion de los conjuntos globales (este ahorita es de un sólo conjunto)
                //2.1 Aplicamos la función de limpieza al arreglo de símbolos 
                //3. Obtener todos los números enteros, decimales dentro del parentesis
                //4. Obtener todos los símbolos dentro del conjunto, utilizamos el paso 1.
            function findConjuntos(operacion){ //Input: El string de operaciones
                    var pattern = /\(([^)]+)\)/g; 
                    var res =   operacion.match(pattern);
                    return res; // ["(123+123)","(123-123)","(123*123)"]
            }
            function findOpesConjuntos(operacion){   //Input: El string de operaciones
                    var pattern =   /\)([\+\-\*\/])\(/g;
                    var res =   operacion.match(pattern);
                    return res; // ["+","+","-"]
            }
            function findNumbers(arregloSubConjunto){ // (2+3+5-6)
                    var pattern = /([0-9]*[.])?\d+/g; ///\d([.]\d)/g; // Obtenemos los números
                    var res =   arregloSubConjunto.match(pattern);
                    return res;
            }
            function findSymbolsSubs(arregloSubConjunto){ // (2+3+5-6)
                    var pattern = /([\+\-\*\/])/g;
                    var res = arregloSubConjunto.match(pattern);
                    return res;
            }

        /* FUNCIONES DE PATRONES */
        /* FUNCIONES DE OPERACIONES (funciones recursivas)*/
            //función con base a prioridades
            var arrNums = [];
            var arrOps = [];
            var sumatoria = 0;

            function setOutsideVars(nums,ops,sum){
                arrNums = nums;
                arrOps = ops;
                sumatoria = sum;
            }

            function getOutsideVars(){
                return [arrNums,arrOps,sumatoria];
            }




            function multiply(arrNums,arrOpsSyms,period,resultadoAcu){ //opeString puede ser "*","+","-","/" //periodo es el index en el que va

                    // El periodo tiene que iniciar en cero
                    //Resultado acumulado 
                    var indexArrOps = period; //tenemos que sumar al periodo ++ para seguir avanzando en el arreglo
                    var sumatoria = resultadoAcu; 
                    
                    //Llamamos la misma función si el opString es "*"
                    // if(arrOpsSyms[indexArrOps]==="/*"){
                    
                    var nNums = arrNums;
                    var nOps = arrOpsSyms;
                       
                    // }
                   
                    if(arrOpsSyms[indexArrOps]=="*"){
                     
                        sumatoria = parseFloat(arrNums[indexArrOps])*parseFloat(arrNums[indexArrOps+1]);
                        nNums.splice(indexArrOps+1,1);
                        nNums.splice(indexArrOps,1,sumatoria);
                        nOps.splice(indexArrOps,1);
                        indexArrOps--;
                    }
                    
                    if(arrOpsSyms[indexArrOps+1]===undefined || arrOpsSyms[indexArrOps+1]==null){
                        var arr = [nNums,nOps,sumatoria];
                        setOutsideVars(nNums,nOps,sumatoria);
                        return arr;
                    }
             
                    indexArrOps++;
                    multiply(nNums,nOps,indexArrOps,sumatoria);
                    
            }


            function division(arrNums,arrOpsSyms,period,resultadoAcu){ //opeString puede ser "*","+","-","/" //periodo es el index en el que va
                    if(arrNums.length==1){
                        return;
                    }
                    // El periodo tiene que iniciar en cero
                    //Resultado acumulado 
                    var indexArrOps = period; //tenemos que sumar al periodo ++ para seguir avanzando en el arreglo
                    var sumatoria = resultadoAcu; 
                    
                    //Llamamos la misma función si el opString es "*"
                    // if(arrOpsSyms[indexArrOps]==="/*"){
                    
                    var nNums = arrNums;
                    var nOps = arrOpsSyms;
                       
                    // }
                   
                    if(arrOpsSyms[indexArrOps]=="/"){
                        sumatoria = parseFloat(arrNums[indexArrOps])/parseFloat(arrNums[indexArrOps+1]);
                        nNums.splice(indexArrOps+1,1);
                        nNums.splice(indexArrOps,1,sumatoria);
                        nOps.splice(indexArrOps,1);
                        indexArrOps--;
                    }
                    
                    if(arrOpsSyms[indexArrOps+1]===undefined || arrOpsSyms[indexArrOps+1]==null){
                        
                        var arr = [nNums,nOps,sumatoria];
                        setOutsideVars(nNums,nOps,sumatoria);
                        return arr;
                    }

                    indexArrOps++;
                    division(nNums,nOps,indexArrOps,sumatoria);
                    
                    
            }


            function suma(arrNums,arrOpsSyms,period,resultadoAcu){ //opeString puede ser "*","+","-","/" //periodo es el index en el que va
                if(arrNums.length==1){
                        return;
                    }
                    // El periodo tiene que iniciar en cero
                    //Resultado acumulado 
                    var indexArrOps = period; //tenemos que sumar al periodo ++ para seguir avanzando en el arreglo
                    var sumatoria = resultadoAcu; 
                    var nNums = arrNums;
                    var nOps = arrOpsSyms;
          
                    if(arrOpsSyms[indexArrOps]=="+"||arrOpsSyms[indexArrOps]=="-"){
                        var num1 = parseFloat(arrNums[indexArrOps]);
                        var num2 = parseFloat(arrNums[indexArrOps+1]);
               
                        
                            if(arrOpsSyms[indexArrOps]=="-"){
                                sumatoria = num1-num2;
                             
                            }else{
                                sumatoria = num1+num2;
                            }

                        nNums.splice(indexArrOps+1,1);
                        nNums.splice(indexArrOps,1,sumatoria);
                        nOps.splice(indexArrOps,1);
                        indexArrOps--;
                    }
                    
                    if(arrOpsSyms[indexArrOps+1]===undefined || arrOpsSyms[indexArrOps+1]==null){
                        
                        var arr = [nNums,nOps,sumatoria];
                        setOutsideVars(nNums,nOps,sumatoria);
                        return arr;
                    }
                    
                    indexArrOps++;
                    suma(nNums,nOps,indexArrOps,sumatoria);
                        
            }
            

        /* FUNCIONES DE OPERACIONES */

        function readString(operacion){
           
                //1. Primero tenemos que encontrar los números dentro los paréntesis
                var gralString = operacion;
                var patt2 =  /\(([^)]+)\)/g; // Con este regex ya logramos obtener los valores que estén dentro de unos paréntesis
                var res =   gralString.match(patt2);
                //console.log(res);

                var resultadoOp = []; // Se van acumulando los resultados de las operacions
                //2. Apartir del arreglo de conjuntos iteramos en él
                var resultado = 0;
                
                if(res.length>0){
                    var operaciones = screenOperations.operacionesGlobales;
                   
                    
                    for(var i=0; i<res.length;i++){
                            // Aquí empezamos a obtener los arreglos de: números y operacion de cada conjunto
                            var arregloNumeros = findNumbers(res[i]); //función arroja arreglo de números
                          
                            var arregloOperaciones = findSymbolsSubs(res[i]); //Iteramos al arreglo operaciones
                           
                                //El siguiente paso es iterar dentro del conjunto
                                if(arregloNumeros.length!=1){
                               
                                    var numItems = arregloOperaciones.length;
                                    var counter = 0;

                                    multiply(arregloNumeros,arregloOperaciones,0,0);
                                    var other = getOutsideVars();
                                    
                                    division(other[0],other[1],0,0);
                                    var other = getOutsideVars();
                                    
                                    suma(other[0],other[1],0,0);
                                    var other = getOutsideVars();
                                    
                                    resultadoOp[i] =  other[2];
                                }else{
                                    resultadoOp[i] = arregloNumeros[0];
                                }
                                
                                
                    }
                }
                               multiply(resultadoOp,operaciones,0,0);
                               var other = getOutsideVars();
                              
                               division(other[0],other[1],0,0);
                               var other = getOutsideVars();
                               
                               suma(other[0],other[1],0,0);
                               var other = getOutsideVars();
                               

                return other[2];


        }
        
        
       
        // Vamos a hacer una funcion que compare los resultados 

        function validateResponse(correctRes,userInput){
           
            if(correctRes==userInput){
                return true;
            }
            return false;
        }


        // pruebas 
         //Cantidad de conjuntos, num min de subconjuntos por cada conjunto, num max de subconjuntos por cada conjunto, min digitos ,max digitos, allow decimals
                                                // Arreglo deshabilitar falta
        
                                              
        var arr_gral = screenOperations.generador(2,1,3,0,1,["+","-","*"]); //(qtyCGlobales,minSubs, maxSubs,minDigitos,maxDigitos)
        var operacion = screenOperations.procesarArregloGral(arr_gral);
        var res = readString(operacion);
        $(".operacion").html(operacion.replaceAll("*","x","gi"));
        $(".resultado").html(res);

        function anotherOperation(){

            //Clean possible triggered renders
            $(".conjunto_res").hide();
            $(".mensaje").html("");
            $(".userResponse").val("");

            arr_gral = screenOperations.generador(2,1,3,0,1,["+","-","*"]);
            operacion = screenOperations.procesarArregloGral(arr_gral);
            res = readString(operacion);
            $(".operacion").html(operacion.replaceAll("*","x","gi"));
            $(".resultado").html(res);
        }



        // Sobre el cronometro
        const zeroPad = (num, places) => String(num).padStart(places, '0'); // Por si conseguimos un 9 lo convertimos a 09
        
        var cronometer = {
            action: 'start', // action = 'start','restart','stop'
            setTime: 20, // seconds
            barPercent: false, // Obtained at the begging
            barObj: '#myBar',
            displayIn: "",
            start: function(displayIn){ // ".className" "#idName" or other
                this.displayIn = displayIn;
                if(this.barPercent==false){ //Se ejecuta sólo al inicio
                    $(this.barObj).css("width","100%");
                    this.barPercent = (100/this.setTime)/100;
                    this.barPercent = this.barPercent * $(this.barObj).width();
                }
                
                $(displayIn).html(zeroPad(this.setTime,2));

                if(this.setTime==0){ // When we reach 0
                    return;
                }

                if(this.action=="stop"){ // When we set cronometer property to stop
                    return;
                }

                this.setTime--;

                setTimeout(() => { //Función de barra y función recursiva
                    this.barWidth();
                    this.start(displayIn);
                }, 1000);

            },
            stop: function(){ // Aplicarlo en un evento
                this.action = "stop";
            },
            restart: function(seconds){ // Aplicarlo en un evento
                this.setTime = seconds;
                this.barPercent=false;
            },
            refresh: function(seconds){ // resetea las variables
                this.action = 'stop'; // para evitar que se repitan dos o más ciclos,
                
                this.setTime = seconds;
                this.barPercent = false;
                this.action = 'start'; // se vuelve a reestablecer dado que cambiamos a stop para salir del ciclo anterior
                this.start(this.displayIn);
                // NOTA: Puede ocurrir un desfase si cambia el settimeout del método start
                
            },
            barWidth: function(){
               
                if($(this.barObj).width()>0){ // Sólo restar la longitud de barra cuando el valor sea mayor a cero
                    var barLen = $(this.barObj).width()-this.barPercent;
                    $(this.barObj).width(barLen);
                }
              
            }


        }

        // Evento que suceden cuando hay una respuesta correcta
        function onTrueAnswer(){

            cronometer.action = 'stop';
            $(".mensaje").html('<div class="alert alert-success">¡Resultado correcto!</div>');
            setTimeout(() => {
                anotherOperation();
                eventoClickBoton();
                eventoClickTeclaEnter();
                cronometer.refresh(20);
            }, 2000);

        }


        function eventoClickTeclaEnter(){
            $(document).off("keypress");
            $(document).keypress(function(e) {
                
                if(e.which == 13) { //When enter key is pressed
                    var userResponse = $(".userResponse").val();
                    if(userResponse!="" || userResponse!=undefined){
                        userResponse = parseFloat(userResponse);
                        if(validateResponse(res,userResponse)){ //Si la respuesta es válida entonces arrojar mensaje de éxito
                            onTrueAnswer();
                        }else{
                                $(".conjunto_res").show();
                        }
                    }
                }
            });

            
        }
        function eventoClickBoton(){
            $(".validar").off();
            $(".validar").click(function(){
                var userResponse = $(".userResponse").val();
                if(userResponse!="" || userResponse!=undefined){
                    userResponse = parseFloat(userResponse);
                    if(validateResponse(res,userResponse)){ //Si la respuesta es válida entonces arrojar mensaje de éxito
                        onTrueAnswer();
                    }else{
                            $(".conjunto_res").show();
                    }
                }
            })


        }

        


       
        eventoClickBoton();
        eventoClickTeclaEnter();
        cronometer.start(".cronometer");
        

        



        
        

})
</script>

</body>
</html>