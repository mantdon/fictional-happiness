import React from 'react';
import {render} from 'react-dom';

export default class TimeSelection extends React.Component {

    render(){
        return (
            <div className={'timeSelectionContainer'}>
                <span onClick={this.props.onExit} className={'close'} aria-hidden="true">&times;</span>
            </div>
        );
    }
}