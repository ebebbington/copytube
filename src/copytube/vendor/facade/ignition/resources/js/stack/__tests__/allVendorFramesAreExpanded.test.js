"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var createFlareErrorFrame_1 = __importDefault(require("./__helpers__/createFlareErrorFrame"));
var allVendorFramesAreExpanded_1 = __importDefault(require("../selectors/allVendorFramesAreExpanded"));
describe('allVendorFramesAreExpanded', function () {
    test('it can determine that all vendor frames are expanded', function () {
        var state = {
            frames: [
                createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/b.php' }),
            ],
            expanded: [3, 2, 1],
            selected: 3,
        };
        expect(allVendorFramesAreExpanded_1.default(state)).toBe(true);
    });
    test('it can determine that not all vendor frames are expanded', function () {
        var state = {
            frames: [
                createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/b.php' }),
            ],
            expanded: [3, 1],
            selected: 3,
        };
        expect(allVendorFramesAreExpanded_1.default(state)).toBe(false);
    });
});
