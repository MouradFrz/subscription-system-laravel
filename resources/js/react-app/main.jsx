import { createRoot } from "react-dom/client";
import React from "react";
import { BrowserRouter, Link } from "react-router-dom";
import App from "./App";
if (document.getElementById("root")) {
    createRoot(document.getElementById("root")).render(
        <BrowserRouter>
            <Link to={"/contact"}>Contact</Link>
            <Link to={"/"}>Home</Link>
            <App />
        </BrowserRouter>
    );
}
