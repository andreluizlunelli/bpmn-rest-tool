import './bootstrap-dependencies';
import {View} from "./view";

let form = <HTMLFormElement> document.getElementById('send-bpmn');
if (form) {
    let view = new View.ViewEnviarArquivo(form, new View.Loader(undefined, undefined));
    view.eventOnSubmit();
}
