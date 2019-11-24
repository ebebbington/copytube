"use strict";
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var props = {
    title: 'Some dynamic title'
};
var a = 'jj';
var GenericForm = (function (_super) {
    __extends(GenericForm, _super);
    function GenericForm(props) {
        var _this = _super.call(this, props) || this;
        _this.exampleProp1 = _this.props.exampleProp1;
        _this.exampleProp2 = _this.props.exampleProp2;
        console.log([_this.exampleProp1, _this.exampleProp2]);
        _this.handleChange = _this.handleChange.bind(_this);
        _this.handleClick = _this.handleClick.bind(_this);
        return _this;
    }
    GenericForm.prototype.handleChange = function (event) {
        this.setState({ value: event.target.value });
    };
    GenericForm.prototype.handleClick = function (event) {
        alert('The submit was clicked, and the last added value was: ' + this.state.value);
        event.preventDefault();
    };
    GenericForm.prototype.render = function () {
        return (React.createElement("form", null,
            React.createElement("img", { src: "img/copytube_logo.png" }),
            React.createElement("legend", null, "Register"),
            React.createElement("fieldset", null,
                React.createElement("legend", null, "Information"),
                React.createElement("div", { className: "notify" }),
                React.createElement("label", null,
                    "Username",
                    React.createElement("label", { className: "required-field" }, "*"),
                    React.createElement("input", { className: "form-control", name: "username", placeholder: "Jane Doe", type: "text", onChange: this.handleChange, required: true })),
                React.createElement("label", null,
                    "Email",
                    React.createElement("label", { className: "required-field" }, "*"),
                    React.createElement("input", { className: "form-control", name: "email", placeholder: "jane.doe@hotmail.com", type: "text", onChange: this.handleChange, required: true })),
                React.createElement("label", null,
                    "Password",
                    React.createElement("label", { className: "required-field" }, "*"),
                    React.createElement("input", { className: "form-control", name: "password", placeholder: "Enter a password", type: "password", onChange: this.handleChange, required: true })),
                React.createElement("button", { type: "button", className: "btn btn-primary", onClick: this.handleClick }, "Submit"))));
    };
    return GenericForm;
}(React.Component));
function GetForm() {
    return (React.createElement("div", null,
        React.createElement(GenericForm, { exampleProp1: "I am a valid property value!" })));
}
ReactDOM.render(React.createElement(GetForm, null), document.getElementById('form-container'));
