// Obtain the root
const rootElement = document.getElementById('example')
// Create a ES6 class component
class ShoppingList extends React.Component {
// Use the render function to return JSX component
  render() {
    return (
      <div className="shopping-list">
        <h1>Shopping List for {this.props.name}</h1>
        <ul>
          <li>Instagiuiiram</li>
          <li>WhatsApp</li>
          <li>Oculus</li>
        </ul>
      </div>
    );
  }
}
// Create a function to wrap up your component
function App(){
  return(
    <div>
      <ShoppingList name="@luispagarcia on Dev.to!"/>
    </div>
  )
}


// Use the ReactDOM.render to show your component on the browser
ReactDOM.render(
  <App />,
  rootElement
)