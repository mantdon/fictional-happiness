import React from 'react';
import {render} from 'react-dom';
import VehicleSelection from './VehicleSelection';

class OrderPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {step: 1};
        this.nextStep = this.nextStep.bind(this);
    }

    nextStep()
    {
        this.setState({
            step : this.state.step - 1
        })
    }

    render() {
        switch (this.state.step) {
            case 1:
                return <VehicleSelection nextStep={this.nextStep}/>
        }
    }
}

render(
    <OrderPage/>,
    document.getElementById('VehicleSelection')
);
