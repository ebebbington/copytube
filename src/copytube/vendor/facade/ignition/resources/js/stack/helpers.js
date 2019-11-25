"use strict";
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
function addFrameNumbers(frames) {
    return frames.map(function (frame, i) { return (__assign(__assign({}, frame), { frame_number: frames.length - i })); });
}
exports.addFrameNumbers = addFrameNumbers;
function getFrameType(frame) {
    if (frame.relative_file.startsWith('vendor/')) {
        return 'vendor';
    }
    if (frame.relative_file === 'unknown') {
        return 'unknown';
    }
    return 'application';
}
exports.getFrameType = getFrameType;
