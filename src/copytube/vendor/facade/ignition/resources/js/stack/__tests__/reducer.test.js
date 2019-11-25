"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var createFlareErrorFrame_1 = __importDefault(require("./__helpers__/createFlareErrorFrame"));
var reducer_1 = __importDefault(require("../reducer"));
describe('reducer', function () {
    describe('EXPAND_FRAMES', function () {
        var initialState = {
            frames: [
                createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/b.php' }),
            ],
            expanded: [2],
            selected: 2,
        };
        test('it can expand a frame', function () {
            var state = reducer_1.default(initialState, { type: 'EXPAND_FRAMES', frames: [1] });
            expect(state.expanded).toEqual([2, 1]);
        });
    });
    describe('EXPAND_ALL_VENDOR_FRAMES', function () {
        var initialState = {
            frames: [
                createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/b.php' }),
                createFlareErrorFrame_1.default({ relative_file: 'vendor/c.php' }),
            ],
            expanded: [3],
            selected: 3,
        };
        test('it can expand all vendor frames', function () {
            var state = reducer_1.default(initialState, { type: 'EXPAND_ALL_VENDOR_FRAMES' });
            expect(state.expanded).toEqual([3, 2, 1]);
        });
    });
    describe('COLLAPSE_ALL_VENDOR_FRAMES', function () {
        test('it can collapse all vendor frames', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'vendor/b.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'vendor/c.php' }),
                ],
                expanded: [3, 2, 1],
                selected: 3,
            };
            var state = reducer_1.default(initialState, { type: 'COLLAPSE_ALL_VENDOR_FRAMES' });
            expect(state.expanded).toEqual([3]);
        });
    });
    describe('SELECT_FRAME', function () {
        test('it can select a frame', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 2,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_FRAME', frame: 1 });
            expect(state.selected).toBe(1);
        });
        test('it expands a selected frame', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'vendor/b.php' }),
                ],
                expanded: [2],
                selected: 2,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_FRAME', frame: 1 });
            expect(state.selected).toBe(1);
            expect(state.expanded).toEqual([2, 1]);
        });
        test('it keeps the selected frame if a non existing frame is selected', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 2,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_FRAME', frame: 3 });
            expect(state.selected).toBe(2);
        });
    });
    describe('SELECT_NEXT_FRAME', function () {
        test('it can select the next frame', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 2,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_NEXT_FRAME' });
            expect(state.selected).toBe(1);
        });
        test('it selects the first frame when there is no next frame', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 1,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_NEXT_FRAME' });
            expect(state.selected).toBe(2);
        });
        test('it skips unknown frames', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'unknown' }),
                    createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
                ],
                expanded: [3, 1],
                selected: 3,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_NEXT_FRAME' });
            expect(state.selected).toBe(1);
        });
    });
    describe('SELECT_PREVIOUS_FRAME', function () {
        test('it can select the previous frame', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 1,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_PREVIOUS_FRAME' });
            expect(state.selected).toBe(2);
        });
        test('it selects the last frame when there is no previous frame', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 2,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_PREVIOUS_FRAME' });
            expect(state.selected).toBe(1);
        });
        test('it skips unknown frames', function () {
            var initialState = {
                frames: [
                    createFlareErrorFrame_1.default({ relative_file: 'a.php' }),
                    createFlareErrorFrame_1.default({ relative_file: 'unknown' }),
                    createFlareErrorFrame_1.default({ relative_file: 'b.php' }),
                ],
                expanded: [3, 1],
                selected: 1,
            };
            var state = reducer_1.default(initialState, { type: 'SELECT_PREVIOUS_FRAME' });
            expect(state.selected).toBe(3);
        });
    });
});
