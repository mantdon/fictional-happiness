import React from 'react';
import {render} from 'react-dom';
import DayPicker from 'react-day-picker';
import TimeSelection from './TimeSelection';
import 'moment/locale/lt';
import MomentLocaleUtils from 'react-day-picker/moment';
import moment from 'moment';
import Loader from "./Loader";

export default class DateSelection extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            showTimeSelection: false,
            dateSelected: null,
            unavailableDays: [],
            isLoaded: false
        };

        this.onDateSelection = this.onDateSelection.bind(this);
        this.onTimeSelectionExit = this.onTimeSelectionExit.bind(this);
    }

    componentDidMount()
    {
        this.updateAvailableDays();
    }

    updateAvailableDays()
    {
        fetch("/order/fetch_unavailable_days", {
            credentials: "same-origin",
            method: "POST"
        })
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        unavailableDays: this.datesToObjects(result),
                        isLoaded: true
                    })
                },
                (error) => {
                    this.setState({
                        error
                    });
                }
            )
    }

    datesToObjects(dates)
    {
        return dates.map((date) => new Date(date))
    }

    onDateSelection(date, modifiers = {})
    {
        if(modifiers.disabled)
            return;

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
        let disabled = [{ before: new Date()}, { daysOfWeek: [0]}];
        disabled = disabled.concat(this.state.unavailableDays);

        const content = this.state.showTimeSelection === true
            ? <TimeSelection onTimeSelection={this.props.onDateSelection} date={this.state.dateSelected} onExit={this.onTimeSelectionExit}/>
            : <DayPicker className={'calendarContainer'} showOutsideDays localeUtils={MomentLocaleUtils} locale={'lt'} onDayClick={this.onDateSelection}
                         disabledDays={disabled} fromMonth={new Date()}/>;

        return (
            <div className={'datePickerContainer row'}>
                <div className={'d-flex justify-content-center w-100'}>
                {this.state.isLoaded
                    ? content
                    : <Loader/>
                }
                </div>
            </div>
        );
    }
}