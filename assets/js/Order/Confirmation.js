import React from 'react';
import {render} from 'react-dom';

export default class Confirmation extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            orderCompleted: false,
            errors: [],
            cost: 0
        };
        this.submitOrder = this.submitOrder.bind(this);
    }

    componentDidMount()
    {
        this.setState({
            cost: this.calculateCost(this.props.services)
        });
    }

    submitOrder()
    {
        let services = this.props.services;
        let vehicle = this.props.vehicle;

        fetch("/order/submit", {
            method: "POST",
            headers: {
                'Accept': 'application/json, text/plain, */*',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                services: services,
                vehicle: vehicle
            })
        })
            .then(res => res.json())
            .then(
                (result) => {
                    this.processResponse(result);
                },
                (error) => {
                    this.setState({
                        error
                    });
                }
            )
    }

    calculateCost(services)
    {
        let cost = 0;
        services.forEach((service) => {
            cost += parseFloat(service['price']);
        });
        return cost;
    }

    processResponse(result) {
        if (result['errors'])
            this.setState({
                errors: result['errors']
            });
        else {
            this.props.nextStep();
        }
    }

    render(){
        return <div className={'text-center'}>
            <div>kaina ${this.state.cost}</div>
            <div className={'orderDialogButton'} onClick={this.submitOrder}>Mokėti</div>
        </div>;
    }
}
