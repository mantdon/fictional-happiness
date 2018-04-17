import React from 'react';
import {render} from 'react-dom';
import { PulseLoader } from 'react-spinners';

export default class Loader extends React.Component {
    render(){
        return <PulseLoader color={'#AFAFAF'}/>;
    }
}