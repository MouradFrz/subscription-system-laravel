import React, { useEffect } from "react";
import axios from "axios";
axios.defaults.withCredentials = true;
function Hello(props) {
    useEffect(() => {
        // axios.get("http://localhost:8000/api/user", {
        //     // headers: {
        //     //     "X-CSRF-TOKEN": document
        //     //         .querySelector('meta[name="csrf-token"]')
        //     //         .getAttribute("content"),
        //     // },
        // });
    }, []);
    return <div>Hello component</div>;
}

export default Hello;
