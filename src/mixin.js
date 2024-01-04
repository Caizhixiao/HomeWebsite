// import axios from "axios";

export const mixin = {
    methods:{
        getCookie(cname) {
            let name = cname + "=";
            let ca = document.cookie.split(';');
            for(let i=0; i<ca.length; i++) {
                let c = ca[i].trim();
                if (c.indexOf(name) === 0) return c.substring(name.length,c.length);
            }
            return "";
        },
        setCookie(cname,cvalue,exdays){
            let d = new Date();
            d.setTime(d.getTime()+(exdays*24*60*60*1000));
            let expires = "expires="+d.toGMTString();
            document.cookie = cname + "=" + cvalue + "; " + expires;
        },
        clearCookies() {
            let cookies = document.cookie.split("; ");
            for (let i = 0; i < cookies.length; i++) {
                let cookie = cookies[i];
                let eqPos = cookie.indexOf("=");
                let name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
            }
        }
    },

}