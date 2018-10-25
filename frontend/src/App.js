import React, { Component } from 'react';
import './App.css';

//creating component that holds table with id,title,content,createdAt
class EntriesList extends Component {
  render() {
    return (
      <table className="Entries-list">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Content</th>
            <th>Created at</th>
            <th></th>
            <th></th>
          </tr>
        </thead>

        <tbody>
          {this.props.entries.map(entry => (
            <tr key={entry.entryID}
              data-id={entry.entryID}
              data-title={entry.title}
              data-content={entry.content}>

              <td>{entry.entryID}</td>
              <td>{entry.title}</td>
              <td>{entry.content}</td>
              <td>{entry.createdAt}</td>
              <td><button onClick={this.props.onRemoveEntry}>❌</button></td>
              <td><button onClick={this.props.onEditEntry}>✍🏽</button></td>
            </tr>
          ))}
        </tbody>
      </table>
    )
  }
}

class CreateEntryForm extends Component {
  render() {
    return (
      <div id="submitPost">
        <form onSubmit={this.props.isEditing ? this.props.onUpdateEntry : this.props.onAddEntry}>

            <label>
              Title
              <input type="text"
                name="title"
                value={this.props.title}
                onChange={this.props.onFieldChange}
              />
            </label>

            <label>
              Content
              <textarea name="content" cols="30" rows="10"
                value={this.props.content}
                onChange={this.props.onFieldChange}
              />
            </label>

            {this.props.isEditing === true && (
              <button type="submit">Update</button>
            )}

            {this.props.isEditing === false && (
              <button type="submit">Create entry</button>
            )}
        </form>
      </div>
    )
  }
}

//creating component with an state that include 
//an empty array with id,title and content
class App extends Component {
  state = {
    entries: [],
    editing: {
      id: 0,
      title: '',
      content: '',
    },
    isEditing: false
  }

  componentDidMount() {
    this.apiFetchEntries();
  }

  //fetches 20 of the entries
  apiFetchEntries = () => {
    fetch('http://localhost:4000/api/entries?limit=20')
    .then((response) => response.json())
    .then((json) => this.setState({entries: json.data}));
  }
//create entry
  apiCreateEntry = (entry) => {
    fetch('http://localhost:4000/api/entries', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(entry)
    })
    .then(() => this.apiFetchEntries());
  }
//deletes entry
  apiDeleteEntry = (id) => {
    fetch(`http://localhost:4000/api/entries/${id}`, {
      method: 'DELETE',
      headers: {'Content-Type':'application/json'},
    })
    .then(() => this.apiFetchEntries());
  }
  //updates enrry
  apiUpdateEntry = (id, entry) => {
    fetch(`http://localhost:4000/api/entries/${id}`, {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      method: 'PATCH',                                                              
      body: JSON.stringify( entry )                                        
    })   
    .then(response => response.json())
    .then(console.log); 
  }
  //event to create a entry
  onAddEntry = (event) => {
    event.preventDefault()

    const entry = this.state.editing

    if (! entry.title.length > 0 || ! entry.content.length > 0) {
      return alert('Invalid input')
    }

    this.apiCreateEntry(entry)

    this.setState({editing:{
      title:"",
      content:""
    }})
  }
 //event to delete entry
  onRemoveEntry = (event) => {
    // Get the entries ID from the DOM's data-id attribute.
    const id = event.target.parentElement.parentElement.dataset.id

    this.apiDeleteEntry(id)
  }
  //event to edit entry
  onEditEntry = (event) => {
    const {id, content, title } = event.target.parentElement.parentElement.dataset

    const newEntry = {
      id: id,
      title: title,
      content: content
    }

    const newState = Object.assign({}, this.state)

    newState.isEditing = true
    newState.editing = newEntry

    this.setState(newState)

    this.apiUpdateEntry(id, newEntry)
  }
//event to update entry
  onUpdateEntry = (event) => {
    event.preventDefault()
    const newState = Object.assign({}, this.state)
    newState.isEditing = false
    this.setState(newState)

    const entry = this.state.editing

    this.apiUpdateEntry(entry.id, entry)
    this.apiFetchEntries();

    this.setState({editing:{
      title:"",
      content:""
    }})
  }

  onFieldChange = (event) => {
    const field = event.target.name

    const newState = Object.assign({}, this.state)

    newState.editing[field] = event.target.value

    this.setState(newState)
  }

  render() {
    return (
      <div className="App">
        <EntriesList
          entries={this.state.entries}
          onRemoveEntry={this.onRemoveEntry}
          onEditEntry={this.onEditEntry}
          />
        
        <CreateEntryForm
          onFieldChange={this.onFieldChange}
          title={this.state.editing.title}
          content={this.state.editing.content}
          onAddEntry={this.onAddEntry}
          onUpdateEntry={this.onUpdateEntry}
          isEditing={this.state.isEditing}  />
      </div>
    );
  }
}

export default App;