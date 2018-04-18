import React from 'react';
import {render} from 'react-dom';
import TimeOption from './TimeOption';
import moment from 'moment';
import Loader from './Loader';

export default class TimeSelection extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            timesList: [],
            isLoaded: false
        };
        this.handleTimeSelection = this.handleTimeSelection.bind(this);
    }

    componentWillMount()
    {
        this.fetchTimes(this.props.date);
    }

    fetchTimes(date)
    {
        fetch("/order/fetch_times", {
            credentials: "same-origin",
            method: "POST",
            body: JSON.stringify({
                date: date
            })
        })
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        timesList: this.formTimesList(result),
                        isLoaded: true
                    });
                },
                (error) => {
                    this.setState({
                        error
                    });
                }
            )
    }

    formTimesList(times)
    {
        return times.map((time, i) => this.formTimeElement(time, i));
    }

    formTimeElement(time, i)
    {
        return <TimeOption time={time} onClick={this.handleTimeSelection}
                              key={i}/>;
    }

    handleTimeSelection(time)
    {
        this.props.onTimeSelection(moment(moment(this.props.date).format('YYYY-MM-DD') + ' ' + time));
    }

    render(){
        return (
            <div className={'timeSelectionContainer'}>
                <span>{moment(this.props.date).format('YYYY-MM-DD')}</span>
                <span onClick={this.props.onExit} className={'close'} aria-hidden="true">&times;</span>
                {this.state.isLoaded
                    ? this.state.timesList
                    : <Loader/>
                }
            </div>
        );
    }
}