import React from 'react';
import {render} from 'react-dom';
import Datetime from 'react-datetime';
import 'moment/locale/lt';

export default class DateSelection extends React.Component {

    render(){
        return <Datetime input={false}/>;
    }
}