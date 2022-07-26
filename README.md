This library is used to create random sets operations including round brackets, such as: "(123+20)-(540+21)", "(2x4-3)*(8+99)"

Also created a function that solves the previous operations while accepting a string as a parameter. It has already configured to take care of the priority of operations, only for sum, subtraction, multiplication and division.

How to use:

Call this function: screenOperations.generador(qtyCGlobales,minSubs, maxSubs,minDigitos,maxDigitos,opsAllowed=["+","-"]) Parameters explained:

qtyCGlobales(int): You have to define how many sets of parenthesis
minSubs(int) and maxSubs(int): Sets a range to display random quantities of sets of numbers
minDigitos(int) and maxDigitos(int): Within the sets of numbers we can define how many digits can be displayed on each set. It has a math formula so when we put 0 it means 10^0=1 , if we use 1 then 10^1-1=9. In the case below we use real values meaning that it will provide numbers within 1-9
opsAllowed(array): Is kind of self-explanatory, just remember that for this while only "+","-","*","/"
var arr = screenOperations.generador(2,1,3,0,1,["+","-","*"]); var operation = screenOperations.procesarArregloGral(arr); // returns randomly -> "(5-8+7)x(2)"

// operation variable its just a string now it needs to be solved with our function

var res = readString(operation);