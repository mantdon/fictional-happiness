import React from 'react';
import {render} from 'react-dom';

/*class App extends React.Component {
    render () {
        return <p> Hello React!</p>;
    }
}*/



const element = <h1>Hello, world</h1>;
require("../img/logo.png");
require("../img/login/login-icon.png");
render(element, document.getElementById('app'));

