import React, { Component } from 'react';
import './App.js';
import './EntriesList.js';
import './App.css';

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
  

  