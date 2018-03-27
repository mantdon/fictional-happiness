import React from 'react';
import {render} from 'react-dom';
import ServiceOption from './ServiceOption';

export default class ServicesSelection extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            services: [],
            selectedServices: []};
        this.selectService = this.selectService.bind(this);
        this.fetchServiceList();
    }

    fetchServiceList()
    {
        fetch("/services/search")
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        services: this.formServiceList(result)
                    });
                },
                (error) => {
                    this.setState({
                        error
                    });
                }
            )
    }

    selectService()
    {

    }

    formServiceList(services)
    {
        if(services.length > 0)
            return services.map((service, i) => <ServiceOption onClick={this.selectService} service={service}
                                                                key={i}/>);
    }

    handleSearchBox()
    {
        this.setState({searchValue: event.target.value});
    }

    render(){
        return <div>
                    <h1>Pasirinkite paslaugas</h1>
                        <div className={'optionsContainer'}>
                            <input
                                className={'searchBox'}
                                type='text'
                                value={this.state.searchValue}
                                onChange={this.handleSearchBox}
                            />
                            {this.state.services}
                        </div>
                </div>;
    }
}