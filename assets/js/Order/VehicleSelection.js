import React from 'react';
import {render} from 'react-dom';
import VehicleOption from './Option';

export default class OrderPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {isLoaded: false,
        items: []};
    }

    componentDidMount() {
        fetch("/api")
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
                return items.map((vehicle, i) => <VehicleOption nextStep={this.props.nextStep} vehicle={vehicle} key={i} />);
    }

    render() {
        return  <div>
            <h1>Pasirinkite automobilį</h1>
                    <div className={'optionsContainer'}> {this.formOptions(this.state.items) }</div>
                </div>;
    }
}