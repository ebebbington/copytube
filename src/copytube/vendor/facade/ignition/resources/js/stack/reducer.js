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
var __spreadArrays = (this && this.__spreadArrays) || function () {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var uniq_1 = __importDefault(require("lodash/uniq"));
var helpers_1 = require("./helpers");
function stackReducer(state, action) {
    switch (action.type) {
        case 'EXPAND_FRAMES': {
            var expanded = uniq_1.default(__spreadArrays(state.expanded, action.frames));
            return __assign(__assign({}, state), { expanded: expanded });
        }
        case 'EXPAND_ALL_VENDOR_FRAMES': {
            var knownFrameNumbers = helpers_1.addFrameNumbers(state.frames)
                .filter(function (frame) { return frame.relative_file !== 'unknown'; })
                .map(function (frame) { return frame.frame_number; });
            return __assign(__assign({}, state), { expanded: knownFrameNumbers });
        }
        case 'COLLAPSE_ALL_VENDOR_FRAMES': {
            var applicationFrameNumbers = helpers_1.addFrameNumbers(state.frames)
                .filter(function (frame) {
                return !frame.relative_file.startsWith('vendor/') &&
                    frame.relative_file !== 'unknown';
            })
                .map(function (frame) { return frame.frame_number; });
            var expanded = uniq_1.default(__spreadArrays(applicationFrameNumbers, [state.frames.length]));
            return __assign(__assign({}, state), { expanded: expanded });
        }
        case 'SELECT_FRAME': {
            var selectableFrameNumbers = helpers_1.addFrameNumbers(state.frames)
                .filter(function (frame) { return frame.relative_file !== 'unknown'; })
                .map(function (frame) { return frame.frame_number; });
            var selected = selectableFrameNumbers.includes(action.frame)
                ? action.frame
                : state.selected;
            var expanded = uniq_1.default(__spreadArrays(state.expanded, [selected]));
            return __assign(__assign({}, state), { expanded: expanded, selected: selected });
        }
        case 'SELECT_NEXT_FRAME': {
            var selectableFrameNumbers = helpers_1.addFrameNumbers(state.frames)
                .filter(function (frame) { return frame.relative_file !== 'unknown'; })
                .map(function (frame) { return frame.frame_number; });
            var selectedIndex = selectableFrameNumbers.indexOf(state.selected);
            var selected = selectedIndex === selectableFrameNumbers.length - 1
                ? selectableFrameNumbers[0]
                : selectableFrameNumbers[selectedIndex + 1];
            var expanded = uniq_1.default(__spreadArrays(state.expanded, [selected]));
            return __assign(__assign({}, state), { expanded: expanded, selected: selected });
        }
        case 'SELECT_PREVIOUS_FRAME': {
            var selectableFrameNumbers = helpers_1.addFrameNumbers(state.frames)
                .filter(function (frame) { return frame.relative_file !== 'unknown'; })
                .map(function (frame) { return frame.frame_number; });
            var selectedIndex = selectableFrameNumbers.indexOf(state.selected);
            var selected = selectedIndex === 0
                ? selectableFrameNumbers[selectableFrameNumbers.length - 1]
                : selectableFrameNumbers[selectedIndex - 1];
            var expanded = uniq_1.default(__spreadArrays(state.expanded, [selected]));
            return __assign(__assign({}, state), { expanded: expanded, selected: selected });
        }
        default: {
            return state;
        }
    }
}
exports.default = stackReducer;
