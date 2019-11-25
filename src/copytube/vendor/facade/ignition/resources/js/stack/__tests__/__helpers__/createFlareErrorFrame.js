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
function createFlareErrorFrame(attributes) {
    return __assign({ id: 541, file: '/Users/sebastiandedeyne/Sites/flare.laravel.com/database/faker/ExceptionProvider.php', relative_file: 'database/faker/ExceptionProvider.php', line_number: 35, class: 'ExceptionProvider', method: 'exception', code_snippet: {
            '35': '        return Stacktrace::createForThrowable($this->exception())->toArray();',
        } }, attributes);
}
exports.default = createFlareErrorFrame;
