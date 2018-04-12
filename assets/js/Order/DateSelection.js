import React from 'react';
import {render} from 'react-dom';
import DayPicker from 'react-day-picker';
import TimeSelection from './TimeSelection';
import 'moment/locale/lt';
import MomentLocaleUtils from 'react-day-picker/moment';

export default class DateSelection extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            showTimeSelection: false
        };

        this.onDateSelection = this.onDateSelection.bind(this);
        this.onTimeSelectionExit = this.onTimeSelectionExit.bind(this);
    }

    onDateSelection(date)
    {
        this.setState({
            showTimeSelection: true
        });
    }

    onTimeSelectionExit()
    {
        this.setState({
            showTimeSelection: false
        });
    }

    render(){
        return (
            <div className={'DatePickerContainer'}>
                <DayPicker showOutsideDays localeUtils={MomentLocaleUtils} locale={'lt'} onDayClick={this.onDateSelection}/>
                {this.state.showTimeSelection === true? <TimeSelection onExit={this.onTimeSelectionExit}/>: null}
            </div>
        );
    }
}