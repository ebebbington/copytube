"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var createFlareErrorFrame_1 = __importDefault(require("./__helpers__/createFlareErrorFrame"));
var getSelectedFrame_1 = __importDefault(require("../selectors/getSelectedFrame"));
describe('getSelectedFrame', function () {
    test('it can get the selected frame', function () {
        var state = {
            frames: [
                createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
            ],
            expanded: [2, 1],
            selected: 2,
        };
        expect(getSelectedFrame_1.default(state).relative_file).toBe('a.php');
    });
});
