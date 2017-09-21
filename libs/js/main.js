var config = {
    lineNumbers: true,
    mode: "text/html",
    extraKeys: {"Ctrl-Space": "autocomplete"}

};

CodeMirror.fromTextArea(document.getElementById("image-show-template"), config);
CodeMirror.fromTextArea(document.getElementById("collection-show-template"), config);