import React from 'react';
import {render} from 'react-dom';
import TimeOption from './TimeOption';
import moment from 'moment';

export default class TimeSelection extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            timesList: []
        };
        this.handleTimeSelection = this.handleTimeSelection.bind(this);
    }

    componentWillMount()
    {
        this.fetchTimes();
    }

    fetchTimes()
    {
        fetch("/order/fetch_times", {
            method: "POST",
           /* body: JSON.stringify({
                date: date
            })*/
        })
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        timesList: this.formTimesList(result)
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
                {this.state.timesList}
            </div>
        );
    }
}