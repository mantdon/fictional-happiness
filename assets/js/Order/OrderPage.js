import React from 'react';
import {render} from 'react-dom';
import VehicleSelection from './VehicleSelection';
import ServiceSelection from './ServiceSelection';

class OrderPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {step: 1};
        this.nextStep = this.nextStep.bind(this);
    }

    nextStep()
    {
        this.setState({
            step : this.state.step + 1
        })
    }

    getCurrentDialog()
    {
        switch (this.state.step) {
            case 1:
                return <VehicleSelection nextStep={this.nextStep}/>;
            case 2:
                return <ServiceSelection nextStep={this.nextStep()}/>;
        }
    }

    render() {
        return <div className={'orderDialog'}>{this.getCurrentDialog()}</div>;
    }
}

render(
    <OrderPage/>,
    document.getElementById('VehicleSelection')
);
