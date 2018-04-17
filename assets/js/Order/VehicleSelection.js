import React from 'react';
import {render} from 'react-dom';
import VehicleOption from './Option';
import Loader from './Loader';

export default class OrderPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {isLoaded: false,
        items: []};
    }

    componentDidMount() {
        fetch("/user/vehicles/get", {
            credentials: "same-origin"
        })
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        isLoaded: true,
                        items: result
                    });
                },
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    formOptions(items)
    {
        if(this.state.isLoaded)
            if(items.length > 0)
                return items.map((vehicle, i) => <VehicleOption selectVehicle={this.props.selectVehicle} nextStep={this.props.nextStep} vehicle={vehicle} key={i} />);
    }

    render() {
        return  <div>
            <h1 className={'orderDialogLabel'}>Automobilio pasirinkimas</h1>
            {this.state.isLoaded ?
                <div className={'optionsContainer'}> {this.formOptions(this.state.items)}</div>
                :
                <Loader/>
            }
                </div>;
    }
}