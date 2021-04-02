
<?php

// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 *
 * @package     local_form_conditioner
 * @copyright   2021 Paul E. <paul-edoho-eket@uiowa.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */




require_once('../../config.php');
global $CFG, $PAGE;

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Add Form Rules');
$PAGE->set_heading('Form Conditioner');
$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('bootstrap');
$PAGE->requires->jquery_plugin('bootstrap-css');
$PAGE->set_url('/local/form_conditioner/');

$formconditionernode = $PAGE->navigation->add(get_string('pluginname','local_form_conditioner'), new moodle_url('/local/form_conditioner/'), navigation_node::TYPE_CONTAINER);


echo $OUTPUT->header();
?>








<div class="container mt-5">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="btn_protocol">Form Link:</span>
            </div>
            <input type="text" class="form-control" id="url_input" aria-describedby="btn_protocol">
            <div class="input-group-append">
                <button id="findBtn" class="btn btn-primary" type="button" data-toggle="modal" data-target="#searchModal">Find Form</button>
            </div>
        </div>
        <div class="row">
        </div>
    </div>

    <div class="container" id="ruleFormWrap">

    </div>
    <div class="row p-2 d-none" id="rulesButtons">

        <div class="col-12 text-center">
            <input id="makeRuleBtn" class="btn btn-outline-primary" type="button" value="New Rule">
            <input value="Generate Conditions" onclick="generateConditionsScript()" id="generateRuleBtn" class="btn btn-primary btn" type="button">
        </div>
    </div>


    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Select a Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
                </div>
                <div class="modal-body" id="main-modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="formChoiceBtn">Select Form</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mainDisplayModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
                </div>
                <div id="mainDisplayModalBody" class="modal-body overflow-auto">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadConditions()">Save JS</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <!-- <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js'></script> -->

<script>

var formHTML;
var wrapEl = $('#conditionsWrap');
var elArray = [];
var selectedForm;
var selectedFormObject;

var editSVG = '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 383.947 383.947" style="enable-background:new 0 0 383.947 383.947;" xml:space="preserve"> <g> <g> <g> <polygon points="0,303.947 0,383.947 80,383.947 316.053,147.893 236.053,67.893 			"/> <path d="M377.707,56.053L327.893,6.24c-8.32-8.32-21.867-8.32-30.187,0l-39.04,39.04l80,80l39.04-39.04 C386.027,77.92,386.027,64.373,377.707,56.053z"/> </g> </g> </g> </svg>';

function getHTML(url) {


    $('#main-modal-body').html('<img width="200" src="https://flevix.com/wp-content/uploads/2019/07/Curve-Loading.gif" alt="loading" />');
    $('#main-modal-body').addClass('text-center');
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'text',
        success: function(data) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(data, "text/html");
            var formLength = doc.getElementsByTagName("form").length;
            var forms = [].slice.call(doc.getElementsByTagName("form"));

            $('#main-modal-body').removeClass('text-center');

            formHTML = doc;
            $('#main-modal-body').html(formLength == 1 ? formLength + " form found. . ." : formLength + " forms found. . .");
            $('#main-modal-body').append('<div class="input-group mb-3"> <div class="input-group-prepend"> <label class="input-group-text" for="chooseFormSelect">Forms Found</label></div> <select class="custom-select" id="chooseFormSelect"><option selected>Select a Form</option></select><div class="my-3 py-2 bg-secondary text-light container" id="formPreview"><samp></samp></div></div>');
            $('#formPreview').attr('hidden', true);
            forms.forEach(function(f, i) {
                $('#chooseFormSelect').append('<option value="' + i + '">' + 'Form ' + (i + 1) + '</option>');
            });
            $("#chooseFormSelect").change(function() {
                $('#formPreview').removeAttr('hidden');
                if ($("#chooseFormSelect").val()) {
                    $('#formPreview').text(String(forms[Number($("#chooseFormSelect").val())].outerHTML));
                    selectedForm = forms[Number($("#chooseFormSelect").val())];

                }

            });
            $('#formChoiceBtn').click(function() {
                getForm(selectedForm, $("#chooseFormSelect").val());

                $('#searchModal').modal('hide');
            });


            //Set Modal Display
            if (!forms.length) {
                $('#formChoiceBtn').attr('disabled', 'true');
                $('#chooseFormSelect').attr('disabled', 'true');
            } else {
                $('#formChoiceBtn').removeAttr('disabled');
                $('#chooseFormSelect').removeAttr('disabled');
            }
            //End Set Modal Display


        },
        error: function(data) {
            console.error("Error getting form(s).");
        }
    });
    return false;
}

function getForm(form, formNumber) {
    $('#rulesButtons').removeClass('d-none');
    selectedFormObject = {
        //outerHTML: form,
        formID: form.getAttribute('id') ? form.getAttribute('id') : null,
        formNo: formNumber,
        nameElements: [].slice.call(form.querySelectorAll('*[name]')),
        nameElementObjects: [],
        conditionsObjects: []
    };

    selectedFormObject.nameElements.forEach(function(item, index) {
        var newNameObj = {
            name: item.getAttribute('name'),
            val: "",
            type: item.getAttribute('type') ? item.getAttribute('type') : ""
        };
        selectedFormObject.nameElementObjects.push(newNameObj);
    });
    createRuleForm();

}


$('#findBtn').click(function() {
    getHTML($('#url_input').val());
});

function download(filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);
}


function setBoilerpate(conditionsData) {
    return `(function(rules) {
    function initConditions(data) {
        var conditions = data.conditionsObjects.filter(function(item, index){
            return item.defaultDisplay == null;
        });
        var defaults = data.conditionsObjects.filter(function(item, index){
            return item.defaultDisplay != null;
        });
        
        conditions.forEach(function(item, index) {
            setElementCondition(getLocalForm(data), item);
        }); 
        defaults.forEach(function(item, index) {
            getLocalForm(data).querySelector(item.customSelector != "true" ? '[name="' + item.name + '"]' : item.customSelector).setAttribute(item.displayType == "_defaultHidden_" ? "hidden" : "disabled", 'true');
        }); 
    }

    function setElementCondition(targetForm, item) {
        var nameEl = targetForm.querySelector('[name="' + item.name + '"]');
        nameEl.addEventListener('change', function() {
            if (item.operator == "equal") {
                if (this.value == item.matchValue) {
                    targetForm.querySelector(item.customSelector == "false" ? '[name="' + item.changeElement + '"]' : item.changeElement).setAttribute(item.displayType, 'true');
                } else {
                    targetForm.querySelector(item.customSelector == "false" ? '[name="' + item.changeElement + '"]' : item.changeElement).removeAttribute(item.displayType);
                }
            } else {
                if (this.value != item.matchValue) {
                    targetForm.querySelector(item.customSelector == "false" ? '[name="' + item.changeElement + '"]' : item.changeElement).setAttribute(item.displayType, 'true');
                } else {
                    targetForm.querySelector(item.customSelector == "false" ? '[name="' + item.changeElement + '"]' : item.changeElement).removeAttribute(item.displayType);
                }
            }
        });
    }

    function getLocalForm(d) {
        return document.getElementsByTagName('form')[d.formNo];
    }
    initConditions(rules);
})(` + conditionsData + `);`;
}




var iTest = 0;
$('#makeRuleBtn').click(function() {
    createRuleForm();
});

function createRuleForm() {
    selectedFormObject.conditionsObjects.push({});
    var i = selectedFormObject.conditionsObjects.length;
    var formID = "ruleForm" + i;
    var newForm = $('#ruleFormWrap').append('<form id="' + formID + '" class="ruleForm container p-2"><fieldset id="' + formID + 'Row" class="row p-2"></fieldset></form>');
    $('#' + formID + 'Row')
        //Listening Element
        .append('<div class="col-2 param input_group"><select data-custom="false" id="' + formID + 'ListenSelect"  class="custom-select mw-100" name="listening-element"><option>Select Form Element Name</option></select></div>')
        //Operator
        .append('<div class="col-2 param-row text-center"><div class="input_group operator param"><select id="' + formID + 'OperatorSelect" name="operator" class="custom-select mw-100"><option value="equal">Is Equal</option><option value="notequal">Is Not Equal</option><option value="_defaultHidden_">Hide on Default</option><option value="_defaultDisabled_">Disable on Default</option></select></div></div>')
        //Match Value
        .append('<div class="col-2 param-row text-center"><div class="input_group operator param text-center"><input id="' + formID + 'MatchStringInput" type="text" name="matchstr" class="text-center form-control"></div> </div>')
        //Display Type
        .append('<div class="col-2 param-row text-center"><div class="input_group param"><select id="' + formID + 'DisplaySelect" class="custom-select" name="display-type"> <option value="hidden">Hide</option> <option value="disabled">Disable</option> </select> </div> </div>')
        //Change Element
        .append('<div class="col-2 param"><div class="input_group"><select id="' + formID + 'ChangeSelect" style="max-width: 160px;" class="custom-select  mw-100" name="change-element"><option>Select Form Element Name</option></select> </div> </div>')
        //Utility Buttons
        .append('<div class="row param-row text-center"><div class="col-2 param"><div class="btn-toolbar mb-3" role="toolbar"><div class="btn-group mr-2" role="group"><button onclick="saveCondition(' + i + ')" type="button" class="btn btn-outline-success" id="formSaveBtn' + i + '">&#10003;</button><button type="button" class="btn btn-outline-danger" id="formDelBtn' + i + '" onclick="deleteCondition(' + i + ')">-</button> </div> </div> </div> </div>');


    selectedFormObject.nameElementObjects.forEach(function(f, i) {
        $('#' + formID + 'ListenSelect').append('<option value="' + f.name + '">' + f.name + '</option>');
        $('#' + formID + 'ChangeSelect').append('<option value="' + f.name + '">' + f.name + '</option>');
    });
    $('#' + formID + 'ListenSelect').append('<option value="_custom_">Custom Selector</option>');
    $('#' + formID + 'ChangeSelect').append('<option value="_custom_">Custom Selector</option>');
    
    $('#' + formID + 'OperatorSelect').change(function(){

        if($(this).val() == "_defaultHidden_" || $(this).val() == "_defaultDisabled_"){
            $('#' + formID + 'ChangeSelect').attr('disabled', 'true');
            $('#' + formID + 'DisplaySelect').attr('disabled', 'true');
            $('#' + formID + 'MatchStringInput').attr('disabled', 'true');
        } else {
            if(  $('#' + formID + 'ChangeSelect').attr('disabled') ){
                $('#' + formID + 'ChangeSelect').removeAttr('disabled');
            }
            if( $('#' + formID + 'DisplaySelect').attr('disabled')){
                $('#' + formID + 'DisplaySelect').removeAttr('disabled');  
            }
            if( $('#' + formID + 'MatchStringInput').attr('disabled') ){
                $('#' + formID + 'MatchStringInput').removeAttr('disabled');
            }
        }
    });

    $('#' + formID + 'ChangeSelect').change(function() {

        if ($(this).val() == "_custom_") {
            var customInput = prompt('Enter Custom Selector (example: #box1 )');
            if (customInput.length) {
                $('#' + formID + 'ChangeSelect').html('<option value="' + customInput + '">' + customInput + '</option>');
                $('#' + formID + 'ChangeSelect').attr('disabled', 'true');
                $('#' + formID + 'ChangeSelect').attr('data-custom', 'true');
            }
        } else {
            $('#' + formID + 'ChangeSelect').attr('data-custom', 'false');
        }
    });
    $('#' + formID + 'ListenSelect').change(function() {

        if ($(this).val() == "_custom_") {
            var customInput = prompt('Enter Custom Selector (example: #box1 )');
            if (customInput.length) {
                $('#' + formID + 'ListenSelect').html('<option value="' + customInput + '">' + customInput + '</option>');
                $('#' + formID + 'ListenSelect').attr('disabled', 'true');
                $('#' + formID + 'ListenSelect').attr('data-custom', 'true');
            }
        } else {
            $('#' + formID + 'ListenSelect').attr('data-custom', 'false');
        }
    });
};

function deleteCondition(i) {
    selectedFormObject.conditionsObjects.splice(i - 1, 1);
    $("#ruleForm" + i).remove();
}

function editRule(i) {
    console.log(i);
    $("#editBtn" + i).remove();
    $('#formSaveBtn' + i).addClass('btn-outline-success');
    $('#formDelBtn' + i).addClass('btn-outline-danger');
    $('#ruleForm' + i + ' fieldset').removeAttr('disabled', 'false');
}

function downloadConditions() {
    download('FormConditions.js', setBoilerpate(JSON.stringify(selectedFormObject.conditionsObjects)));
}

function saveCondition(i) {
    if( $('#ruleForm' + i + ' select[name="operator"]').val() != "_defaultHidden_" && $('#ruleForm' + i + ' select[name="operator"]').val() != "_defaultDisabled_" ){
   
        //console.log($('#ruleForm' + i + ' select[name="operator"]').val() != "_defaultHidden_" , $('#ruleForm' + i + ' select[name="operator"]').val() != "_defaultDisabled_", $('#ruleForm' + i + ' select[name="operator"]').val(), $('#ruleForm' + i + ' select[name="operator"]').val()  );
        if (
            $('#ruleForm' + i + ' select[name="listening-element"]').val().length &&
            $('#ruleForm' + i + ' select[name="change-element"]').val().length &&
            $('#ruleForm' + i + ' input[name="matchstr"]').val().length
        ) {
            $('#formSaveBtn' + i).removeClass('btn-outline-success');
            $('#formDelBtn' + i).removeClass('btn-outline-danger');
            $('#ruleForm' + i + ' fieldset').attr('disabled', 'true');
            $('#ruleForm' + i + ' fieldset').append('<span id="editBtn' + i + '" onclick="editRule(' + i + ')" style="cursor: pointer; margin: 5px; width: 15px; height: 15px;" id="edit' + i + ' fieldset">' + editSVG + '</span>')
    
    
            selectedFormObject.conditionsObjects[i - 1] = {
                name: $('#ruleForm' + i + ' select[name="listening-element"]').val(),
                operator: $('#ruleForm' + i + ' select[name="operator"]').val(),
                matchValue: $('#ruleForm' + i + ' input[name="matchstr"]').val(),
                displayType: $('#ruleForm' + i + ' select[name="display-type"]').val(),
                changeElement: $('#ruleForm' + i + ' select[name="change-element"]').val(),
                customSelector: $('#ruleForm' + i + ' select[name="change-element"]').attr('data-custom'),
                defaultDisplay: null
            };
        } else {
            alert('Please Provide Values for all Fields');
        }
    } else {
  
        if (
            $('#ruleForm' + i + ' select[name="listening-element"]').val().length && $('#ruleForm' + i + ' select[name="listening-element"]').val() != "Select Form Element Name"
        ) {
            $('#formSaveBtn' + i).removeClass('btn-outline-success');
            $('#formDelBtn' + i).removeClass('btn-outline-danger');
            $('#ruleForm' + i + ' fieldset').attr('disabled', 'true');
            $('#ruleForm' + i + ' fieldset').append('<span id="editBtn' + i + '" onclick="editRule(' + i + ')" style="cursor: pointer; margin: 5px; width: 15px; height: 15px;" id="edit' + i + ' fieldset">' + editSVG + '</span>')
    
    
            selectedFormObject.conditionsObjects[i - 1] = {
                name: $('#ruleForm' + i + ' select[name="listening-element"]').val(),
                operator: $('#ruleForm' + i + ' select[name="operator"]').val(),
                matchValue: $('#ruleForm' + i + ' select[name="operator"]').val(),
                displayType: $('#ruleForm' + i + ' select[name="operator"]').val(),
                changeElement: $('#ruleForm' + i + ' select[name="operator"]').val(),
                customSelector: $('#ruleForm' + i + ' select[name="change-element"]').attr('data-custom'),
                defaultDisplay: $('#ruleForm' + i + ' select[name="operator"]').val()
            };
        } else {
            alert('Please Provide a Value for the Form Element to Select');
        }
    }
}


function checkRules() {
    var ruleFields = document.getElementById('ruleFormWrap').querySelectorAll('fieldset');
    var startLength = ruleFields.length;
    ruleFields = [].slice.call(ruleFields);
    var completeRules = ruleFields.filter(function(r, i) {

        return r.hasAttribute('disabled');
    });


    return startLength == completeRules.length;

}

function generateConditionsScript() {

    var finalConditionsOutput = {
        formID: selectedFormObject.formID,
        formNo: selectedFormObject.formNo,
        conditionsObjects: selectedFormObject.conditionsObjects,
    };
    console.log(finalConditionsOutput);
    if (checkRules()) {
        $('#mainDisplayModal').modal('show');
        $('#mainDisplayModal .modal-body').html(setBoilerpate(JSON.stringify(finalConditionsOutput)));
        $('#mainDisplayModal .modal-title').html('Results');
    } else {
        alert('Please Complete and Save all Conditional Rules.')
    }
} 	

	
	
</script>



<?php
echo $OUTPUT->footer();
