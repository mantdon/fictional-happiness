import React from 'react';
import {render} from 'react-dom';

export default class Confirmation extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            orderCompleted: false,
            errors: []
        };
        this.submitOrder = this.submitOrder.bind(this);
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
        return <div onClick={this.submitOrder}>Mokėti</div>;
    }
}
