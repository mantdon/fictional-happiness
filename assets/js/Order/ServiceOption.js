import React from 'react';
import {render} from 'react-dom';

export default class ServiceOption extends React.Component {
    render(){
       return <div className={'Option'} onClick={this.props.nextStep}>
                    <div className={'serviceName'}> {this.props.service.name} </div>
                    <div className={'servicePrice'}>{this.props.service.price}</div>
                </div>;
    }
}