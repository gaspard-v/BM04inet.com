"use strict";
let i = 0;
let changeTitle = setInterval(function () { document.title = i; i++; }, 1000);
const btnLogout = document.getElementById("logout");
const btnAllLogout = document.getElementById("allLogout");
const testTxtAjax = document.getElementById("txtajax");
const readFile = document.getElementById("readFile");


function errorHanlder(error) {
    this.error = error;
}

errorHanlder.prototype.toString = function () {
    let retour = "";
    //retour += `url: "${url}"\n`;
    //retour += `Send Message: "${isok}"\n`;
    //retour += `Excepted Message: "${exceptedOk}"\n`;
    retour += `readyState: "${this.readyState}"\n`;
    retour += `status: "${this.status}"\n`;
    retour += `statusText: "${this.statusText}"\n`;
    retour += `responseText: ${this.responseText}`;
    return retour;
}

async function ajaxOK() {
    let retour = false;
    let error = "";
    const url = "ajax.php";
    const isok = "! KO";
    const exceptedOk = "OK !"
    await $.ajax({
        url: url,
        data: {
            isok: isok
        },
        success: function (result) {
            if (result === exceptedOk) retour = result;
            else {
                error = new Error(`Not excepted result in AJAX test connection :\n
                ULR: "${url}"\n
                Send Message: "${isok}"\n
                Excepted Message: "${exceptedOk}"\n
                Message Received: "${result}"`);
            }
        },
        error: function (e) {
            error = new errorHanlder(e);
        },
    });
    if (error) throw error;
    return retour;
}
class readJsonFile {
    constructor(htmlModif) {
        this.url = "ajax.php"
        this.htmlModif = htmlModif;
        this.json = [];
    }
    async getJsonFile(element) {
        let retour = "";
        let error = "";
        await $.ajax({
            url: this.url,
            data: {
                jsonfile: element,
            },
            success: ((result) => {
                if(result)
                {
                    try {
                        retour = JSON.parse(result);
                    }
                    catch (err) {
                        error = err;
                    }
                }
            }),
            error: ((err) => {
                error = new errorHanlder(err);
            })
        });
        if (error) throw error;
        return retour;
    }
    async modifHtml()
    {
        let error = "";
        const that = this;
        let element = "all";
        if(this.json)
        {
            element = this.json.length;
        }
        await this.getJsonFile(element)
        .then((json) => {
            if(json)
            {
                that.json = that.json.concat(json);
                json.forEach(elem => {
                    that.htmlModif.innerHTML += `${elem.id}    ${elem.value}<br>`;
                });
            }
        })
        .catch(err => {
            error = err;
        });
        if(error) throw error;
        return;
    }
}


if (btnLogout) {
    btnLogout.addEventListener('click', (() => {
        window.location.href = "logout.php?type=single";
    }));
}
if (btnAllLogout) {
    btnAllLogout.addEventListener('click', (() => {
        window.location.href = "logout.php?type=all";
    }));
}
if (testTxtAjax) {
    testTxtAjax.innerHTML = "Waiting...";
    ajaxOK()
        .then((result) => {
            testTxtAjax.innerHTML = `${result}`;
        })
        .catch((err) => {
            testTxtAjax.innerHTML = `<strong>Erreur test de connection AJAX<br>${err.toString()}</strong>\n`;
            console.error(`Erreur test de connection Ajax: `);
            console.error(err);
        })
}

if (readFile) {
    let inter = null;
    const readJson = new readJsonFile(readFile);
    const errorHandler = function(err)
    {
        console.error("Erreur Ajax: ");
        console.error(err);
    };
    const handlerModifHtml = function() {
        readJson.modifHtml()
        .catch(err => {errorHandler(err);
        if(inter)
        {
            clearInterval(inter);
        }
        });
    };
    ajaxOK()
        .then(() => {
            handlerModifHtml();
            inter = setInterval(handlerModifHtml.bind(readJson, null), 5000);
        })
        .catch((err) => {errorHandler(err);});
}