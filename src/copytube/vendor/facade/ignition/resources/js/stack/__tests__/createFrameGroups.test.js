"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var createFlareErrorFrame_1 = __importDefault(require("./__helpers__/createFlareErrorFrame"));
var createFrameGroups_1 = __importDefault(require("../selectors/createFrameGroups"));
describe('createFrameGroups', function () {
    var state = {
        frames: [
            createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
            createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
            createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
        ],
        expanded: [3, 2, 1],
        selected: 3,
    };
    var frameGroups = createFrameGroups_1.default(state);
    test('it creates list items', function () {
        expect(frameGroups).toHaveLength(2);
        expect(frameGroups[0].type).toBe('application');
        expect(frameGroups[0].relative_file).toBe('a.php');
        expect(frameGroups[0].frames).toHaveLength(2);
        expect(frameGroups[1].type).toBe('application');
        expect(frameGroups[1].relative_file).toBe('b.php');
        expect(frameGroups[1].frames).toHaveLength(1);
    });
    test('it adds frame numbers', function () {
        expect(frameGroups[0].frames[0].frame_number).toBe(3);
        expect(frameGroups[0].frames[1].frame_number).toBe(2);
        expect(frameGroups[1].frames[0].frame_number).toBe(1);
    });
    test('it expands frame groups', function () {
        expect(frameGroups[0].expanded).toBe(true);
        expect(frameGroups[1].expanded).toBe(true);
    });
    test('it selects frames', function () {
        expect(frameGroups[0].frames[0].selected).toBe(true);
        expect(frameGroups[0].frames[1].selected).toBe(false);
        expect(frameGroups[1].frames[0].selected).toBe(false);
    });
    test('it collapses successive vendor frames', function () {
        state = {
            frames: [
                createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/b.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/c.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'd.php' }),
            ],
            expanded: [4, 1],
            selected: 4,
        };
        frameGroups = createFrameGroups_1.default(state);
        expect(frameGroups).toHaveLength(3);
        expect(frameGroups[0].type).toBe('application');
        expect(frameGroups[0].relative_file).toBe('a.php');
        expect(frameGroups[0].frames).toHaveLength(1);
        expect(frameGroups[0].expanded).toBe(true);
        expect(frameGroups[1].type).toBe('vendor');
        expect(frameGroups[1].relative_file).toBe('vendor/b.php');
        expect(frameGroups[1].frames).toHaveLength(2);
        expect(frameGroups[1].expanded).toBe(false);
        expect(frameGroups[2].type).toBe('application');
        expect(frameGroups[2].relative_file).toBe('d.php');
        expect(frameGroups[2].frames).toHaveLength(1);
        expect(frameGroups[2].expanded).toBe(true);
    });
    test('it collapses successive vendor frames', function () {
        state = {
            frames: [
                createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'unknown' }),
                createFlareErrorFrame_1.default({ relative_file: 'unknown' }),
                createFlareErrorFrame_1.default({ relative_file: 'd.php' }),
            ],
            expanded: [4, 1],
            selected: 4,
        };
        frameGroups = createFrameGroups_1.default(state);
        expect(frameGroups).toHaveLength(3);
        expect(frameGroups[0].type).toBe('application');
        expect(frameGroups[0].relative_file).toBe('a.php');
        expect(frameGroups[0].frames).toHaveLength(1);
        expect(frameGroups[0].expanded).toBe(true);
        expect(frameGroups[1].type).toBe('unknown');
        expect(frameGroups[1].relative_file).toBe('unknown');
        expect(frameGroups[1].frames).toHaveLength(2);
        expect(frameGroups[1].expanded).toBe(false);
        expect(frameGroups[2].type).toBe('application');
        expect(frameGroups[2].relative_file).toBe('d.php');
        expect(frameGroups[2].frames).toHaveLength(1);
        expect(frameGroups[2].expanded).toBe(true);
    });
});
