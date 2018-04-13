import React from 'react';
import {render} from 'react-dom';

export default class TimeOption extends React.Component {

    constructor(props) {
        super(props);

        this.handleClick = this.handleClick.bind(this);
    }
    handleClick()
    {
        this.props.onClick(this.props.time);
    }


    render(){
        return <div onClick={this.handleClick} className={'Option'}>{this.props.time}</div>
    }
}