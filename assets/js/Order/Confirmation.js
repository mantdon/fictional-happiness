import React from 'react';
import {render} from 'react-dom';
import Loader from "./Loader";

export default class Confirmation extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            orderCompleted: false,
            errors: [],
            cost: 0,
            isSubmitted: false
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
        this.setState({
            isSubmitted: true
        });

        let services = this.props.services;
        let vehicle = this.props.vehicle;
        let date = this.props.date.format('YYYY-MM-DD HH:mm');
        
        fetch("/order/submit", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                'Accept': 'application/json, text/plain, */*',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                services: services,
                vehicle: vehicle,
                date: date
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
            <h1 className={'orderDialogLabel'}>Užsakymo patvirtinimas</h1>
            <div>kaina ${this.state.cost}</div>
            {!this.state.isSubmitted ?
                <div className={'orderDialogButton'} onClick={this.submitOrder}>Mokėti</div>
                :
                <Loader/>
            }
        </div>;
    }
}
