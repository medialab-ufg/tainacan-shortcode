var CONTROLLERS_PATH = "../wp-content/plugins/tainacan-short-code/controllers/";
var config = {
    lineNumbers: true,
    mode: "text/html",
    extraKeys: {"Ctrl-Space": "autocomplete"}

};

var items_editor = CodeMirror.fromTextArea(document.getElementById("image-show-template"), config);
var collection_editor = CodeMirror.fromTextArea(document.getElementById("collection-show-template"), config);

function save_templates() {
    var items_editor_val = items_editor.getValue(), 
        collection_editor_val = collection_editor.getValue();
    
    $.ajax({
        url: CONTROLLERS_PATH + "templates_controller.php?operation=save_templates",
        data: {items_editor_val: items_editor_val, collection_editor_val: collection_editor_val},
        method: "POST"
    }).done(function (response) {
        if(response === true)
        {
            alert("Salvo!");
        }
        alert(response);
    });
}