import React, { Component } from 'react';
import 'CreateEntryForm.js';
import './App.js';
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