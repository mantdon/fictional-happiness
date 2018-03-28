import React from 'react';
import {render} from 'react-dom';
import ServiceOption from './ServiceOption';

export default class ServicesSelection extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            services: [],
            servicesList: [],
            selectedServices: [],
            selectedServicesList: [],
            searchValue: ''
        };
        this.selectService = this.selectService.bind(this);
        this.handleSearchBox = this.handleSearchBox.bind(this);
        this.removeSelectedService = this.removeSelectedService.bind(this);
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
                        services: result,
                        servicesList: this.formAvailableServiceList(result, this.selectService)
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

        this.setSelectedServices(newList);
    }

    removeSelectedService(service)
    {
        const newList = this.state.selectedServices.filter((srv) => {
            return srv.id !== service.id;
        });

        this.setSelectedServices(newList);
    }

    setSelectedServices(newList)
    {
        this.setState({
                selectedServices: newList,
                selectedServicesList: this.formServiceList(newList, this.removeSelectedService)}, () => {
                this.updateServiceList();
            }
        );
    }

    updateServiceList()
    {
        this.setState({
            servicesList: this.formAvailableServiceList(this.state.services, this.selectService)
        });
    }

    formAvailableServiceList(services, onClick)
    {
        if(services.length > 0)
            return services.map((service, i) => this.formAvailableService(service, i, onClick));
    }

    formAvailableService(service, i, onClick)
    {
        if(!this.isSelected(service))
            return this.formService(service, i, onClick);
    }

    formServiceList(services, onClick)
    {
        if(services.length > 0)
            return services.map((service, i) => this.formService(service, i, onClick));
    }

    formService(service, i, onClick)
    {
        return <ServiceOption onClick={onClick} service={service}
                              key={i}/>;
    }

    isSelected(service)
    {
        return this.state.selectedServices.some((srv) => {
            if(srv.id === service.id)
                return true;
        });
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
                                {this.state.servicesList}
                            </div>
                        </div>
                        <div className={'serviceOptionsContainer scrollable-vertical align-right'}>
                            {this.state.selectedServicesList}
                        </div>
                    </div>
                </div>;
    }
}