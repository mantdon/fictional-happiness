import React from 'react';
import {render} from 'react-dom';
import VehicleOption from './VehicleOption';

class VehicleSelect extends React.Component {

    formOptions(vehicles)
    {
        return vehicles.map((vehicle, i) => <VehicleOption vehicle={vehicle} key={i} />);
    }

    render() {
        return <select> {this.formOptions(JSON.parse(this.props.vehicles)) }</select>;
    }
}

render(
    <VehicleSelect vehicles={ vehicles }/>,
    document.getElementById('VehicleSelection')
);
