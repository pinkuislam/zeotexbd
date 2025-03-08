import React from "react";
import ziggyRoute from "ziggy";
import {Ziggy as Routes} from "../ziggy";
import {getBaseUrl} from "../utils/helpers";

export default function useZiggy() {
    const route = (name, params, absolute) => {
        return ziggyRoute(name, params, absolute, {...Routes, url: getBaseUrl()});
    }
    return {route};
};
