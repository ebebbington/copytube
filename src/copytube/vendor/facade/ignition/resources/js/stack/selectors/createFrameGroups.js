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
var helpers_1 = require("../helpers");
var dummyFrameGroup = {
    type: 'application',
    relative_file: '',
    expanded: true,
    frames: [],
};
function createFrameGroups(_a) {
    var frames = _a.frames, selected = _a.selected, expanded = _a.expanded;
    return frames.reduce(function (frameGroups, current, i) {
        var context = {
            current: current,
            previous: frameGroups[frameGroups.length - 1] || dummyFrameGroup,
            isFirstFrame: i === 0,
            frameNumber: frames.length - i,
            expanded: expanded,
            selected: selected,
        };
        if (context.expanded.includes(context.frameNumber)) {
            return frameGroups.concat(parseExpandedFrame(context));
        }
        return frameGroups.concat(parseCollapsedFrame(context));
    }, []);
}
exports.default = createFrameGroups;
function parseExpandedFrame(context) {
    if (context.current.relative_file !== context.previous.relative_file) {
        return [
            {
                type: helpers_1.getFrameType(context.current),
                relative_file: context.current.relative_file,
                expanded: true,
                frames: [
                    __assign(__assign({}, context.current), { frame_number: context.frameNumber, selected: context.selected === context.frameNumber }),
                ],
            },
        ];
    }
    context.previous.frames.push(__assign(__assign({}, context.current), { frame_number: context.frameNumber, selected: context.selected === context.frameNumber }));
    return [];
}
function parseCollapsedFrame(context) {
    var type = helpers_1.getFrameType(context.current);
    if (!context.previous.expanded && type === context.previous.type) {
        // Mutate the previous result. It's not pretty, makes the general flow of the program less
        // complex because we kan keep the result list append-only.
        context.previous.frames.push(__assign(__assign({}, context.current), { selected: false, frame_number: context.frameNumber }));
        return [];
    }
    return [
        {
            type: type,
            relative_file: context.current.relative_file,
            expanded: false,
            frames: [
                __assign(__assign({}, context.current), { frame_number: context.frameNumber, selected: context.selected === context.frameNumber }),
            ],
        },
    ];
}
