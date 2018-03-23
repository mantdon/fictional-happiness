import React from 'react';
import {render} from 'react-dom';

/*class App extends React.Component {
    render () {
        return <p> Hello React!</p>;
    }
}*/

require('bootstrap');
const element = <h1>Hello, world</h1>;
require("../img/logo.png");
render(element, document.getElementById('app'));

