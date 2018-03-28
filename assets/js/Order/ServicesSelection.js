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
            searchValue: ''
        };
        this.selectService = this.selectService.bind(this);
        this.handleSearchBox = this.handleSearchBox.bind(this);
    }

    componentDidMount()
    {
        this.fetchServiceList(this.state.searchValue);
    }

    fetchServiceList(pattern)
    {
        const slash = pattern.length > 0? '/': '';
        fetch("/services/search" + slash + pattern)
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
        return <div className={'serviceSelectionDialog'}>
                    <h1>Pasirinkite paslaugas</h1>
                    <div className={'serviceSelectionContainer'}>
                        <div className={'serviceOptionsContainer'}>
                            <input
                                className={'searchBox'}
                                type='text'
                                value={this.state.searchValue}
                                onChange={this.handleSearchBox}
                            />
                            <div className={'scrollable-vertical searchableServices'}>
                                {this.state.services}
                            </div>
                        </div>
                        <div className={'serviceOptionsContainer scrollable-vertical align-right'}>
                            {this.state.selectedServicesList}
                        </div>
                    </div>
                </div>;
    }
}