"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var helpers_1 = require("../helpers");
function allVendorFramesAreExpanded(state) {
    return helpers_1.addFrameNumbers(state.frames)
        .filter(function (frame) { return helpers_1.getFrameType(frame) === 'vendor'; })
        .every(function (frame) { return state.expanded.includes(frame.frame_number); });
}
exports.default = allVendorFramesAreExpanded;
