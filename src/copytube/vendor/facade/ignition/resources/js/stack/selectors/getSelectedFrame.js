"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var helpers_1 = require("../helpers");
function getSelectedFrame(state) {
    return helpers_1.addFrameNumbers(state.frames).find(function (frame) { return frame.frame_number === state.selected; });
}
exports.default = getSelectedFrame;
