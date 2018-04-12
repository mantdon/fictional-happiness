import React from 'react';
import {render} from 'react-dom';
import Datetime from 'react-datetime';
import TimeSelection from './TimeSelection';
import 'moment/locale/lt';

export default class DateSelection extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            showTimeSelection: false
        };

        this.onDateSelection = this.onDateSelection.bind(this);
    }

    onDateSelection(date)
    {
        this.setState({
            showTimeSelection: true
        });
    }

    render(){
        return (
            <div className={'DatePickerContainer'}>
                <Datetime input={false} timeFormat={false} onChange={this.onDateSelection}/>
                {this.state.showTimeSelection === true? <TimeSelection/>: null}
            </div>
        );
    }
}