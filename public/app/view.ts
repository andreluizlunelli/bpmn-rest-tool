export namespace View {
    export class Loader {
        constructor(
            private modal: HTMLElement|undefined
            , private loader: HTMLElement|undefined
        ){
            this.modal = modal;
            this.loader = loader;
            if (typeof modal == 'undefined') {
                this.modal = document.createElement('div');
                this.modal.classList.add('modal-backdrop', 'fade', 'show');
            }
            if (typeof loader == 'undefined') {
                this.loader = document.createElement('div');
                this.loader.classList.add('lds-grid');
                for (let i = 0; i < 9; i++)
                    this.loader.append(document.createElement('div'));
            }
        }

        show(): void {
            let body = <HTMLBodyElement>document.querySelector('body');
            body.append(<HTMLElement>this.modal);
            body.append(<HTMLElement>this.loader);
        }
    }

    export class ViewEnviarArquivo {
        constructor(
            private form: HTMLFormElement
            , private loader: Loader
        ) {}

        eventOnSubmit(): void {
            let that = this;
            this.form.addEventListener('submit', event => {
                that.loader.show();
            });
        }
    }
}