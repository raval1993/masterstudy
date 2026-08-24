// External Dependencies
import React, { Component, Fragment } from 'react';

// Internal Dependencies
import './style.scss';


class Courses_Categories extends Component {

  static slug = 'dmslms_courses_categories';

  render() {
    return (
      <h1>
        <Fragment> 
          {this.props.title}
        </Fragment>
      </h1>
    );
  }
}

export default Courses_Categories;