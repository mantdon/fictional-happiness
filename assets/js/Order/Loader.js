import React from 'react';
import {render} from 'react-dom';
import { PulseLoader } from 'react-spinners';

export default class Loader extends React.Component {
    render(){
        return <div className={'loaderContainer'}>
            <PulseLoader color={'#433e3f'} size={8} margin={'7px'}/>
        </div>;
    }
}