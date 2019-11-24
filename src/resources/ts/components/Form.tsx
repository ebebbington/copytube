export {};
// Define the props
const props: object = {
  title: 'Some dynamic title'
}


const a: string = 'jj'

/**
 * Form
 *
 * The base form block any implemented form should use.
 * This would be used for types like a register from or login form,
 * and the required elememts needed within would be added - such as
 * username input, email input, select field
 *
 * To use this component, a new component should be create e.g. LoginForm,
 * and that login form should extend this, and implement the required components.
 * So take for example, the LoginForm would implement this component, the email component,
 * and the password component.
 * 
 * To make this file a TS file, i literally just modified to ext from .js to .tsx
 * 
 * To remove the error of duplicate function implementations, i just added the following and it seemed to fix it
 *    export {};
 * 
 * To fix the IDE error 'property xyz does not exist on type readonly...., you must add an interface to define those props eg
 *    interface IFormProps {
 *      exampleProp1?: string
 *    }
 *    ... extends ....Component<IFormProps>
 *
 * @type {HTMLElement}
 *
 * @method handlechange   Handles the changed state of the input fields
 * @method handleClick    Handles click of submit button
 * @method render         Displays the form
 */
class GenericForm extends React.Component {

  state: object
  exampleProp1: string
  exampleProp2: string
  
  // Always call super() right away
  /**
   * Constructor
   *
   * Always call super(props) right away
   * @param props
   */
  constructor(props: object) {
    super(props);
    this.state = {value: ''};
    this.exampleProp1 = this.props.exampleProp1
    this.exampleProp2 = this.props.exampleProp2
    console.log([this.exampleProp1, this.exampleProp2])
    this.handleChange = this.handleChange.bind(this);
    this.handleClick = this.handleClick.bind(this);
  }

  /**
   * Save the value in the event object
   *
   * @param event
   */
  handleChange(event) {
    this.setState({value: event.target.value});
  }

  /**
   * Display last edited field value
   *
   * @param event
   */
  handleClick(event) {
    alert('The submit was clicked, and the last added value was: ' + this.state.value);
    event.preventDefault();
  }

  render() {
    return (
      <form>
        <img src="img/copytube_logo.png"/>
        <legend>Register</legend>
        <fieldset>
          <legend>Information</legend>
          <div className="notify"></div>
          <label>
            Username<label className="required-field">*</label>
            <input className="form-control" name="username" placeholder="Jane Doe" type="text" onChange={this.handleChange} required/>
          </label>
          <label>
            Email<label className="required-field">*</label>
            <input className="form-control" name="email" placeholder="jane.doe@hotmail.com" type="text" onChange={this.handleChange} required/>
          </label>
          <label>
            Password<label className="required-field">*</label>
            <input className="form-control" name="password" placeholder="Enter a password" type="password" onChange={this.handleChange} required/>
          </label>
          <button type="button" className="btn btn-primary" onClick={this.handleClick}>Submit</button>
        </fieldset>
      </form>
    )
  }
}

// Create a function to wrap up your component
/**
 * Grab the form component
 *
 * @returns {*}
 * @constructor
 */
// Passing in a property here can be used in the component
function GetForm () {
  return(
    <div>
      <GenericForm exampleProp1="I am a valid property value!"/>
    </div>
  )
}

// Use the ReactDOM.render to show your component on the browser
ReactDOM.render(
  // Passing in a property here isn't accessible(?) inside the component
  <GetForm exampleProp1="I show up as undefined!" />,
  document.getElementById('form-container')
)