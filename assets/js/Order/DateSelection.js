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
            showTimeSelection: false,
            dateSelected: null
        };

        this.onDateSelection = this.onDateSelection.bind(this);
        this.onTimeSelectionExit = this.onTimeSelectionExit.bind(this);
    }

    onDateSelection(date)
    {
        this.setState({
            showTimeSelection: true,
            dateSelected: date
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
            <div className={'datePickerContainer'}>
                {this.state.showTimeSelection === true
                    ? <TimeSelection onTimeSelection={this.props.onDateSelection} date={this.state.dateSelected} onExit={this.onTimeSelectionExit}/>
                    : <DayPicker className={'timeSelectionElementSize'} showOutsideDays localeUtils={MomentLocaleUtils} locale={'lt'} onDayClick={this.onDateSelection}/>}
            </div>
        );
    }
}