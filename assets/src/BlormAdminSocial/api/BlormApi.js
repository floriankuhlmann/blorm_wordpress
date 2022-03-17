import axios from 'axios';

const httpHeaders = {
    'Content-Type': 'application/json',
    'X-WP-Nonce': restapiVars.nonce,
};

export class User {

    id = "";
    name = "";
    blormHandle = "";
    photoUrl  = "";
    websiteName = "";
    websiteUrl = "";
    websiteId = "";
    category = "";
    websiteType = "";

    constructor(id, name, blormHandle, photoUrl, websiteName, websiteUrl, websiteId, category, websiteType) {
        this.id = id;
        this.name = name;
        this.blormHandle = blormHandle;
        this.photoUrl = photoUrl;
        this.websiteName = websiteName;
        this.websiteUrl = websiteUrl;
        this.websiteId = websiteId;
        this.category = category;
        this.websiteType = websiteType;
    }
}


export function getBlormApiData (url) {
    let promiseObj = new Promise( function( fullfill, reject) {
        axios.get( url,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,
                },
            }
        ).then(response => {
            fullfill(response);
        }).catch(error => {
            logMsgToCons("getBlormApiData error", error);
            reject(error);
        });
    });
    return promiseObj;
}

function logMsgToCons (logmessage, e) {
    console.log("message: " + logmessage);
    console.log(e);
}