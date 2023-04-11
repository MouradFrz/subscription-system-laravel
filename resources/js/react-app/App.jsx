import React from "react";
import Hello from "./components/Hello";
import { useRoutes } from "react-router-dom";
export default function App() {
    const myRoutes = useRoutes([
        {
            path: "/",
            element: <Hello />,
        },
        {
            path: "/contact",
            element: <><p>I am the contact page</p></>,
        },
    ]);
    return myRoutes;
}


