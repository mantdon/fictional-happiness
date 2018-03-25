import React from 'react';
import {render} from 'react-dom';

export default class VehicleOption extends React.Component {
    render() {
        return <option> {this.props.vehicle.plateNumber} </option>;
    }
}