import '../bootstrap-dependencies';
import $ from 'jquery';
import BpmnModeler from 'bpmn-js/lib/Modeler';
import { debounce } from 'min-dash';
import minimapModule from 'diagram-js-minimap';

let container = $('#js-drop-zone');

let modeler = new BpmnModeler({
    container: '#js-canvas',
    additionalModules: [
        minimapModule
    ]
});

document.addEventListener("DOMContentLoaded", function(event) {
    fetch(new Request(window.location.pathname + '/fetch'))
        .then((response) => {
            response.text().then((text) => {
                openDiagram(text);
            });
        });
});

function createNewDiagram() {
    openDiagram('');
}

function openDiagram(xml) {

    modeler.importXML(xml, function(err) {

        if (err) {
            container
                .removeClass('with-diagram')
                .addClass('with-error');

            container.find('.error pre').text(err.message);

            console.error(err);
        } else {
            container
                .removeClass('with-error')
                .addClass('with-diagram');
        }
    });
}

function saveSVG(done) {
    modeler.saveSVG(done);
}

function saveDiagram(done) {

    modeler.saveXML({ format: true }, function(err, xml) {
        done(err, xml);
    });
}

function registerFileDrop(container, callback) {

    function handleFileSelect(e) {
        e.stopPropagation();
        e.preventDefault();

        var files = e.dataTransfer.files;

        var file = files[0];

        var reader = new FileReader();

        reader.onload = function(e) {

            var xml = e.target.result;

            callback(xml);
        };

        reader.readAsText(file);
    }

    function handleDragOver(e) {
        e.stopPropagation();
        e.preventDefault();

        e.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
    }

    container.get(0).addEventListener('dragover', handleDragOver, false);
    container.get(0).addEventListener('drop', handleFileSelect, false);
}


////// file drag / drop ///////////////////////

// check file api availability
if (!window.FileList || !window.FileReader) {
    window.alert(
        'Looks like you use an older browser that does not support drag and drop. ' +
        'Try using Chrome, Firefox or the Internet Explorer > 10.');
} else {
    registerFileDrop(container, openDiagram);
}

// bootstrap diagram functions

$(function() {

    $('#js-create-diagram').click(function(e) {
        e.stopPropagation();
        e.preventDefault();

        createNewDiagram();
    });

    var downloadLink = $('#js-download-diagram');
    var downloadSvgLink = $('#js-download-svg');

    $('.buttons a').click(function(e) {
        if (!$(this).is('.active')) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    function setEncoded(link, name, data) {
        var encodedData = encodeURIComponent(data);

        if (data) {
            link.addClass('active').attr({
                'href': 'data:application/bpmn20-xml;charset=UTF-8,' + encodedData,
                'download': name
            });
        } else {
            link.removeClass('active');
        }
    }

    var exportArtifacts = debounce(function() {

        saveSVG(function(err, svg) {
            setEncoded(downloadSvgLink, document.querySelector('title').innerText+'.svg', err ? null : svg);
        });

        saveDiagram(function(err, xml) {
            setEncoded(downloadLink, document.querySelector('title').innerText+'.bpmn', err ? null : xml);
        });
    }, 500);

    modeler.on('commandStack.changed', exportArtifacts);
    // modeler.on('attach', exportArtifacts);
    // modeler.on('commandStack.canExecute', exportArtifacts);
    // modeler.on('commandStack.connection.layout.executed', exportArtifacts);
    // modeler.on('canvas.init', exportArtifacts);

    downloadLink.click(() => {
        saveDiagram(function(err, xml) {
            setEncoded(downloadLink, document.querySelector('title').innerText+'.bpmn', err ? null : xml);
        });
    });
    downloadSvgLink.click(() => {
        saveSVG(function(err, svg) {
            setEncoded(downloadSvgLink, document.querySelector('title').innerText+'.svg', err ? null : svg);
        });
    });
});