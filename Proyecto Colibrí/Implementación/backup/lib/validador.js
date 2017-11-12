/*
 *
 * Validador de Formularios.
 * 
 * UtilizaciÃ³n:
 * var Validador = new Validator("nombreFormulario");
 * 
 * Agregando validadores:
 * Validador.addValidation("nombreAtributo","Opciones");
 * 
 * Opciones
 * obligatorio              Campo obligatorio   Validador.addValidation("nombre","obligatorio");
 * largoMaximo              Largo maximo        Validador.addValidation("nombre","largoMaximo=200");
 * largoMinimo              Largo minimo        Validador.addValidation("nombre","largoMaximo=5");
 * alfanumerico             Campo alfanumerico  Validator.addValidation("domicilio","alfanumerico");
 * alpha | alphabetic       Campo alfabetico    Validator.addValidation("numero","alpha");
 * email                    e-mail              Validator.addValidation("correo","email");
 * num | numeric            Campo numerico      Validator.addValidation("numero","num");
 * moneda                   Formato 999.99      Validador.addValidation("precio","moneda");
 * fecha                    Formato dd/mm/yyyy  Validador.addValidation("fecha","fecha");
 * selectOptions =??        Select Obligatorio  Validador.addValidation("select","selectOptions=0");
 * lt=X                    Debe ser menor q X   Validador.addValidation("numero","lt=100");
 * gt=X                    Debe ser mayor q X   Validador.addValidation("numero","gt=0");

lt=???
lessthan=??? 	Verify the data to be less than the value passed. Valid only for numeric fields.
example: if the value should be less than 1000 give validation description as "lt=1000"
gt=???
greaterthan=??? 	Verify the data to be greater than the value passed. Valid only for numeric fields.
example: if the value should be greater than 10 give validation description as "gt=10"
regexp=??? 	Check with a regular expression the value should match the regular expression.
example: "regexp=^[A-Za-z]{1,20}$" allow up to 20 alphabetic characters.
    -------------------------------------------------------------------------  
 */

var tituloSistema =  'GEDoc UARG\n\n';
function Validator(frmname)
{
    this.formobj=document.forms[frmname];
    if(!this.formobj)
    {
        alert("ERROR: No se ha encontrado el formulario indicado " + frmname );
        return;
    }
    if(this.formobj.onsubmit)
    {
        this.formobj.old_onsubmit = this.formobj.onsubmit;
        this.formobj.onsubmit=null;
    }
    else
    {
        this.formobj.old_onsubmit = null;
    }
    this.formobj.onsubmit=form_submit_handler;
    this.addValidation = add_validation;
    this.setAddnlValidationFunction=set_addnl_vfunction;
    this.clearAllValidations = clear_all_validations;
}
function set_addnl_vfunction(functionname)
{
    this.formobj.addnlvalidation = functionname;
}
function clear_all_validations()
{
    for(var itr=0;itr < this.formobj.elements.length;itr++)
    {
        this.formobj.elements[itr].validationset = null;
    }
}
function form_submit_handler()
{
    for(var itr=0;itr < this.elements.length;itr++)
    {
        this.elements[itr+1].style.border = '1px solid black';
        if(this.elements[itr].validationset &&
            !this.elements[itr].validationset.validate())
            {
            return false;
        }
    }
    if(this.addnlvalidation)
    {
        str =" var ret = "+this.addnlvalidation+"()";
        eval(str);
        if(!ret) return ret;
    }
    return true;
}
function add_validation(itemname,descriptor,errstr)
{
    if(!this.formobj)
    {
        alert("BUG: El nombre del objeto formulario no esta informado correctamente.");
        return;
    }
    var itemobj = this.formobj[itemname];
    if(!itemobj)
    {
        alert("BUG: No se puede seleccionar el input llamado "+ itemname);
        return;
    }
    if(!itemobj.validationset)
    {
        itemobj.validationset = new ValidationSet(itemobj);
    }
    itemobj.validationset.add(descriptor,errstr);
}
function ValidationDesc(inputitem,desc,error)
{
    this.desc=desc;
    this.error=error;
    this.itemobj = inputitem;
    this.validate=vdesc_validate;
}
function vdesc_validate()
{
    if(!V2validateData(this.desc, this.itemobj, this.error))
    {
        this.itemobj.focus();
        if ( (this.desc.substring(0,13)) == 'selectOptions') {
            this.itemobj.parentNode.style.border = '1px solid red';
        } else {
            this.itemobj.select();
            this.itemobj.style.border = '1px solid red';
        }
        return false;
    }
    return true;
}
function ValidationSet(inputitem)
{
    this.vSet=new Array();
    this.add= add_validationdesc;
    this.validate= vset_validate;
    this.itemobj = inputitem;
}
function add_validationdesc(desc,error)
{
    this.vSet[this.vSet.length]= 
    new ValidationDesc(this.itemobj,desc,error);
}
function vset_validate()
{
    for(var itr=0;itr<this.vSet.length;itr++)
    {
        if(!this.vSet[itr].validate())
        {
            return false;
        }
    }
    return true;
}
function validateEmailv2(email)
{
    // a very simple email validation checking. 
    // you can add more complex email checking if it helps 
    if(email.length <= 0)
    {
        return true;
    }
    var splitted = email.match("^(.+)@(.+)$");
    if(splitted == null) return false;
    if(splitted[1] != null )
    {
        var regexp_user=/^\"?[\w-_\.]*\"?$/;
        if(splitted[1].match(regexp_user) == null) return false;
    }
    if(splitted[2] != null)
    {
        var regexp_domain=/^[\w-\.]*\.[A-Za-z]{2,4}$/;
        if(splitted[2].match(regexp_domain) == null) 
        {
            var regexp_ip =/^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
            if(splitted[2].match(regexp_ip) == null) return false;
        }// if
        return true;
    }
    return false;
}
function V2validateData(strValidateStr,objValue,strError) 
{ 
    var epos = strValidateStr.search("="); 
    var  command  = ""; 
    var  cmdvalue = ""; 
    if(epos >= 0) 
    { 
        command  = strValidateStr.substring(0,epos); 
        cmdvalue = strValidateStr.substr(epos+1); 
    } 
    else 
    { 
        command = strValidateStr; 
    } 
    
    nombreObjeto = objValue.title ? objValue.title : objValue.name ? objValue.name : objValue.id;
    
    switch(command) 
    { 
        case "obligatorio":
        { 
            if(eval(objValue.value.length) == 0) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + "Campo Requerido : " + (nombreObjeto);
                    objValue.value=objValue.parentNode.id;
                }//if 
                alert(strError); 
                return false; 
            }//if 
            break;             
        }        
        case "imagen":
        {
            // Campo vacÃ­o.
            if(!objValue.value) {
                archivo_permitido = true;
                break;
            }
            /*
             * Validador de extensiones.
             */
            var extensiones_permitidas = new Array(".gif", ".jpg", ".jpeg", ".png");
            var extension_archivo = (objValue.value.substring(objValue.value.lastIndexOf("."))).toLowerCase();
            var archivo_permitido = false;
            for (var i=0; i < extensiones_permitidas.length; i++) {
                if(extension_archivo == extensiones_permitidas[i]) archivo_permitido = true;
            }

            if(!archivo_permitido) {
                alert(tituloSistema + '\n\nExtension de archivo no permitida. \n\nPor favor elija una foto con una de las siguientes extensiones: \n\ngif, jpg, jpeg o png');
                return false;
            }
            break;
        }            
        case "fecha":
        {
            var validacion = objValue.value.match(/^\d{1,2}\/\d{1,2}\/\d{2,4}$/) ||objValue.value == ''; 
            if(validacion == null) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Solo se permiten fechas en el formato DD/MM/YYYY";
                }//if
                alert(strError);
                return false; 
            }//if 
            break;               
        }
        case "largoMaximo":
        { 
            if(eval(objValue.value.length) >  eval(cmdvalue)) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + " No puede tener mas de : "+cmdvalue+" caracteres "; 
                }//if 
                alert(strError + "\n[Fueron ingresados = " + objValue.value.length + " caracteres ]"); 
                return false; 
            }//if 
            break; 
        }//case maxlen 
        case "largoMinimo":
        { 
            if((objValue.value.length > 0) && (eval(objValue.value.length) <  eval(cmdvalue))) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Debe tener como minimo " + cmdvalue + " caracteres. "; 
                }//if               
                alert(strError + "\n[Fueron ingresados = " + objValue.value.length + " caracteres]"); 
                return false;                 
            }//if 
            break; 
        }//case minlen 
        case "alfanumerico":
        { 
            var charpos = objValue.value.search("[^A-Za-z0-9]"); 
            if(objValue.value.length > 0 &&  charpos >= 0) 
            { 
                if (!strError || strError.length == 0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Solo se permiten caracteres letras y numeros."; 
                    alert(strError);
                }
                return false; 
            }
            break; 
        }//case alphanumeric 
        case "num": 
        case "numeric":
        { 
            var charpos = objValue.value.search("[^0-9]"); 
            if(objValue.value.length > 0 &&  charpos >= 0) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Solo se permiten numeros."; 
                }//if               
                alert(strError + "\n [Error en el caracter de la posicion " + eval(charpos+1)+"]"); 
                return false; 
            }//if 
            break;               
        }//numeric 
        case "positivonegativo":
        { 
            var charpos = objValue.value.search("[^0-9\-]"); 
            if(objValue.value.length > 0 &&  charpos >= 0) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Solo se permiten numeros positivos o negativos."; 
                }//if               
                alert(strError + "\n [Error en el caracter de la posicion " + eval(charpos+1)+"]"); 
                return false; 
            }//if 
            charpos = objValue.value.search("[^0-9]"); 
            if(objValue.value.length > 0 &&  charpos > 0) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Solo se permiten numeros positivos o negativos."; 
                }//if               
                alert(strError + "\n [Error en el caracter de la posicion " + eval(charpos+1)+"]"); 
                return false; 
            }//if 
            break;               
        }//numeric 
        case "moneda":
        { 
            var charpos = objValue.value.search("[^0-9\-.]"); 
            if(objValue.value.length > 0 &&  charpos >= 0) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Solo se permiten digitos y un punto (ej.: 100 o 100.2 o 100.25)"; 
                }//if               
                alert(strError + "\n [Error en el caracter de la posicion " + eval(charpos+1)+"]"); 
                return false; 
            }//if 
            break;               
        }//moneda
        case "alphabetic": 
        case "alpha":
        { 
            var charpos = objValue.value.search("[^A-Za-z]"); 
            if(objValue.value.length > 0 &&  charpos >= 0) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Solo se aceptan caracteres alfabÃ©ticos. "; 
                }//if                             
                alert(strError + "\n [Error el en carÃ¡cter en la posiciÃ³n " + eval(charpos+1)+"]"); 
                return false; 
            }//if 
            break; 
        }//alpha 
        case "alnumhyphen":
        {
            var charpos = objValue.value.search("[^A-Za-z0-9\-_]"); 
            if(objValue.value.length > 0 &&  charpos >= 0) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": characters allowed are A-Z,a-z,0-9,- and _"; 
                }//if                             
                alert(strError + "\n [Error character position " + eval(charpos+1)+"]"); 
                return false; 
            }//if 			
            break;
        }
        case "email":
        { 
            if(!validateEmailv2(objValue.value)) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Ingrese Un Correo Valido "; 
                }//if                                               
                alert(strError); 
                return false; 
            }//if 
            break; 
        }//case email 
        case "lt": 
        case "lessthan":
        { 
            if(isNaN(objValue.value)) 
            { 
                alert(objValue.name+": Deberia ser un Numero "); 
                return false; 
            }//if 
            if(eval(objValue.value) >=  eval(cmdvalue)) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + " : value should be less than "+ cmdvalue; 
                }//if               
                alert(strError); 
                return false;                 
            }//if             
            break; 
        }//case lessthan 
        case "gt": 
        case "greaterthan":
        { 
            if(isNaN(objValue.value)) 
            { 
                alert(objValue.name+": Deberia ser un Numero "); 
                return false; 
            }//if 
            if(eval(objValue.value) <=  eval(cmdvalue)) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + " : value should be greater than "+ cmdvalue; 
                }//if               
                alert(strError); 
                return false;                 
            }//if             
            break; 
        }//case greaterthan 
        case "regexp":
        { 
            if(objValue.value.length > 0)
            {
                if(!objValue.value.match(cmdvalue)) 
                { 
                    if(!strError || strError.length ==0) 
                    { 
                        strError = tituloSistema + nombreObjeto + ": Se encontraron Caracteres Invalidos"; 
                    }//if                                                               
                    alert(strError); 
                    return false;                   
                }//if 
            }
            break; 
        }//case regexp 
        case "selectOptions":
        { 
            if(objValue.selectedIndex == null) 
            { 
                alert("BUG: dontselect command for non-select Item"); 
                return false; 
            } 
            if(objValue.selectedIndex == eval(cmdvalue)) 
            { 
                if(!strError || strError.length ==0) 
                { 
                    strError = tituloSistema + nombreObjeto + ": Por Favor Seleccione Una Opcion."; 
                }//if                                                               
                alert(strError);
                return false;
            } 
            break; 
        }//case dontselect 
    }//switch 
    return true; 
}

function validar_password(campo1, campo2) {
    if(campo1.value != campo2.value) {
        alert(tituloSistema + "Las contraseñas ingresadas son diferentes.");
        campo1.focus();
        campo1.style.border = '1px solid red';
        campo2.style.border = '1px solid red';
        return false;
    } else {
        return true;
    }
}