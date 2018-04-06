import React from 'react';
import {render} from 'react-dom';
import VehicleSelection from './VehicleSelection';
import ServicesSelection from './ServicesSelection';
import Confirmation from "./Confirmation";

class OrderPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            step: 1,
            totalSteps: 3,
            dialog: null,
            isBackwardsActive: false,
            isForwardsActive: false,
            selectedVehicle: null
        };
        this.nextStep = this.nextStep.bind(this);
        this.previousStep = this.previousStep.bind(this);
        this.selectVehicle = this.selectVehicle.bind(this);
    }

    selectVehicle(vehicle)
    {
        this.setState({
            selectedVehicle: vehicle
        });
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
        this.proceedStep(1);
    }

    previousStep()
    {
        this.proceedStep(-1);
    }

    proceedStep(direction)
    {
        this.setState({
            step : this.state.step + direction
        }, () => {
            this.changeDialog(this.state.step);
            this.updateButtonsStates();
        });
    }

    getCurrentDialog(step)
    {
        switch (step) {
            case 1:
                return <VehicleSelection selectVehicle={this.selectVehicle} nextStep={this.nextStep}/>;
            case 2:
                return <ServicesSelection nextStep={this.nextStep}/>;
            case 3:
                return <Confirmation vehicle={this.state.selectedVehicle} services={this.state.selectedServices}/>
        }
    }

    formForwardsButton()
    {
        return <div className={'orderDialogButton ' + this.disableButton(this.state.isBackwardsActive)} onClick={this.previousStep}>
            Atgal
        </div>;
    }

    formBackwardsButton()
    {
        return <div className={'orderDialogButton align-right ' + this.disableButton(this.state.isForwardsActive)} onClick={this.nextStep}>
            Sekantis
        </div>;
    }

    updateButtonsStates()
    {
        if(this.state.step === 1)
        {
            this.setState({
                isBackwardsActive: false,
                isForwardsActive: false
            });
        }
        else{
            this.setState({
                isBackwardsActive: true,
                isForwardsActive: true
            });
        }

        if(this.state.step === this.state.totalSteps) {
            this.setState({
                isForwardsActive: false
            });
        }
    }

    disableButton(active)
    {
        if(!active)
            return 'orderButtonDisabled';
        return '';
    }

    render() {
        return <div className={'orderDialog'}>
            <div className={'orderDialogButtonsContainer'}>
                {this.formForwardsButton()}
                {this.formBackwardsButton()}
            </div>

            {this.state.dialog}
        </div>;
    }
}

render(
    <OrderPage/>,
    document.getElementById('VehicleSelection')
);
