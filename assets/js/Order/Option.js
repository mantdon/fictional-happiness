import React from 'react';
import {render} from 'react-dom';

export default class Option extends React.Component {

    render() {
        return <div className={'Option'} onClick={this.props.nextStep}> {this.props.vehicle.plateNumber} </div>;
    }
}