import React from 'react';
import {render} from 'react-dom';

export default class TimeOption extends React.Component {

    render(){
        return <div className={'Option'}>{this.props.time}</div>
    }
}