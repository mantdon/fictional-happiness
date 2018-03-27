import React from 'react';
import {render} from 'react-dom';
import VehicleSelection from './VehicleSelection';
import ServicesSelection from './ServicesSelection';

class OrderPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {step: 1,
        dialog: null};
        this.nextStep = this.nextStep.bind(this);
    }

    componentWillMount()
    {
        this.changeDialog(this.state.step);
    }

    changeDialog(step)
    {
        this.setState({dialog: this.getCurrentDialog(step)});
    }

    nextStep()
    {
        this.setState({
            step : this.state.step + 1
        }, () => {this.changeDialog(this.state.step);});

    }

    getCurrentDialog(step)
    {
        switch (step) {
            case 1:
                return <VehicleSelection nextStep={this.nextStep}/>;
            case 2:
                return <ServicesSelection nextStep={this.nextStep}/>;
        }
    }

    render() {
        return <div className={'orderDialog'}>{this.state.dialog}</div>;
    }
}

render(
    <OrderPage/>,
    document.getElementById('VehicleSelection')
);
