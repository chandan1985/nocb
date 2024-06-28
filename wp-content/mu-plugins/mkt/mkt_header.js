/**
 * Created by chris.meier on 3/7/17.
 */
//Acton Form Validation Code

function formElementSerializers() { function input(element) { switch (element.type.toLowerCase()) { case 'checkbox': case 'radio': return inputSelector(element); default: return valueSelector(element); } };
    function inputSelector(element) { return element.checked ? element.value : null; };
    function valueSelector(element) { return element.value; };
    function select(element) { return (element.type === 'select-one' ? selectOne : selectMany)(element); };
    function selectOne(element) { var index = element.selectedIndex; return index < 0 ? null : optionValue(element.options[index]); };
    function selectMany(element) { var length = element.length; if (!length) return null; var values = []; for (var i = 0; i < length; i++) { var opt = element.options[i]; if (opt.selected) values.push(optionValue(opt)); } return values; };
    function optionValue(opt) { if (document.documentElement.hasAttribute) return opt.hasAttribute('value') ? opt.value : opt.text; var element = document.getElementById(opt); if (element && element.getAttributeNode('value')) return opt.value; else return opt.text; };
    return { input: input, inputSelector: inputSelector, textarea: valueSelector, select: select, selectOne: selectOne, selectMany: selectMany, optionValue: optionValue, button: valueSelector };
};

var requiredFields = new Array();
var requiredFieldGroups = new Array();

addRequiredField = function (id) { requiredFields.push (id); };

addRequiredFieldGroup = function (id, count) { requiredFieldGroups.push ([id, count]); };

missing = function (fieldName) {
    var f = document.getElementById(fieldName);
    var v = formElementSerializers()[f.tagName.toLowerCase()](f);

    if (v) { v = v.replace (/^s*(.*)/, "$1"); v = v.replace (/(.*?)s*$/, "$1"); }
    if (!v) { f.style.backgroundColor = '#FFFFCC'; return 1; }
    else { f.style.backgroundColor = ''; return 0; }
};

missingGroup = function (fieldName, count) {
    var result = 1;
    var color = '#FFFFCC';

    for (var i = 0; i < count; i++) {
        if (document.getElementById(fieldName+'-'+i).checked) { color = ''; result = 0; break; }
    }

    for (var i = 0; i < count; i++) document.getElementById(fieldName+'-'+i).parentNode.style.backgroundColor = color; return result;
};

var validatedFields = new Array();

addFieldToValidate = function (id, validationType, arg1, arg2) { validatedFields.push ([ id, validationType, arg1, arg2 ]); };

validateField = function (id, fieldValidationValue, arg1, arg2) { var field = document.getElementById(id); var name = field.name; var value = formElementSerializers()[field.tagName.toLowerCase()](field); for (var i = 0; i < validators.length; i++) { var validationDisplay = validators[i][3]; var validationValue = validators[i][1]; var validationFunction = validators[i][2]; if (validationValue === fieldValidationValue) { if (!validationFunction (value,arg1,arg2,id)) { field.style.backgroundColor = '#FFFFCC'; alert (validationDisplay); return false; } else { field.style.backgroundColor = ''; } break; } } for (var i = 0; i < implicitValidators.length; i++) { var validationDisplay = implicitValidators[i][0]; var validationValue = implicitValidators[i][1]; var validationFunction = implicitValidators[i][2]; if (validationValue === fieldValidationValue) { if (!validationFunction (value,arg1,arg2,id)) { field.style.backgroundColor = '#FFFFCC'; alert (validationDisplay); return false; } else { field.style.backgroundColor = ''; } break; } } return true; };
var r = ''; formElementById = function(form, id) { for (var i = 0; i < form.length; ++i) if (form[i].id === id) return form[i]; return null; };
doSubmit = function(form) { try { if (typeof(customSubmitProcessing) === "function") customSubmitProcessing(); } catch (err) { } var ao_jstzo = formElementById(form, "ao_jstzo"); if (ao_jstzo) ao_jstzo.value = new Date().getTimezoneOffset(); var submitButton = document.getElementById(form.id+'_ao_submit_button'); submitButton.style.visibility = 'hidden'; var missingCount = 0; for (var i = 0; i < requiredFields.length; i++) if (requiredFields[i].indexOf(form.id+'_') === 0) missingCount += missing (requiredFields[i]); for (var i = 0; i < requiredFieldGroups.length; i++) if (requiredFieldGroups[i][0].indexOf(form.id+'_') === 0) missingCount += missingGroup (requiredFieldGroups[i][0], requiredFieldGroups[i][1]); if (missingCount >
    0) { alert ('Please fill all required fields. '); submitButton.style.visibility = 'visible'; return; } for (var i = 0; i < validatedFields.length; i++) { var ff = validatedFields[i]; if (ff[0].indexOf(form.id+'_') === 0 && !validateField (ff[0], ff[1], ff[2], ff[3])) { document.getElementById(ff[0]).focus(); submitButton.style.visibility = 'visible'; return; } } if (formElementById(form, 'ao_p').value === '1') { submitButton.style.visibility = 'visible'; return; } formElementById(form, 'ao_bot').value = 'nope'; form.submit(); };
