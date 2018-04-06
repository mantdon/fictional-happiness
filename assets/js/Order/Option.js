import React from 'react';
import {render} from 'react-dom';

export default class Option extends React.Component {

    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
    }

    handleClick()
    {
        this.props.selectVehicle(this.props.vehicle);
        this.props.nextStep();
    }

    render() {
        return <div className={'Option'} onClick={this.handleClick}> {this.props.vehicle.plateNumber} </div>;
    }
}