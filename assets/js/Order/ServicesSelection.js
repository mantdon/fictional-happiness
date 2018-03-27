import React from 'react';
import {render} from 'react-dom';
import ServiceOption from './ServiceOption';

export default class ServicesSelection extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            services: [],
            selectedServices: [],
            selectedServicesList: [],
            searchValue: ''};
        this.selectService = this.selectService.bind(this);
        this.handleSearchBox = this.handleSearchBox.bind(this);
    }

    fetchServiceList(pattern)
    {
        fetch("/services/search/" + pattern)
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        services: this.formServiceList(result, this.selectService)
                    });
                },
                (error) => {
                    this.setState({
                        error
                    });
                }
            )
    }

    selectService(service)
    {
        const newList = this.state.selectedServices.slice();
        newList.push(service);
        this.setState({
            selectedServices: newList,
            selectedServicesList: this.formServiceList(newList, null)}
        );
    }

    formServiceList(services, onClick)
    {
        console.log(services);
        if(services.length > 0)
            return services.map((service, i) => <ServiceOption onClick={onClick} service={service}
                                                                key={i}/>);
    }

    handleSearchBox(event)
    {
        this.setState({searchValue: event.target.value}, () => {
            this.fetchServiceList(this.state.searchValue);
        });
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
                        <div className={'optionsContainer'}>
                            {this.state.selectedServicesList}
                        </div>
                </div>;
    }
}