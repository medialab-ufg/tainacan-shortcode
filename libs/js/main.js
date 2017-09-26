var config = {
    lineNumbers: true,
    mode: "text/html",
    extraKeys: {"Ctrl-Space": "autocomplete"}

};

var items_editor = CodeMirror.fromTextArea(document.getElementById("items-show-template"), config);
var collection_editor = CodeMirror.fromTextArea(document.getElementById("collection-show-template"), config);

items_editor.on('change', function (cm_items_editor) {
    $("#items-show-template").text(cm_items_editor.getValue());
});

collection_editor.on('change', function (cm_collection_editor) {
    $("#collection-show-template").text(cm_collection_editor.getValue());
});