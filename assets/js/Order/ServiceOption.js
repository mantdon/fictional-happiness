import React from 'react';
import {render} from 'react-dom';

export default class ServiceOption extends React.Component {
    render(){
       return <div className={'vehicleOption'} onClick={this.props.nextStep}>
                    <div> {this.props.service.name} </div>
                    <div>{this.props.service.price}</div>
                </div>;
    }
}